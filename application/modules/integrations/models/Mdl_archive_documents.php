<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Registry of documents held by the archive layer.
 *
 * This model records the document identity and provenance without claiming
 * that the underlying filesystem is immutable. A future storage adapter will
 * provide WORM or external SAE guarantees.
 */
class Mdl_Archive_documents extends CI_Model
{
    private const TABLE = 'ip_archive_documents';

    public function register(array $data): int
    {
        $sourceModule = trim((string) ($data['source_module'] ?? ''));
        $sourceReference = trim((string) ($data['source_reference'] ?? ''));
        $storagePath = trim((string) ($data['storage_path'] ?? ''));
        $sha256 = strtolower(trim((string) ($data['sha256'] ?? '')));

        if ($sourceModule === '' || $sourceReference === '' || $storagePath === '') {
            throw new InvalidArgumentException('Archive document provenance is required.');
        }
        if (preg_match('/^[a-f0-9]{64}$/', $sha256) !== 1) {
            throw new InvalidArgumentException('Archive document SHA-256 is invalid.');
        }

        $existing = $this->get_by_source($sourceModule, $sourceReference);
        if ($existing !== []) {
            if ( ! hash_equals((string) $existing['sha256'], $sha256)) {
                throw new RuntimeException('Archive document provenance points to a different file.');
            }

            return (int) $existing['archive_document_id'];
        }

        $now = date('Y-m-d H:i:s');
        $this->db->insert(self::TABLE, [
            'document_type' => trim((string) ($data['document_type'] ?? 'document')),
            'source_module' => $sourceModule,
            'source_reference' => $sourceReference,
            'storage_path' => $storagePath,
            'file_name' => trim((string) ($data['file_name'] ?? basename($storagePath))),
            'mime_type' => trim((string) ($data['mime_type'] ?? 'application/octet-stream')),
            'file_size' => max(0, (int) ($data['file_size'] ?? 0)),
            'sha256' => $sha256,
            'integrity_status' => $this->nullableString($data['integrity_status'] ?? null),
            'integrity_verified_at' => $this->nullableDateTime($data['integrity_verified_at'] ?? null),
            'integrity_error' => $this->nullableString($data['integrity_error'] ?? null),
            'document_profile' => $this->nullableString($data['document_profile'] ?? null),
            'validation_status' => $this->nullableString($data['validation_status'] ?? null),
            'validation_error' => $this->nullableString($data['validation_error'] ?? null),
            'received_at' => $this->nullableDateTime($data['received_at'] ?? null),
            'archived_at' => $this->nullableDateTime($data['archived_at'] ?? null) ?? $now,
            'retention_until' => $this->nullableDate($data['retention_until'] ?? null),
            'legal_hold' => (int) ($data['legal_hold'] ?? 0) === 1 ? 1 : 0,
            'sealed_at' => $this->nullableDateTime($data['sealed_at'] ?? null),
            'created_by' => isset($data['created_by']) ? (int) $data['created_by'] : null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $archiveDocumentId = (int) $this->db->insert_id();

        $this->load->model('integrations/Mdl_archive_audit_events');
        $this->Mdl_archive_audit_events->append(
            $archiveDocumentId,
            'registered',
            isset($data['created_by']) ? (int) $data['created_by'] : null,
            isset($data['created_by']) ? 'user' : 'system',
            [
                'document_type' => trim((string) ($data['document_type'] ?? 'document')),
                'source_module' => $sourceModule,
                'source_reference' => $sourceReference,
                'sha256' => $sha256,
                'validation_status' => $this->nullableString($data['validation_status'] ?? null),
            ],
            $now
        );

        return $archiveDocumentId;
    }

    public function get_by_id(int $archiveDocumentId): array
    {
        return $this->db
            ->where('archive_document_id', $archiveDocumentId)
            ->get(self::TABLE)
            ->row_array() ?: [];
    }

    public function get_by_source(string $sourceModule, string $sourceReference): array
    {
        return $this->db
            ->where('source_module', $sourceModule)
            ->where('source_reference', $sourceReference)
            ->get(self::TABLE)
            ->row_array() ?: [];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get_retention_candidates(string $beforeDate, int $limit = 100): array
    {
        $beforeDate = $this->requiredDate($beforeDate);

        return $this->db
            ->where('retention_until IS NOT NULL')
            ->where('retention_until <=', $beforeDate)
            ->where('legal_hold', 0)
            ->where('sealed_at IS NOT NULL')
            ->order_by('retention_until', 'ASC')
            ->limit(max(1, min(1000, $limit)))
            ->get(self::TABLE)
            ->result_array();
    }

    public function set_retention_until(
        int $archiveDocumentId,
        ?string $retentionUntil,
        ?int $actorUserId = null,
        string $actorType = 'system'
    ): bool {
        $document = $this->requireDocument($archiveDocumentId);
        $normalisedDate = $retentionUntil === null || trim($retentionUntil) === ''
            ? null
            : $this->requiredDate($retentionUntil);

        if (($document['retention_until'] ?: null) === $normalisedDate) {
            return false;
        }

        $this->db->where('archive_document_id', $archiveDocumentId)->update(self::TABLE, [
            'retention_until' => $normalisedDate,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit(
            $archiveDocumentId,
            'retention_updated',
            $actorUserId,
            $actorType,
            [
                'previous_retention_until' => $document['retention_until'] ?: null,
                'retention_until' => $normalisedDate,
            ]
        );

        return true;
    }

    public function place_legal_hold(
        int $archiveDocumentId,
        string $reason,
        ?int $actorUserId = null,
        string $actorType = 'system'
    ): bool {
        $document = $this->requireDocument($archiveDocumentId);
        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 1000) {
            throw new InvalidArgumentException('A legal hold reason is required.');
        }
        if ((int) $document['legal_hold'] === 1) {
            return false;
        }

        $this->db->where('archive_document_id', $archiveDocumentId)->update(self::TABLE, [
            'legal_hold' => 1,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit($archiveDocumentId, 'legal_hold_placed', $actorUserId, $actorType, [
            'reason' => $reason,
        ]);

        return true;
    }

    public function release_legal_hold(
        int $archiveDocumentId,
        string $reason,
        ?int $actorUserId = null,
        string $actorType = 'system'
    ): bool {
        $document = $this->requireDocument($archiveDocumentId);
        $reason = trim($reason);
        if ($reason === '' || mb_strlen($reason) > 1000) {
            throw new InvalidArgumentException('A legal hold release reason is required.');
        }
        if ((int) $document['legal_hold'] !== 1) {
            return false;
        }

        $this->db->where('archive_document_id', $archiveDocumentId)->update(self::TABLE, [
            'legal_hold' => 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->audit($archiveDocumentId, 'legal_hold_released', $actorUserId, $actorType, [
            'reason' => $reason,
        ]);

        return true;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get_legal_holds(int $limit = 100): array
    {
        return $this->db
            ->where('legal_hold', 1)
            ->order_by('updated_at', 'DESC')
            ->limit(max(1, min(1000, $limit)))
            ->get(self::TABLE)
            ->result_array();
    }

    /**
     * Verify the stored document against its registered SHA-256 digest.
     *
     * @return array{valid: bool, expected_sha256: string, actual_sha256: string|null}
     */
    public function verify_integrity(
        int $archiveDocumentId,
        ?int $actorUserId = null,
        string $actorType = 'system'
    ): array {
        $document = $this->requireDocument($archiveDocumentId);
        $path = $this->archivePath((string) $document['storage_path']);
        $actualSha256 = hash_file('sha256', $path);
        $expectedSha256 = strtolower((string) $document['sha256']);
        $valid = is_string($actualSha256)
            && preg_match('/^[a-f0-9]{64}$/', $actualSha256) === 1
            && hash_equals($expectedSha256, $actualSha256);
        $now = date('Y-m-d H:i:s');

        $this->db->where('archive_document_id', $archiveDocumentId)->update(self::TABLE, [
            'integrity_status' => $valid ? 'verified' : 'failed',
            'integrity_verified_at' => $now,
            'integrity_error' => $valid ? null : 'The stored document does not match its registered SHA-256 digest.',
            'updated_at' => $now,
        ]);

        $this->audit(
            $archiveDocumentId,
            $valid ? 'integrity_verified' : 'integrity_failed',
            $actorUserId,
            $actorType,
            [
                'expected_sha256' => $expectedSha256,
                'actual_sha256' => is_string($actualSha256) ? $actualSha256 : null,
                'valid' => $valid,
            ]
        );

        return [
            'valid' => $valid,
            'expected_sha256' => $expectedSha256,
            'actual_sha256' => is_string($actualSha256) ? $actualSha256 : null,
        ];
    }

    public function seal(
        int $archiveDocumentId,
        ?int $actorUserId = null,
        string $actorType = 'system'
    ): bool {
        $document = $this->requireDocument($archiveDocumentId);
        if (($document['sealed_at'] ?? null) !== null && $document['sealed_at'] !== '') {
            return false;
        }

        $verification = $this->verify_integrity($archiveDocumentId, $actorUserId, $actorType);
        if ($verification['valid'] !== true) {
            throw new RuntimeException('The archive document cannot be sealed because its integrity check failed.');
        }

        $sealedAt = date('Y-m-d H:i:s');
        $this->db->where('archive_document_id', $archiveDocumentId)->update(self::TABLE, [
            'sealed_at' => $sealedAt,
            'updated_at' => $sealedAt,
        ]);
        $this->audit($archiveDocumentId, 'sealed', $actorUserId, $actorType, [
            'sealed_at' => $sealedAt,
            'sha256' => $verification['actual_sha256'],
        ]);

        return true;
    }

    /**
     * Export a sealed document with its metadata and audit chain.
     *
     * @return string Relative path below UPLOADS_ARCHIVE_FOLDER.
     */
    public function export_compliant(
        int $archiveDocumentId,
        ?int $actorUserId = null,
        string $actorType = 'system'
    ): string {
        if ( ! class_exists('ZipArchive')) {
            throw new RuntimeException('The PHP ZIP extension is required for archive export.');
        }

        $document = $this->requireDocument($archiveDocumentId);
        if (($document['sealed_at'] ?? null) === null || $document['sealed_at'] === '') {
            throw new RuntimeException('Only sealed archive documents can be exported.');
        }

        $verification = $this->verify_integrity($archiveDocumentId, $actorUserId, $actorType);
        if ($verification['valid'] !== true) {
            throw new RuntimeException('The archive document cannot be exported because its integrity check failed.');
        }

        $path = $this->archivePath((string) $document['storage_path']);
        $exportDirectory = rtrim(UPLOADS_ARCHIVE_FOLDER, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . 'exports';
        if ( ! is_dir($exportDirectory) && ! mkdir($exportDirectory, 0750, true) && ! is_dir($exportDirectory)) {
            throw new RuntimeException('The archive export directory could not be created.');
        }
        if ( ! is_writable($exportDirectory) || ! validate_file_in_directory($exportDirectory, UPLOADS_ARCHIVE_FOLDER)) {
            throw new RuntimeException('The archive export directory is not writable or is invalid.');
        }

        $exportName = 'archive-' . $archiveDocumentId . '-' . substr($verification['actual_sha256'], 0, 16) . '.zip';
        $finalPath = $exportDirectory . DIRECTORY_SEPARATOR . $exportName;
        if (file_exists($finalPath)) {
            throw new RuntimeException('The archive export already exists and cannot be overwritten.');
        }

        $temporaryPath = tempnam($exportDirectory, '.archive-export-');
        if ($temporaryPath === false || ! validate_file_in_directory($temporaryPath, UPLOADS_ARCHIVE_FOLDER)) {
            throw new RuntimeException('A secure temporary archive export could not be created.');
        }

        $generatedAt = date('Y-m-d H:i:s');
        $this->audit($archiveDocumentId, 'export_started', $actorUserId, $actorType, [
            'export_name' => $exportName,
            'generated_at' => $generatedAt,
        ]);

        try {
            $this->load->model('integrations/Mdl_archive_audit_events');
            $events = $this->Mdl_archive_audit_events->get_by_document($archiveDocumentId);
            if ( ! $this->Mdl_archive_audit_events->verify_chain($archiveDocumentId)) {
                throw new RuntimeException('The archive audit chain could not be verified.');
            }

            $documentEntryName = $this->exportDocumentName($document, $archiveDocumentId);
            $manifest = [
                'format' => 'InvoicePlane archive package',
                'format_version' => '1.0',
                'generated_at' => $generatedAt,
                'document' => [
                    'archive_document_id' => $archiveDocumentId,
                    'document_type' => $document['document_type'],
                    'source_module' => $document['source_module'],
                    'source_reference' => $document['source_reference'],
                    'file_name' => $document['file_name'],
                    'mime_type' => $document['mime_type'],
                    'file_size' => (int) $document['file_size'],
                    'sha256' => $verification['actual_sha256'],
                    'document_profile' => $document['document_profile'],
                    'validation_status' => $document['validation_status'],
                    'received_at' => $document['received_at'],
                    'archived_at' => $document['archived_at'],
                    'retention_until' => $document['retention_until'],
                    'legal_hold' => (int) $document['legal_hold'] === 1,
                    'sealed_at' => $document['sealed_at'],
                    'integrity_verified_at' => $document['integrity_verified_at'],
                    'package_entry' => 'document/' . $documentEntryName,
                ],
                'audit_chain_verified' => true,
                'audit_event_count' => count($events),
            ];

            $zip = new ZipArchive();
            if ($zip->open($temporaryPath, ZipArchive::OVERWRITE) !== true) {
                throw new RuntimeException('The archive export could not be opened.');
            }
            if ( ! $zip->addFile($path, 'document/' . $documentEntryName)) {
                $zip->close();
                throw new RuntimeException('The archived document could not be added to the export.');
            }
            $zip->addFromString(
                'manifest.json',
                json_encode($manifest, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n"
            );
            $zip->addFromString(
                'audit-events.json',
                json_encode($events, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n"
            );
            if ( ! $zip->close() || ! rename($temporaryPath, $finalPath)) {
                throw new RuntimeException('The archive export could not be finalized.');
            }
        } catch (Throwable $exception) {
            if (is_file($temporaryPath)) {
                unlink($temporaryPath);
            }

            throw $exception;
        }

        $this->audit($archiveDocumentId, 'exported', $actorUserId, $actorType, [
            'export_name' => $exportName,
            'generated_at' => $generatedAt,
            'sha256' => hash_file('sha256', $finalPath),
        ]);

        return 'exports/' . $exportName;
    }

    private function requireDocument(int $archiveDocumentId): array
    {
        if ($archiveDocumentId < 1) {
            throw new InvalidArgumentException('Archive document ID is invalid.');
        }

        $document = $this->get_by_id($archiveDocumentId);
        if ($document === []) {
            throw new RuntimeException('Archive document was not found.');
        }

        return $document;
    }

    private function audit(
        int $archiveDocumentId,
        string $eventType,
        ?int $actorUserId,
        string $actorType,
        array $payload
    ): void {
        $this->load->model('integrations/Mdl_archive_audit_events');
        $this->Mdl_archive_audit_events->append(
            $archiveDocumentId,
            $eventType,
            $actorUserId,
            $actorType,
            $payload
        );
    }

    private function archivePath(string $storagePath): string
    {
        $this->load->helper('file_security');
        if ( ! validate_safe_filename($storagePath)['valid']) {
            throw new RuntimeException('The archive document path is invalid.');
        }

        $path = rtrim(UPLOADS_ARCHIVE_FOLDER, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR
            . str_replace('/', DIRECTORY_SEPARATOR, $storagePath);
        if ( ! is_file($path) || ! validate_file_in_directory($path, UPLOADS_ARCHIVE_FOLDER)) {
            throw new RuntimeException('The archived document is unavailable.');
        }

        return $path;
    }

    private function exportDocumentName(array $document, int $archiveDocumentId): string
    {
        $name = basename((string) ($document['file_name'] ?? ''));
        $name = preg_replace('/[^A-Za-z0-9._-]/', '_', $name) ?? '';
        $name = trim($name, '._-');

        return $name === '' ? 'document-' . $archiveDocumentId . '.bin' : $name;
    }

    private function nullableString(mixed $value): ?string
    {
        if ( ! is_scalar($value)) {
            return null;
        }

        $value = trim((string) $value);

        return $value === '' ? null : mb_substr($value, 0, 1000);
    }

    private function nullableDateTime(mixed $value): ?string
    {
        if ( ! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', trim((string) $value));

        return $date === false ? null : $date->format('Y-m-d H:i:s');
    }

    private function nullableDate(mixed $value): ?string
    {
        if ( ! is_scalar($value) || trim((string) $value) === '') {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', trim((string) $value));

        return $date === false ? null : $date->format('Y-m-d');
    }

    private function requiredDate(string $value): string
    {
        $date = DateTimeImmutable::createFromFormat('!Y-m-d', trim($value));
        $errors = DateTimeImmutable::getLastErrors();
        if ($date === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            throw new InvalidArgumentException('Archive retention date is invalid.');
        }

        return $date->format('Y-m-d');
    }
}
