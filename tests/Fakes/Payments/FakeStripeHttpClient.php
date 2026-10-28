<?php

namespace Tests\Fakes\Payments;

use OutOfBoundsException;
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

        // Fail loudly, like Guzzle's MockHandler (used by the PayPal fake), instead of
        // silently returning a fabricated 200 '{}' — a test whose code made one Stripe
        // call too many (or too few queued responses) must turn red, not pass by accident.
        if ($this->queue === []) {
            throw new OutOfBoundsException('Stripe mock queue is empty: an unexpected API call was made.');
        }

        $entry = array_shift($this->queue);

        return [(string) ($entry['body'] ?? '{}'), (int) ($entry['status'] ?? 200), []];
    }
}
