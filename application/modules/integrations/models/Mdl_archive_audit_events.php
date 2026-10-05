<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/integrations/libraries/IntegrationPayloadSanitizer.php';

/**
 * Append-only, hash-chained audit events for archived documents.
 */
class Mdl_Archive_audit_events extends CI_Model
{
    private const TABLE = 'ip_archive_audit_events';

    public function append(
        int $archiveDocumentId,
        string $eventType,
        ?int $actorUserId = null,
        string $actorType = 'system',
        array $payload = [],
        ?string $eventAt = null
    ): int {
        if ($archiveDocumentId < 1) {
            throw new InvalidArgumentException('Archive document ID is invalid.');
        }
        if (preg_match('/^[a-z][a-z0-9_.-]{0,49}$/', $eventType) !== 1) {
            throw new InvalidArgumentException('Archive audit event type is invalid.');
        }
        if (preg_match('/^[a-z][a-z0-9_.-]{0,19}$/', $actorType) !== 1) {
            throw new InvalidArgumentException('Archive audit actor type is invalid.');
        }

        $normalisedEventAt = $this->normaliseDateTime($eventAt);
        $payloadJson = IntegrationPayloadSanitizer::json($payload);

        $this->db->trans_begin();

        $lastEvent = $this->db->query(
            'SELECT sequence_number, event_hash FROM ' . self::TABLE
            . ' WHERE archive_document_id = ? ORDER BY sequence_number DESC LIMIT 1 FOR UPDATE',
            [$archiveDocumentId]
        )->row_array();

        $sequenceNumber = $lastEvent === null ? 1 : ((int) $lastEvent['sequence_number'] + 1);
        $previousEventHash = $lastEvent['event_hash'] ?? null;
        $eventHash = $this->hashEvent(
            $archiveDocumentId,
            $sequenceNumber,
            $eventType,
            $actorType,
            $actorUserId,
            $normalisedEventAt,
            $payloadJson,
            $previousEventHash
        );

        $this->db->insert(self::TABLE, [
            'archive_document_id' => $archiveDocumentId,
            'sequence_number' => $sequenceNumber,
            'event_type' => $eventType,
            'actor_type' => $actorType,
            'actor_user_id' => $actorUserId,
            'event_at' => $normalisedEventAt,
            'payload_json' => $payloadJson,
            'previous_event_hash' => $previousEventHash,
            'event_hash' => $eventHash,
        ]);

        if ($this->db->trans_status() === false) {
            $this->db->trans_rollback();
            throw new RuntimeException('Archive audit event could not be stored.');
        }

        $this->db->trans_commit();

        return (int) $this->db->insert_id();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function get_by_document(int $archiveDocumentId): array
    {
        return $this->db
            ->where('archive_document_id', $archiveDocumentId)
            ->order_by('sequence_number', 'ASC')
            ->get(self::TABLE)
            ->result_array();
    }

    public function verify_chain(int $archiveDocumentId): bool
    {
        $previousHash = null;

        foreach ($this->get_by_document($archiveDocumentId) as $event) {
            if (($event['previous_event_hash'] ?: null) !== $previousHash) {
                return false;
            }

            $expectedHash = $this->hashEvent(
                $archiveDocumentId,
                (int) $event['sequence_number'],
                (string) $event['event_type'],
                (string) $event['actor_type'],
                $event['actor_user_id'] === null ? null : (int) $event['actor_user_id'],
                (string) $event['event_at'],
                $event['payload_json'] === null ? null : (string) $event['payload_json'],
                $previousHash
            );

            if ( ! hash_equals((string) $event['event_hash'], $expectedHash)) {
                return false;
            }

            $previousHash = (string) $event['event_hash'];
        }

        return true;
    }

    private function hashEvent(
        int $archiveDocumentId,
        int $sequenceNumber,
        string $eventType,
        string $actorType,
        ?int $actorUserId,
        string $eventAt,
        ?string $payloadJson,
        ?string $previousEventHash
    ): string {
        return hash('sha256', implode("\n", [
            $archiveDocumentId,
            $sequenceNumber,
            $eventType,
            $actorType,
            $actorUserId ?? '',
            $eventAt,
            $payloadJson ?? '',
            $previousEventHash ?? '',
        ]));
    }

    private function normaliseDateTime(?string $eventAt): string
    {
        if ($eventAt === null || trim($eventAt) === '') {
            return date('Y-m-d H:i:s');
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', trim($eventAt));
        if ($date === false) {
            throw new InvalidArgumentException('Archive audit event date is invalid.');
        }

        return $date->format('Y-m-d H:i:s');
    }
}
