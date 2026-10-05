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
        $this->mdl_archive_audit_events->append(
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
}
