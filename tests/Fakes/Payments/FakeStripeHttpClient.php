<?php

namespace Tests\Fakes\Payments;

use Stripe\HttpClient\ClientInterface;

/**
 * Replays Stripe responses and optionally records the outbound request.
 *
 * This fake is loaded only by the integration request subprocess. It keeps
 * Stripe SDK transport concerns and cross-process request capture out of the
 * production controller.
 */
final class FakeStripeHttpClient implements ClientInterface
{
    public function __construct(private array $queue) {}

    public function request($method, $absUrl, $headers, $params, $hasFile)
    {
        $captureFile = getenv('STRIPE_MOCK_REQUEST_CAPTURE');
        if (is_string($captureFile) && $captureFile !== '') {
            file_put_contents($captureFile, (string) json_encode([
                'method' => $method,
                'url'    => $absUrl,
                'params' => $params,
            ], JSON_THROW_ON_ERROR));
        }

        $entry = array_shift($this->queue) ?? ['status' => 200, 'body' => '{}'];

        return [(string) ($entry['body'] ?? '{}'), (int) ($entry['status'] ?? 200), []];
    }
}
