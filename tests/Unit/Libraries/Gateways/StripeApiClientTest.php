<?php

namespace Tests\Unit\Libraries\Gateways;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Stripe\ApiRequestor;
use Stripe\HttpClient\CurlClient;
use Stripe\StripeClient;
use StripeApiClient;
use Tests\Fakes\Payments\FakeStripeHttpClient;

/**
 * Unit coverage for StripeApiClient::ping() — the Stripe-side reachability
 * probe, mirroring IntegrationClientInterface::ping() on the e-invoice
 * providers.
 *
 * The Stripe SDK's HTTP layer is process-global (ApiRequestor::setHttpClient),
 * so each test swaps in Tests\Fakes\Payments\FakeStripeHttpClient with a canned
 * response and restores the real cURL client afterwards.
 */
class StripeApiClientTest extends TestCase
{
    protected function tearDown(): void
    {
        ApiRequestor::setHttpClient(CurlClient::instance());

        parent::tearDown();
    }

    #[Test]
    public function it_reports_stripe_as_reachable_when_the_session_list_call_succeeds(): void
    {
        $this->fakeStripe([
            ['status' => 200, 'body' => json_encode([
                'object'   => 'list',
                'url'      => '/v1/checkout/sessions',
                'has_more' => false,
                'data'     => [['id' => 'cs_test_1', 'object' => 'checkout.session']],
            ])],
        ]);

        $result = $this->client()->ping();

        self::assertTrue($result['reachable']);
        self::assertSame(200, $result['http_code']);
        self::assertStringContainsString('reachable', $result['message']);
    }

    #[Test]
    public function it_reports_stripe_as_unreachable_on_an_authentication_error(): void
    {
        $this->fakeStripe([
            ['status' => 401, 'body' => json_encode([
                'error' => ['type' => 'invalid_request_error', 'message' => 'Invalid API Key provided'],
            ])],
        ]);

        $result = $this->client()->ping();

        self::assertFalse($result['reachable']);
        self::assertSame(401, $result['http_code']);
        self::assertStringContainsString('Invalid API Key', $result['message']);
    }

    #[Test]
    public function it_reports_stripe_as_unreachable_on_a_server_error(): void
    {
        $this->fakeStripe([
            ['status' => 500, 'body' => json_encode([
                'error' => ['type' => 'api_error', 'message' => 'Stripe is temporarily unavailable'],
            ])],
        ]);

        $result = $this->client()->ping();

        self::assertFalse($result['reachable']);
        self::assertSame(500, $result['http_code']);
    }

    private function fakeStripe(array $queue): void
    {
        ApiRequestor::setHttpClient(new FakeStripeHttpClient($queue));
    }

    private function client(): StripeApiClient
    {
        return new StripeApiClient(new StripeClient('sk_test_fake_key'));
    }
}
