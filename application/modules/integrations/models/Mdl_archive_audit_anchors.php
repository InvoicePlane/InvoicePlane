<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/integrations/libraries/S3ObjectLockArchiveConnector.php';

/**
 * Immutable database records for audit-chain anchors stored outside the DB.
 */
class Mdl_Archive_audit_anchors extends CI_Model
{
    private const TABLE = 'ip_archive_audit_anchors';

    /**
     * @return array<string, mixed>
     */
    public function anchor_on_s3_worm(
        int $archiveDocumentId,
        S3ObjectLockArchiveConnector $connector,
        ?int $actorUserId = null,
        string $actorType = 'system'
    ): array {
        $this->load->model('integrations/Mdl_archive_documents');
        $document = $this->Mdl_archive_documents->get_by_id($archiveDocumentId);
        if ($document === []) {
            throw new RuntimeException('Archive document was not found.');
        }
        $retentionUntil = trim((string) ($document['retention_until'] ?? ''));
        if ($retentionUntil === '') {
            throw new RuntimeException('A retention date is required before anchoring an audit chain.');
        }

        $this->load->model('integrations/Mdl_archive_audit_events');
        $events = $this->Mdl_archive_audit_events->get_by_document($archiveDocumentId);
        if ($events === [] || ! $this->Mdl_archive_audit_events->verify_chain($archiveDocumentId)) {
            throw new RuntimeException('The archive audit chain could not be verified.');
        }
        $lastEvent = $events[count($events) - 1];
        $eventHashes = array_map(static fn (array $event): string => (string) $event['event_hash'], $events);
        $chainSha256 = hash('sha256', implode("\n", $eventHashes));
        $payload = [
            'format' => 'InvoicePlane archive audit anchor',
            'format_version' => '1.0',
            'archive_document_id' => $archiveDocumentId,
            'sequence_number' => (int) $lastEvent['sequence_number'],
            'last_event_hash' => (string) $lastEvent['event_hash'],
            'chain_sha256' => $chainSha256,
            'event_count' => count($events),
            'created_at' => date('Y-m-d H:i:s'),
        ];
        $payloadJson = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . "\n";
        $payloadPath = $this->writablePath(
            'audit-anchors/' . $archiveDocumentId . '-' . $lastEvent['sequence_number'] . '-' . $chainSha256 . '.json'
        );
        if (file_exists($payloadPath)) {
            throw new RuntimeException('This audit-chain anchor already exists.');
        }
        if (file_put_contents($payloadPath, $payloadJson, LOCK_EX) !== strlen($payloadJson)) {
            if (is_file($payloadPath)) {
                unlink($payloadPath);
            }

            throw new RuntimeException('The audit-chain anchor could not be written.');
        }

        $retentionDate = DateTimeImmutable::createFromFormat(
            '!Y-m-d H:i:s',
            $retentionUntil . ' 23:59:59',
            new DateTimeZone('UTC')
        );
        if ($retentionDate === false) {
            throw new RuntimeException('The archive retention date is invalid.');
        }
        $objectKey = 'invoiceplane/audit-anchors/' . $archiveDocumentId . '/'
            . $lastEvent['sequence_number'] . '-' . $chainSha256 . '.json';
        $external = $connector->store(
            $payloadPath,
            $objectKey,
            $retentionDate,
            (int) $document['legal_hold'] === 1,
            ['archive-document-id' => (string) $archiveDocumentId, 'chain-sha256' => $chainSha256],
            'application/json'
        );

        $this->db->insert(self::TABLE, [
            'archive_document_id' => $archiveDocumentId,
            'sequence_number' => (int) $lastEvent['sequence_number'],
            'last_event_hash' => $lastEvent['event_hash'],
            'chain_sha256' => $chainSha256,
            'payload_json' => $payloadJson,
            'external_storage_provider' => 's3-object-lock',
            'external_storage_key' => $external['key'],
            'external_storage_version_id' => $external['version_id'],
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return [
            'archive_audit_anchor_id' => (int) $this->db->insert_id(),
            'archive_document_id' => $archiveDocumentId,
            'sequence_number' => (int) $lastEvent['sequence_number'],
            'chain_sha256' => $chainSha256,
            'external_storage_key' => $external['key'],
            'external_storage_version_id' => $external['version_id'],
        ];
    }

    public function verify_local_anchor(int $anchorId): bool
    {
        $anchor = $this->db
            ->where('archive_audit_anchor_id', $anchorId)
            ->get(self::TABLE)
            ->row_array();
        if ($anchor === null || $anchor === []) {
            return false;
        }
        $events = $this->db
            ->where('archive_document_id', (int) $anchor['archive_document_id'])
            ->where('sequence_number <=', (int) $anchor['sequence_number'])
            ->order_by('sequence_number', 'ASC')
            ->get('ip_archive_audit_events')
            ->result_array();
        if ($events === [] || (string) $events[count($events) - 1]['event_hash'] !== (string) $anchor['last_event_hash']) {
            return false;
        }
        $hashes = array_map(static fn (array $event): string => (string) $event['event_hash'], $events);

        return hash_equals((string) $anchor['chain_sha256'], hash('sha256', implode("\n", $hashes)));
    }

    private function writablePath(string $relativePath): string
    {
        require_once APPPATH . 'helpers/file_security_helper.php';
        if ( ! validate_safe_filename($relativePath)['valid']) {
            throw new RuntimeException('The audit anchor path is invalid.');
        }
        $path = rtrim(UPLOADS_ARCHIVE_FOLDER, DIRECTORY_SEPARATOR)
            . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $directory = dirname($path);
        if ( ! is_dir($directory) && ! mkdir($directory, 0750, true) && ! is_dir($directory)) {
            throw new RuntimeException('The audit anchor directory could not be created.');
        }
        if ( ! validate_file_in_directory($directory, UPLOADS_ARCHIVE_FOLDER)) {
            throw new RuntimeException('The audit anchor destination is invalid.');
        }

        return $path;
    }
}
