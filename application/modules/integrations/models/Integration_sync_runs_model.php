<?php

defined('BASEPATH') || exit('No direct script access allowed');

class Integration_sync_runs_model extends CI_Model
{
    private const TABLE = 'ip_integration_sync_runs';

    public function start(
        int $merchantClientId,
        string $correlationId,
        string $trigger,
        string $scope
    ): int {
        $this->db->insert(self::TABLE, [
            'merchant_client_id' => $merchantClientId,
            'correlation_id'     => $correlationId,
            'trigger_type'       => $trigger,
            'sync_scope'         => $scope,
            'status'             => 'running',
            'started_at'         => date('Y-m-d H:i:s'),
        ]);

        return (int) $this->db->insert_id();
    }

    public function fail_stale(int $merchantClientId, int $staleAfterSeconds = 3600): int
    {
        $this->db
            ->where('merchant_client_id', $merchantClientId)
            ->where('status', 'running')
            ->where('started_at <', date('Y-m-d H:i:s', time() - $staleAfterSeconds))
            ->update(self::TABLE, [
                'status'        => 'failed',
                'error_summary' => 'Synchronization process stopped before recording completion.',
                'finished_at'   => date('Y-m-d H:i:s'),
            ]);

        return $this->db->affected_rows();
    }

    public function finish(int $runId, array $result, int $durationMilliseconds): void
    {
        $this->db
            ->where('id', $runId)
            ->update(self::TABLE, [
                'status'            => $result['status'],
                'attempt_count'     => $result['attempts'],
                'incoming_received' => $result['incoming']['received'],
                'incoming_archived' => $result['incoming']['archived'],
                'incoming_skipped'  => $result['incoming']['skipped'],
                'incoming_failed'   => $result['incoming']['failed'],
                'events_received'   => $result['events']['received'],
                'events_created'    => $result['events']['created'],
                'events_skipped'    => $result['events']['skipped'],
                'error_summary'     => $result['errors'] === [] ? null : implode(' ', $result['errors']),
                'duration_ms'       => max(0, $durationMilliseconds),
                'finished_at'       => date('Y-m-d H:i:s'),
            ]);
    }

    public function latest_by_client(): array
    {
        $rows = $this->db->query(
            'SELECT runs.*
             FROM ' . self::TABLE . ' AS runs
             INNER JOIN (
                 SELECT merchant_client_id, MAX(id) AS latest_id
                 FROM ' . self::TABLE . '
                 GROUP BY merchant_client_id
             ) AS latest ON latest.latest_id = runs.id'
        )->result_array();
        $latest = [];

        foreach ($rows as $row) {
            $clientId = (int) $row['merchant_client_id'];
            if ( ! isset($latest[$clientId])) {
                $latest[$clientId] = $row;
            }
        }

        return $latest;
    }

    public function recent(int $limit = 100): array
    {
        return $this->db
            ->select(self::TABLE . '.*, ip_merchant_clients.label, ip_merchant_clients.merchant_type')
            ->join('ip_merchant_clients', 'ip_merchant_clients.id = ' . self::TABLE . '.merchant_client_id', 'left')
            ->order_by(self::TABLE . '.id', 'DESC')
            ->limit(max(1, min(500, $limit)))
            ->get(self::TABLE)
            ->result_array();
    }

    public function latest_success(int $merchantClientId): array
    {
        return $this->db
            ->where('merchant_client_id', $merchantClientId)
            ->where('status', 'success')
            ->order_by('finished_at', 'DESC')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get(self::TABLE)
            ->row_array() ?: [];
    }

    public function prune(int $retentionDays): int
    {
        if ($retentionDays < 1) {
            throw new InvalidArgumentException('Retention days must be positive.');
        }

        $this->db
            ->where('finished_at IS NOT NULL')
            ->where('finished_at <', date('Y-m-d H:i:s', time() - $retentionDays * 86400))
            ->delete(self::TABLE);

        return $this->db->affected_rows();
    }
}
