<?php

namespace Tests\Unit\Integration;

use LetsPeppolApiClient;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RequestMethod;
use RuntimeException;
use Tests\Fakes\Integration\ApiClientFake;

/**
 * Unit tests for LetsPeppolApiClient using ApiClientFake.
 *
 * These tests target the ApiClient layer directly — not the high-level
 * LetsPeppolClient or its endpoint objects. They prove that:
 *
 *  - authenticate() delegates to the HTTP client's fetchToken()
 *  - request() delegates to the HTTP client's send() with the bearer token
 *  - buildUrl() interpolates {id} and joins base URL + path correctly
 *  - The access token obtained from fetchToken() flows into every send() call
 */
#[Group('unit')]
class LetsPeppolApiClientTest extends TestCase
{
    // -------------------------------------------------------------------------
    // configure / getSettings
    // -------------------------------------------------------------------------

    #[Test]
    public function it_stores_settings_via_configure(): void
    {
        /* Arrange */
        [$client] = $this->makeClient();

        /* Act */
        $stored = $client->getSettings();

        /* Assert */
        self::assertSame('test-client-id', $stored['client_id']);
        self::assertSame('https://api.letspeppol.eu', $stored['api_base_url']);
    }

    // -------------------------------------------------------------------------
    // authenticate — delegates to HTTP client fetchToken
    // -------------------------------------------------------------------------

    #[Test]
    public function it_calls_fetch_token_with_the_configured_credentials(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();

        /* Act */
        $client->authenticate();

        /* Assert */
        self::assertCount(1, $http->tokenLog);
        self::assertSame('https://api.letspeppol.eu/oauth2/token', $http->tokenLog[0]['tokenUrl']);
        self::assertSame('test-client-id', $http->tokenLog[0]['clientId']);
        self::assertSame('test-client-secret', $http->tokenLog[0]['clientSecret']);
    }

    #[Test]
    public function it_stores_the_access_token_returned_by_fetch_token(): void
    {
        /* Arrange */
        [$client] = $this->makeClient([], ['access_token' => 'my-bearer-token']);

        /* Act */
        $client->authenticate();

        /* Assert */
        self::assertSame('my-bearer-token', $client->getAccessToken());
    }

    #[Test]
    public function it_throws_when_fetch_token_returns_no_access_token(): void
    {
        /* Arrange */
        [$client] = $this->makeClient([], ['error' => 'invalid_client']);

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('no access_token');

        /* Assert */
        $client->authenticate();
    }

    #[Test]
    public function it_propagates_a_network_exception_from_fetch_token(): void
    {
        /* Arrange */
        $http   = new ApiClientFake([], ['access_token' => 'tok'], 'LetsPeppol OAuth error: timeout');
        $client = new LetsPeppolApiClient($http);
        $client->configure($this->settings());

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('timeout');

        /* Assert */
        $client->authenticate();
    }

    // -------------------------------------------------------------------------
    // request — delegates to HTTP client send() with the bearer token
    // -------------------------------------------------------------------------

    #[Test]
    public function it_passes_the_bearer_token_to_send(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient([], ['access_token' => 'bearer-xyz']);
        $client->authenticate();

        /* Act */
        $client->request(RequestMethod::GET, 'https://api.letspeppol.eu/v1/invoices');

        /* Assert */
        self::assertSame('bearer-xyz', $http->requestLog[0]['bearerToken']);
    }

    #[Test]
    public function it_passes_method_url_payload_and_multipart_flag_to_send(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();
        $client->authenticate();
        $payload = ['key' => 'value'];

        /* Act */
        $client->request(RequestMethod::POST, 'https://api.letspeppol.eu/v1/invoices', $payload, true);

        /* Assert */
        self::assertSame(RequestMethod::POST, $http->requestLog[0]['method']);
        self::assertSame('https://api.letspeppol.eu/v1/invoices', $http->requestLog[0]['url']);
        self::assertSame($payload, $http->requestLog[0]['payload']);
        self::assertTrue($http->requestLog[0]['multipart']);
    }

    #[Test]
    public function it_appends_a_query_string_to_the_url_before_calling_send(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();
        $client->authenticate();

        /* Act */
        $client->request(
            RequestMethod::GET,
            'https://api.letspeppol.eu/v1/invoices',
            [],
            false,
            ['status' => 'new', 'page' => 2]
        );

        /* Assert */
        self::assertStringContainsString('status=new', $http->requestLog[0]['url']);
        self::assertStringContainsString('page=2', $http->requestLog[0]['url']);
    }

    #[Test]
    public function it_returns_the_response_envelope_from_send(): void
    {
        /* Arrange */
        $envelope = [
            'success'     => true,
            'external_id' => 'inv-123',
            'status'      => 'sent',
            'message'     => 'ok',
            'http_code'   => 201,
            'request'     => [],
            'response'    => [],
        ];
        [$client] = $this->makeClient([$envelope]);
        $client->authenticate();

        /* Act */
        $result = $client->request(RequestMethod::POST, 'https://api.letspeppol.eu/v1/invoices');

        /* Assert */
        self::assertTrue($result['success']);
        self::assertSame('inv-123', $result['external_id']);
        self::assertSame(201, $result['http_code']);
    }

    // -------------------------------------------------------------------------
    // buildUrl
    // -------------------------------------------------------------------------

    #[Test]
    public function it_joins_base_url_and_endpoint_path(): void
    {
        /* Arrange */
        [$client] = $this->makeClient();

        /* Act */
        $url = $client->buildUrl('/v1/invoices');

        /* Assert */
        self::assertSame('https://api.letspeppol.eu/v1/invoices', $url);
    }

    #[Test]
    public function it_interpolates_the_id_placeholder_in_the_endpoint(): void
    {
        /* Arrange */
        [$client] = $this->makeClient();

        /* Act */
        $url = $client->buildUrl('/v1/invoices/{id}', 'inv-99');

        /* Assert */
        self::assertSame('https://api.letspeppol.eu/v1/invoices/inv-99', $url);
        self::assertStringNotContainsString('{id}', $url);
    }

    #[Test]
    public function it_url_encodes_the_id_when_interpolating(): void
    {
        /* Arrange */
        [$client] = $this->makeClient();

        /* Act */
        $url = $client->buildUrl('/v1/invoices/{id}', 'inv 99/special');

        /* Assert */
        self::assertStringContainsString('inv+99%2Fspecial', $url);
    }

    #[Test]
    public function it_strips_duplicate_slashes_between_base_and_path(): void
    {
        /* Arrange */
        $http   = new ApiClientFake();
        $client = new LetsPeppolApiClient($http);
        $client->configure(array_merge($this->settings(), ['api_base_url' => 'https://api.letspeppol.eu/']));

        /* Act */
        $url = $client->buildUrl('/v1/invoices');

        /* Assert */
        self::assertStringNotContainsString('//', str_replace('https://', '', $url));
    }

    // -------------------------------------------------------------------------
    // Full call-flow: authenticate → request
    // -------------------------------------------------------------------------

    #[Test]
    public function it_uses_the_token_from_authenticate_in_a_subsequent_request(): void
    {
        /* Arrange */
        $http   = new ApiClientFake([], ['access_token' => 'flow-token']);
        $client = new LetsPeppolApiClient($http);
        $client->configure($this->settings());

        /* Act */
        $client->authenticate();
        $client->request(RequestMethod::GET, 'https://api.letspeppol.eu/v1/invoices');

        /* Assert */
        self::assertCount(1, $http->tokenLog, 'fetchToken must be called exactly once.');
        self::assertCount(1, $http->requestLog, 'send must be called exactly once.');
        self::assertSame(
            'flow-token',
            $http->requestLog[0]['bearerToken'],
            'The token from authenticate() must be passed to every subsequent send().'
        );
    }

    #[Test]
    public function it_sends_multiple_requests_all_carrying_the_same_token(): void
    {
        /* Arrange */
        $http   = new ApiClientFake([], ['access_token' => 'multi-token']);
        $client = new LetsPeppolApiClient($http);
        $client->configure($this->settings());
        $client->authenticate();

        /* Act */
        $client->request(RequestMethod::GET, 'https://api.letspeppol.eu/v1/invoices');
        $client->request(RequestMethod::GET, 'https://api.letspeppol.eu/v1/invoices/inv-1');

        /* Assert */
        self::assertCount(2, $http->requestLog);
        self::assertSame('multi-token', $http->requestLog[0]['bearerToken']);
        self::assertSame('multi-token', $http->requestLog[1]['bearerToken']);
    }

    private function settings(): array
    {
        return [
            'client_id'               => 'test-client-id',
            'client_secret'           => 'test-client-secret',
            'token_url'               => 'https://api.letspeppol.eu/oauth2/token',
            'api_base_url'            => 'https://api.letspeppol.eu',
            'invoice_endpoint'        => '/v1/invoices',
            'invoice_status_endpoint' => '/v1/invoices/{id}',
        ];
    }

    private function makeClient(array $responses = [], array $tokenResponse = ['access_token' => 'tok-abc']): array
    {
        $http   = new ApiClientFake($responses, $tokenResponse);
        $client = new LetsPeppolApiClient($http);
        $client->configure($this->settings());

        return [$client, $http];
    }
}
