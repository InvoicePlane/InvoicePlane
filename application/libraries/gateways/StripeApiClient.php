<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

/**
 * Thin wrapper around the Stripe SDK client that exposes a ping() reachability
 * probe with the same shape and semantics as the e-invoice provider clients'
 * IntegrationClientInterface::ping(): authenticate, make one cheap read call,
 * and report whether Stripe answered.
 *
 * PaypalLib already plays this "API client" role for PayPal.
 */
class StripeApiClient
{
    private StripeClient $stripe;

    public function __construct(?StripeClient $stripe = null, ?string $apiKey = null)
    {
        $this->stripe = $stripe ?? new StripeClient((string) $apiKey);
    }

    /**
     * Probe Stripe by listing a single Checkout Session — the same endpoint
     * family the gateway uses. A returned collection (HTTP 2xx) means Stripe is
     * reachable; an SDK exception (network failure, bad key, rate limit) means
     * it is not, with the HTTP status and message carried through for the
     * caller to judge health.
     *
     * @param array<string, mixed> $settings accepted for signature parity with
     *                                       the e-invoice provider clients; unused
     *
     * @return array{reachable: bool, http_code: int, message: string}
     */
    public function ping(array $settings = []): array
    {
        try {
            $sessions = $this->stripe->checkout->sessions->all(['limit' => 1]);
            $listed   = is_countable($sessions->data ?? null) ? count($sessions->data) : 0;

            return [
                'reachable' => true,
                'http_code' => 200,
                'message'   => sprintf('Stripe reachable (%d checkout session%s listed).', $listed, $listed === 1 ? '' : 's'),
            ];
        } catch (ApiErrorException $e) {
            return [
                'reachable' => false,
                'http_code' => (int) $e->getHttpStatus(),
                'message'   => $e->getMessage(),
            ];
        } catch (Throwable $e) {
            return [
                'reachable' => false,
                'http_code' => 0,
                'message'   => $e->getMessage(),
            ];
        }
    }
}
