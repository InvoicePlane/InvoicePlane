<?php

namespace Tests\Unit\Integration;

use PHPUnit\Framework\TestCase;
use RequestMethod;
use RuntimeException;
use SuperPdpClient;

/**
 * Stubs fetchToken() and request() so no real HTTP is made.
 */
class FakeSuperPdpClient extends SuperPdpClient
{
    public array $requestLog = [];

    public array $tokenLog = [];

    private array $responses;

    private int $callIndex = 0;

    private array $tokenResponse;

    private ?string $tokenError;

    public function __construct(
        array $responses = [],
        array $tokenResponse = ['access_token' => 'fake-token'],
        ?string $tokenError = null
    ) {
        $this->responses     = $responses;
        $this->tokenResponse = $tokenResponse;
        $this->tokenError    = $tokenError;
    }

    protected function oauthFetchToken(string $tokenUrl, string $clientId, string $clientSecret): array
    {
        $this->tokenLog[] = compact('tokenUrl', 'clientId', 'clientSecret');

        if ($this->tokenError !== null) {
            throw new RuntimeException($this->tokenError);
        }

        return $this->tokenResponse;
    }

    protected function request(
        RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $this->requestLog[] = compact('method', 'url', 'payload', 'multipart', 'requestDebug');

        return $this->responses[$this->callIndex++] ?? [
            'success'     => true,
            'external_id' => null,
            'status'      => 'sent',
            'message'     => 'ok',
            'http_code'   => 200,
            'request'     => ['url' => $url, 'method' => $method->value],
            'response'    => [],
        ];
    }
}

class SuperPdpClientTest extends TestCase
{
    // --- authenticate ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_calls_fetch_token_with_the_configured_credentials(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();

        /* Act */
        $result = $provider->authenticate($this->defaultSettings());

        /* Assert */
        $this->assertTrue($result);
        $this->assertCount(1, $provider->tokenLog);
        $this->assertSame('https://api.superpdp.tech/oauth2/token', $provider->tokenLog[0]['tokenUrl']);
        $this->assertSame('cid', $provider->tokenLog[0]['clientId']);
        $this->assertSame('csecret', $provider->tokenLog[0]['clientSecret']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_client_id_is_missing(): void
    {
        /* Arrange */
        $provider              = new FakeSuperPdpClient();
        $settings              = $this->defaultSettings();
        $settings['client_id'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing SuperPDP OAuth2 settings.');

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_client_secret_is_missing(): void
    {
        /* Arrange */
        $provider                  = new FakeSuperPdpClient();
        $settings                  = $this->defaultSettings();
        $settings['client_secret'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_token_url_is_missing(): void
    {
        /* Arrange */
        $provider              = new FakeSuperPdpClient();
        $settings              = $this->defaultSettings();
        $settings['token_url'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_the_token_response_contains_no_access_token(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient([], ['error' => 'invalid_client']);

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no access_token');

        /* Assert */
        $provider->authenticate($this->defaultSettings());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_propagates_a_fetch_token_network_exception(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient([], ['access_token' => 'tok'], 'SuperPDP OAuth error: connection refused');

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('connection refused');

        /* Assert */
        $provider->authenticate($this->defaultSettings());
    }

    // --- sendInvoice ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_send_when_the_invoice_file_does_not_exist(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invoice document not found');

        /* Assert */
        $provider->sendInvoice('/nonexistent/invoice.pdf', []);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_send_when_no_access_token_is_present(): void
    {
        /* Arrange */
        $noTokenProvider = new FakeSuperPdpClient([], []);
        try {
            $noTokenProvider->authenticate($this->defaultSettings());
        } catch (RuntimeException) {
            // expected — fetchToken returns no access_token
        }
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing SuperPDP access token.');

        /* Assert */
        try {
            $noTokenProvider->sendInvoice($tmp, []);
        } finally {
            unlink($tmp);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_an_invoice_as_a_multipart_post(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $provider->authenticate($this->defaultSettings());
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $result = $provider->sendInvoice($tmp, ['ref' => 'INV-001']);
        unlink($tmp);

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertSame(RequestMethod::POST, $provider->requestLog[0]['method']);
        $this->assertTrue($provider->requestLog[0]['multipart']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_appends_the_disable_pre_check_flag_to_the_url(): void
    {
        /* Arrange */
        $settings                      = $this->defaultSettings();
        $settings['disable_pre_check'] = true;
        $provider                      = new FakeSuperPdpClient();
        $provider->authenticate($settings);
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $provider->sendInvoice($tmp, []);
        unlink($tmp);

        /* Assert */
        $this->assertStringContainsString('disable_pre_check=1', $provider->requestLog[0]['url']);
    }

    // --- getInvoiceStatus ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_interpolates_the_invoice_id_into_the_status_url(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient([
            ['success' => true, 'external_id' => 'inv-5', 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $result = $provider->getInvoiceStatus('inv-5');

        /* Assert */
        $this->assertSame('inv-5', $result['external_id']);
        $this->assertStringContainsString('inv-5', $provider->requestLog[0]['url']);
        $this->assertStringNotContainsString('{id}', $provider->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_get_status_when_the_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $settings                            = $this->defaultSettings();
        $settings['invoice_status_endpoint'] = '';
        $provider                            = new FakeSuperPdpClient();
        $provider->authenticate($settings);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->getInvoiceStatus('inv-1');
    }

    // --- receiveInvoices ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_appends_filters_as_a_query_string_when_receiving_invoices(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $provider->receiveInvoices(['status' => 'new', 'page' => 1]);

        /* Assert */
        $this->assertStringContainsString('status=new', $provider->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_receive_when_the_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $settings                               = $this->defaultSettings();
        $settings['incoming_invoices_endpoint'] = '';
        $provider                               = new FakeSuperPdpClient();
        $provider->authenticate($settings);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->receiveInvoices();
    }

    // --- getInvoiceEvents ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fetches_invoice_events_with_a_get_request(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $provider->getInvoiceEvents();

        /* Assert */
        $this->assertSame(RequestMethod::GET, $provider->requestLog[0]['method']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_get_events_when_the_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $settings                            = $this->defaultSettings();
        $settings['invoice_events_endpoint'] = '';
        $provider                            = new FakeSuperPdpClient();
        $provider->authenticate($settings);

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->getInvoiceEvents();
    }

    // --- static metadata ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_superpdp_as_the_provider_code(): void
    {
        /* Arrange */

        /* Act */
        $code = SuperPdpClient::clientCode();

        /* Assert */
        $this->assertSame('superpdp', $code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_SuperPDP_as_the_provider_name(): void
    {
        /* Arrange */

        /* Act */
        $name = SuperPdpClient::clientName();

        /* Assert */
        $this->assertSame('SuperPDP', $name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_all_required_oauth_keys_in_default_settings(): void
    {
        /* Arrange */

        /* Act */
        $settings = SuperPdpClient::defaultSettings();

        /* Assert */
        foreach (['client_id', 'client_secret', 'token_url', 'api_base_url'] as $key) {
            $this->assertArrayHasKey($key, $settings);
        }
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_metadata_unchanged_from_build_invoice_payload(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $metadata = ['ref' => 'INV-001', 'custom' => true];

        /* Act */
        $result = $provider->buildInvoicePayload((object) [], [], $metadata);

        /* Assert */
        $this->assertSame($metadata, $result);
    }

    private function defaultSettings(): array
    {
        return [
            'client_id'                  => 'cid',
            'client_secret'              => 'csecret',
            'token_url'                  => 'https://api.superpdp.tech/oauth2/token',
            'api_base_url'               => 'https://api.superpdp.tech',
            'invoice_endpoint'           => '/v1.beta/invoices',
            'invoice_status_endpoint'    => '/v1.beta/invoices/{id}',
            'incoming_invoices_endpoint' => '/v1.beta/invoices',
            'invoice_events_endpoint'    => '/v1.beta/invoice_events',
            'disable_pre_check'          => false,
        ];
    }
}
