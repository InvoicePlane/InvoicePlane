<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Default IntegrationClientInterface::ping() for the e-invoice provider clients.
 *
 * Every provider client already knows how to authenticate() and how to make a
 * cheap read call (receiveInvoices()), so the reachability probe is the same
 * for all of them: authenticate, list one incoming invoice, and report whether
 * the endpoint answered. A thrown exception (transport error, bad credentials,
 * missing endpoint configuration) is reachable = false; anything that returns —
 * including a 4xx/5xx envelope — is reachable = true, with http_code / message
 * left for the caller to judge health.
 */
trait ProviderPing
{
    public function ping(array $settings): array
    {
        try {
            $this->authenticate($settings);
            $result = $this->receiveInvoices(['limit' => 1]);
        } catch (Throwable $e) {
            return [
                'reachable' => false,
                'http_code' => 0,
                'message'   => $e->getMessage(),
            ];
        }

        return [
            'reachable' => true,
            'http_code' => (int) ($result['http_code'] ?? 200),
            'message'   => (string) ($result['message'] ?? 'OK'),
        ];
    }
}
