<?php

namespace Tests\Unit\Libraries\Gateways;

use GuzzleHttp\Exception\ClientException;
use PaypalLib;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

/**
 * Regresses the OAuth-on-construct fix (GHSA-r382-4chp-xj3p / PR #1749):
 * constructing PaypalLib must not authorize against PayPal, because every
 * request that reaches the guest Paypal controller constructs the library,
 * including requests that should 404 on a local guard before ever touching
 * the PayPal API.
 */
final class PaypalLibLazyAuthTest extends TestCase
{
    protected function tearDown(): void
    {
        putenv('PAYPAL_MOCK_RESPONSES');
        parent::tearDown();
    }

    #[Test]
    public function it_does_not_authorize_when_constructed(): void
    {
        // Empty queue: ANY PayPal call at all, including authorize(), throws
        // out of Guzzle's MockHandler once the queue is exhausted.
        putenv('PAYPAL_MOCK_RESPONSES=' . json_encode([]));

        $lib = new PaypalLib(['client_id' => 'id', 'client_secret' => 'secret', 'demo' => true]);

        $bearerToken = new ReflectionProperty(PaypalLib::class, 'bearer_token');
        $bearerToken->setAccessible(true);
        self::assertFalse(
            $bearerToken->isInitialized($lib),
            'Constructing PaypalLib must not authorize against PayPal.'
        );
    }

    #[Test]
    public function it_authorizes_lazily_on_the_first_real_api_call(): void
    {
        putenv('PAYPAL_MOCK_RESPONSES=' . json_encode([
            ['status' => 200, 'body' => json_encode(['access_token' => 'fake-token'])],
            ['status' => 200, 'body' => json_encode(['id' => 'ORDER-1', 'purchase_units' => []])],
        ]));

        $lib    = new PaypalLib(['client_id' => 'id', 'client_secret' => 'secret', 'demo' => true]);
        $result = $lib->showOrderDetails('ORDER-1');

        self::assertTrue($result['status']);

        $bearerToken = new ReflectionProperty(PaypalLib::class, 'bearer_token');
        $bearerToken->setAccessible(true);
        self::assertTrue($bearerToken->isInitialized($lib));
        self::assertSame('fake-token', $bearerToken->getValue($lib));
    }

    #[Test]
    public function it_authorizes_only_once_across_multiple_api_calls(): void
    {
        // Only one access_token response queued: a second authorize() attempt
        // would consume the next (order-details) response instead and break.
        putenv('PAYPAL_MOCK_RESPONSES=' . json_encode([
            ['status' => 200, 'body' => json_encode(['access_token' => 'fake-token'])],
            ['status' => 200, 'body' => json_encode(['id' => 'ORDER-1', 'purchase_units' => []])],
            ['status' => 200, 'body' => json_encode(['id' => 'ORDER-2', 'purchase_units' => []])],
        ]));

        $lib = new PaypalLib(['client_id' => 'id', 'client_secret' => 'secret', 'demo' => true]);

        $first  = $lib->showOrderDetails('ORDER-1');
        $second = $lib->showOrderDetails('ORDER-2');

        self::assertTrue($first['status']);
        self::assertTrue($second['status']);
    }

    #[Test]
    public function it_fails_cleanly_instead_of_crashing_when_authorization_is_rejected(): void
    {
        // A 401 here makes Guzzle itself throw a ClientException out of authorize().
        // If that exception were swallowed instead of rethrown, bearer_token would stay
        // uninitialized and the very next line in buildHeaders() — reading it into the
        // Authorization header — would throw an uncatchable-by-PaypalRequestExecutor
        // \Error, crashing the request instead of returning a clean failure.
        putenv('PAYPAL_MOCK_RESPONSES=' . json_encode([
            ['status' => 401, 'body' => json_encode(['error' => 'invalid_client'])],
        ]));

        $lib    = new PaypalLib(['client_id' => 'id', 'client_secret' => 'secret', 'demo' => true]);
        $result = $lib->showOrderDetails('ORDER-1');

        self::assertFalse($result['status']);
        self::assertInstanceOf(ClientException::class, $result['error']);
    }
}
