<?php

defined('BASEPATH') || exit('No direct script access allowed');

#[AllowDynamicProperties]
class Cli extends MX_Controller
{
    public function __construct()
    {
        if ( ! is_cli()) {
            show_error('This controller can only be accessed from the command line.', 403);
        }

        parent::__construct();

        $this->load->database();
        $this->load->model('integrations/Merchant_clients_model');
        $this->load->model('integrations/Merchant_responses_model');
        $this->load->model('integrations/Integration_sync_runs_model');

        require_once APPPATH . 'modules/integrations/libraries/IntegrationSyncService.php';
    }

    /**
     * Usage: php index.php integrations/cli/sync [merchant-client-id].
     */
    public function sync($merchantClientId = null): void
    {
        try {
            $clients = $this->clientsToRun($merchantClientId);
        } catch (Throwable $e) {
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            exit(2);
        }

        $exitCode = 0;

        foreach ($clients as $client) {
            try {
                $result = $this->syncService()->run((int) $client['id'], 'cron', 'all');
            } catch (Throwable $e) {
                $result = [
                    'correlation_id' => null,
                    'status'         => 'failed',
                    'errors'         => [IntegrationPayloadSanitizer::text($e->getMessage(), 500)],
                ];
            }

            echo json_encode([
                'provider_id' => (int) $client['id'],
                'provider'    => $client['merchant_type'],
                'run'         => $result,
            ], JSON_UNESCAPED_SLASHES) . PHP_EOL;

            if (($result['status'] ?? 'failed') !== 'success') {
                $exitCode = 1;
            }
        }

        exit($exitCode);
    }

    /**
     * Usage: php index.php integrations/cli/health [maximum-age-minutes].
     */
    public function health($maximumAgeMinutes = 60): void
    {
        $maximumAgeMinutes = filter_var($maximumAgeMinutes, FILTER_VALIDATE_INT);
        if ($maximumAgeMinutes === false || $maximumAgeMinutes < 1 || $maximumAgeMinutes > 10080) {
            fwrite(STDERR, 'Maximum age must be between 1 and 10080 minutes.' . PHP_EOL);
            exit(2);
        }

        $now             = time();
        $clients         = $this->Merchant_clients_model->get_enabled_clients();
        $latestRuns      = $this->Integration_sync_runs_model->latest_by_client();
        $archiveWritable = is_dir(UPLOADS_ARCHIVE_FOLDER) && is_writable(UPLOADS_ARCHIVE_FOLDER);
        $healthy         = $clients !== [] && $archiveWritable;
        $providers       = [];

        foreach ($clients as $client) {
            $clientId     = (int) $client['id'];
            $success      = $this->Integration_sync_runs_model->latest_success($clientId);
            $latest       = $latestRuns[$clientId] ?? [];
            $finished     = isset($success['finished_at']) ? strtotime($success['finished_at']) : false;
            $age          = $finished === false ? null : max(0, $now - $finished);
            $latestStatus = $latest['status'] ?? null;
            try {
                $this->Merchant_clients_model->get_settings($client);
                $configurationReadable = true;
            } catch (Throwable) {
                $configurationReadable = false;
            }
            $current = $age !== null
                && $age <= $maximumAgeMinutes * 60
                && ! in_array($latestStatus, ['failed', 'partial'], true)
                && $configurationReadable;
            $healthy = $healthy && $current;

            $providers[] = [
                'provider_id'              => $clientId,
                'provider'                 => $client['merchant_type'],
                'healthy'                  => $current,
                'configuration_readable'   => $configurationReadable,
                'latest_status'            => $latestStatus,
                'latest_correlation_id'    => $latest['correlation_id'] ?? null,
                'last_success_at'          => $success['finished_at'] ?? null,
                'last_success_age_seconds' => $age,
            ];
        }

        echo json_encode([
            'healthy'             => $healthy,
            'maximum_age_minutes' => $maximumAgeMinutes,
            'checks'              => [
                'enabled_provider' => $clients !== [],
                'archive_writable' => $archiveWritable,
            ],
            'providers' => $providers,
        ], JSON_UNESCAPED_SLASHES) . PHP_EOL;

        exit($healthy ? 0 : 1);
    }

    /**
     * Usage: php index.php integrations/cli/prune [retention-days].
     */
    public function prune($retentionDays = 90): void
    {
        $retentionDays = filter_var($retentionDays, FILTER_VALIDATE_INT);
        if ($retentionDays === false || $retentionDays < 7 || $retentionDays > 3650) {
            fwrite(STDERR, 'Retention must be between 7 and 3650 days.' . PHP_EOL);
            exit(2);
        }

        $deleted = $this->Integration_sync_runs_model->prune($retentionDays);
        echo json_encode([
            'retention_days' => $retentionDays,
            'deleted_runs'   => $deleted,
        ]) . PHP_EOL;
    }

    private function clientsToRun(mixed $merchantClientId): array
    {
        if ($merchantClientId === null || $merchantClientId === '') {
            $clients = $this->Merchant_clients_model->get_enabled_clients();
            if ($clients === []) {
                throw new RuntimeException('No enabled e-invoicing provider found.');
            }

            return $clients;
        }

        $validatedId = filter_var($merchantClientId, FILTER_VALIDATE_INT);
        if ($validatedId === false || $validatedId < 1) {
            throw new InvalidArgumentException('Provider ID must be a positive integer.');
        }

        $client = $this->Merchant_clients_model->get_by_id($validatedId);
        if ( ! $client || (int) $client['enabled'] !== 1) {
            throw new RuntimeException('Enabled e-invoicing provider not found.');
        }

        return [$client];
    }

    private function syncService(): IntegrationSyncService
    {
        return new IntegrationSyncService(
            $this->db,
            $this->Merchant_clients_model,
            $this->Merchant_responses_model,
            $this->Integration_sync_runs_model,
            UPLOADS_ARCHIVE_FOLDER
        );
    }
}
