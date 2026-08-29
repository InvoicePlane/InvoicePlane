<?php

defined('BASEPATH') || exit('No direct script access allowed');

require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';
require_once APPPATH . 'modules/integrations/libraries/IntegrationClientRegistry.php';
require_once APPPATH . 'modules/integrations/libraries/IntegrationClient.php';
require_once APPPATH . 'modules/integrations/libraries/IntegrationResponseNormalizer.php';
require_once APPPATH . 'modules/integrations/libraries/IntegrationPayloadSanitizer.php';
require_once APPPATH . 'modules/integrations/libraries/IntegrationRetryPolicy.php';
require_once APPPATH . 'modules/integrations/libraries/IntegrationSyncLock.php';
require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDriver.php';
require_once APPPATH . 'modules/integrations/libraries/MerchantResponseStatus.php';
require_once APPPATH . 'modules/integrations/libraries/MerchantResponseDirection.php';
require_once APPPATH . 'modules/integrations/libraries/MerchantResponseType.php';
require_once APPPATH . 'modules/integrations/libraries/PeppolDocumentType.php';
require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfile.php';
require_once APPPATH . 'modules/integrations/libraries/EInvoiceProfileRegistry.php';
require_once APPPATH . 'modules/integrations/libraries/EInvoiceArtifact.php';
require_once APPPATH . 'modules/integrations/libraries/EInvoiceSchematronValidator.php';
require_once APPPATH . 'modules/integrations/libraries/FrenchEInvoiceValidator.php';
require_once APPPATH . 'modules/integrations/libraries/EInvoiceDocumentValidator.php';
require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceDocumentService.php';
require_once APPPATH . 'modules/integrations/libraries/IncomingInvoiceSynchronizer.php';

final class IntegrationSyncService
{
    private Closure $clientFactory;

    private IntegrationRetryPolicy $retry;

    public function __construct(
        private object $database,
        private object $clients,
        private object $responses,
        private object $runs,
        private string $archiveDirectory,
        ?IntegrationRetryPolicy $retry = null,
        ?callable $clientFactory = null
    ) {
        $this->retry         = $retry ?? new IntegrationRetryPolicy();
        $this->clientFactory = $clientFactory !== null
            ? Closure::fromCallable($clientFactory)
            : static function (array $merchantClient, array $settings): IntegrationClient {
                $provider = (new IntegrationClientRegistry())->getClient($merchantClient['merchant_type']);

                return new IntegrationClient($provider, $settings);
            };
    }

    public function run(int $merchantClientId, string $trigger = 'manual', string $scope = 'all'): array
    {
        if ( ! in_array($trigger, ['manual', 'api', 'cron'], true)
            || ! in_array($scope, ['all', 'incoming', 'events'], true)) {
            throw new InvalidArgumentException('Invalid integration synchronization mode.');
        }

        $merchantClient = $this->clients->get_by_id($merchantClientId);
        if ( ! $merchantClient || (int) $merchantClient['enabled'] !== 1) {
            throw new RuntimeException('Enabled e-invoicing provider not found.');
        }

        $this->runs->fail_stale($merchantClientId);

        $correlationId = bin2hex(random_bytes(16));
        $startedAt     = hrtime(true);
        $runId         = $this->runs->start($merchantClientId, $correlationId, $trigger, $scope);
        $lock          = new IntegrationSyncLock($this->database);
        $result        = $this->emptyResult($correlationId, $scope);

        try {
            $acquired = $lock->acquire($merchantClientId);
        } catch (Throwable $e) {
            $result['status']   = 'failed';
            $result['errors'][] = $this->safeError('Unable to acquire synchronization lock', $e);
            $this->runs->finish($runId, $result, $this->durationMilliseconds($startedAt));

            return $result;
        }

        if ( ! $acquired) {
            $result['status']   = 'skipped';
            $result['errors'][] = 'Another synchronization is already running for this provider.';
            $this->runs->finish($runId, $result, $this->durationMilliseconds($startedAt));

            return $result;
        }

        try {
            $settings = $this->clients->get_settings($merchantClient);
            $client   = ($this->clientFactory)($merchantClient, $settings);
            if ( ! $client instanceof IntegrationClient) {
                throw new RuntimeException('Integration client factory returned an invalid client.');
            }
            $driver = MerchantResponseDriver::tryFrom($merchantClient['merchant_type']);
            if ($driver === null) {
                throw new RuntimeException('Unrecognized integration provider: ' . $merchantClient['merchant_type']);
            }
            $successfulPhases = 0;

            if ($scope !== 'events') {
                $phaseAttempts = 0;
                try {
                    $operation     = $this->retry->execute(fn (): array => $client->receiveInvoices());
                    $phaseAttempts = $operation['attempts'];
                    $result['attempts'] += $phaseAttempts;
                    $items = IntegrationResponseNormalizer::extractItems(
                        $operation['response'],
                        ['data', 'items', 'invoices']
                    );
                    $result['incoming'] = (new IncomingInvoiceSynchronizer())->synchronize(
                        $client,
                        $merchantClient['merchant_type'],
                        $merchantClientId,
                        $driver,
                        $items,
                        $this->responses,
                        $this->archiveDirectory
                    );
                    $successfulPhases++;
                } catch (Throwable $e) {
                    if ($phaseAttempts === 0) {
                        $result['attempts'] += $this->retry->lastAttempts();
                    }
                    $result['errors'][] = $this->safeError('Incoming synchronization failed', $e);
                }
            }

            if ($scope !== 'incoming') {
                $phaseAttempts = 0;
                try {
                    $operation     = $this->retry->execute(fn (): array => $client->getInvoiceEvents());
                    $phaseAttempts = $operation['attempts'];
                    $result['attempts'] += $phaseAttempts;
                    $items = IntegrationResponseNormalizer::extractItems(
                        $operation['response'],
                        ['data', 'items', 'events']
                    );
                    $result['events']['received'] = count($items);

                    foreach ($items as $item) {
                        if ( ! is_array($item)) {
                            continue;
                        }

                        $created = $this->responses->create_event_item(
                            $merchantClientId,
                            $item,
                            $driver
                        );
                        if ($created > 0) {
                            $result['events']['created']++;
                        } else {
                            $result['events']['skipped']++;
                        }
                    }
                    $successfulPhases++;
                } catch (Throwable $e) {
                    if ($phaseAttempts === 0) {
                        $result['attempts'] += $this->retry->lastAttempts();
                    }
                    $result['errors'][] = $this->safeError('Event synchronization failed', $e);
                }
            }

            $expectedPhases   = $scope === 'all' ? 2 : 1;
            $documentFailures = $result['incoming']['failed'] > 0;
            $result['status'] = match (true) {
                $successfulPhases === 0                                  => 'failed',
                $successfulPhases < $expectedPhases || $documentFailures => 'partial',
                default                                                  => 'success',
            };
        } catch (Throwable $e) {
            $result['status']   = 'failed';
            $result['errors'][] = $this->safeError('Synchronization failed', $e);
        } finally {
            $lock->release();
            $result['errors'] = array_values(array_unique($result['errors']));
            $this->runs->finish($runId, $result, $this->durationMilliseconds($startedAt));
        }

        log_message(
            $result['status'] === 'success' ? 'info' : 'error',
            sprintf(
                'E-invoice sync %s provider=%s correlation_id=%s status=%s',
                $scope,
                IntegrationPayloadSanitizer::text($merchantClient['merchant_type'], 100) ?? 'unknown',
                $correlationId,
                $result['status']
            )
        );

        return $result;
    }

    private function emptyResult(string $correlationId, string $scope): array
    {
        return [
            'correlation_id' => $correlationId,
            'scope'          => $scope,
            'status'         => 'running',
            'attempts'       => 0,
            'incoming'       => ['received' => 0, 'archived' => 0, 'skipped' => 0, 'failed' => 0],
            'events'         => ['received' => 0, 'created' => 0, 'skipped' => 0],
            'errors'         => [],
        ];
    }

    private function safeError(string $prefix, Throwable $error): string
    {
        return $prefix . ': ' . (
            IntegrationPayloadSanitizer::text($error->getMessage(), 500)
                ?? 'unknown error'
        );
    }

    private function durationMilliseconds(int $startedAt): int
    {
        return (int) round((hrtime(true) - $startedAt) / 1_000_000);
    }
}
