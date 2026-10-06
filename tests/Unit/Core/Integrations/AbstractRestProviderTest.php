<?php

namespace Tests\Unit\Core\Integrations;

use AbstractRestProvider;
use ApiClientInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RequestMethod;
use RuntimeException;

require_once APPPATH . 'modules/integrations/libraries/IntegrationClientInterface.php';
require_once APPPATH . 'modules/integrations/libraries/ProviderPing.php';
require_once APPPATH . 'modules/integrations/libraries/AbstractRestProvider.php';

#[Group('unit')]
final class AbstractRestProviderTest extends TestCase
{
    private object $http;

    protected function setUp(): void
    {
        $this->http = new class () implements ApiClientInterface {
            /** @var list<array{0: RequestMethod, 1: string, 2: array}> */
            public array $calls = [];

            /** @var list<array> */
            public array $queue = [];

            public function request(RequestMethod $method, string $url, array $options = []): array
            {
                $this->calls[] = [$method, $url, $options];

                return array_shift($this->queue) ?? ['success' => true, 'http_code' => 200, 'response' => []];
            }
        };
    }

    /** @return array<string, array{array<string, string>, string}> */
    public static function missingBearerSettings(): array
    {
        return [
            'no token'    => [['access_token' => '', 'api_base_url' => 'https://x.test'], 'access_token'],
            'no base url' => [['access_token' => 'tok', 'api_base_url' => ''], 'api_base_url'],
        ];
    }

    /** @return array<string, array{string}> */
    public static function missingOauthSettings(): array
    {
        return ['client_id' => ['client_id'], 'client_secret' => ['client_secret'], 'token_url' => ['token_url'], 'api_base_url' => ['api_base_url']];
    }

    // ---- definition-derived metadata -----------------------------------------------------------

    #[Test]
    public function it_derives_code_name_and_auth_type_from_the_definition(): void
    {
        self::assertSame('bearerfix', BearerFixtureProvider::clientCode());
        self::assertSame('Bearer Fixture', BearerFixtureProvider::clientName());
        self::assertSame('bearer', BearerFixtureProvider::authType());
        self::assertSame('oauth2', OauthFixtureProvider::authType());
    }

    #[Test]
    public function it_defaults_a_setting_without_a_default_to_an_empty_string_and_keeps_given_defaults(): void
    {
        self::assertSame(
            ['access_token' => '', 'api_base_url' => 'https://api.fixture.test', 'flag' => false, 'limit' => 25],
            BearerFixtureProvider::defaultSettings()
        );
    }

    #[Test]
    public function it_only_marks_a_form_field_required_or_sensitive_when_the_definition_says_so(): void
    {
        $schema = BearerFixtureProvider::settingsSchema();

        self::assertSame(['type' => 'password', 'label' => 'access_token', 'required' => true, 'sensitive' => true], $schema['access_token']);
        self::assertSame(['type' => 'url', 'label' => 'api_base_url', 'required' => true], $schema['api_base_url']);
        self::assertSame(['type' => 'checkbox', 'label' => 'flag'], $schema['flag']);
        self::assertSame(['type' => 'text', 'label' => 'limit'], $schema['limit']);
        self::assertSame(['access_token', 'api_base_url', 'flag', 'limit'], array_keys($schema), 'Form order follows the definition.');
    }

    #[Test]
    public function it_returns_the_metadata_unchanged_as_the_default_invoice_payload(): void
    {
        $provider = new BearerFixtureProvider($this->http);

        self::assertSame(['invoice_id' => 7], $provider->buildInvoicePayload((object) ['invoice_number' => 'X'], [], ['invoice_id' => 7]));
    }

    // ---- bearer authentication -----------------------------------------------------------------

    #[Test]
    public function it_authenticates_with_the_configured_token_and_uses_it_as_bearer(): void
    {
        $provider = new BearerFixtureProvider($this->http);

        self::assertTrue($provider->authenticate(['access_token' => 'tok-b', 'api_base_url' => 'https://api.fixture.test']));
        $provider->callRequest(RequestMethod::GET, 'https://api.fixture.test/x');

        self::assertSame('tok-b', $this->http->calls[0][2]['bearer']);
    }

    #[Test]
    public function it_reads_the_token_from_a_custom_token_setting(): void
    {
        $provider = new CustomTokenFixtureProvider($this->http);

        $provider->authenticate(['api_key' => 'key-1', 'api_base_url' => 'https://x.test']);
        $provider->callRequest(RequestMethod::GET, 'https://x.test/x');

        self::assertSame('key-1', $this->http->calls[0][2]['bearer']);
        self::assertSame('key-1', $provider->fetchToken(['api_key' => 'key-1']));
    }

    #[Test]
    #[DataProvider('missingBearerSettings')]
    public function it_names_the_missing_setting_when_bearer_authentication_is_incomplete(array $settings, string $missing): void
    {
        $provider = new BearerFixtureProvider($this->http);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Bearer Fixture setting: ' . $missing);

        $provider->authenticate($settings);
    }

    #[Test]
    public function it_fetches_a_bearer_token_straight_from_the_settings(): void
    {
        $provider = new BearerFixtureProvider($this->http);

        self::assertSame('tok-f', $provider->fetchToken(['access_token' => 'tok-f']));
        self::assertSame('', $provider->fetchToken([]));
        self::assertSame([], $this->http->calls, 'A bearer token needs no network call.');
    }

    // ---- OAuth2 client credentials -------------------------------------------------------------

    #[Test]
    public function it_exchanges_client_credentials_for_a_bearer_token(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'response' => ['access_token' => 'oauth-tok']];
        $provider            = new OauthFixtureProvider($this->http);

        self::assertTrue($provider->authenticate($this->oauthSettings()));

        [$method, $url, $options] = $this->http->calls[0];
        self::assertSame(RequestMethod::POST, $method);
        self::assertSame('https://auth.fixture.test/token', $url);
        self::assertSame(['grant_type' => 'client_credentials', 'client_id' => 'cid', 'client_secret' => 'sec'], $options['form_params']);

        $provider->callRequest(RequestMethod::GET, 'https://api.fixture.test/x');
        self::assertSame('oauth-tok', $this->http->calls[1][2]['bearer']);
    }

    #[Test]
    #[DataProvider('missingOauthSettings')]
    public function it_names_the_missing_setting_when_oauth_authentication_is_incomplete(string $missing): void
    {
        $provider = new OauthFixtureProvider($this->http);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Oauth Fixture setting: ' . $missing);

        $provider->authenticate(array_replace($this->oauthSettings(), [$missing => '']));
    }

    #[Test]
    public function it_fails_when_the_token_response_has_no_access_token(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'response' => ['token_type' => 'Bearer']];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Oauth Fixture OAuth failed: no access_token in response.');

        (new OauthFixtureProvider($this->http))->authenticate($this->oauthSettings());
    }

    #[Test]
    public function it_surfaces_the_transport_message_when_the_token_request_fails(): void
    {
        $this->http->queue[] = ['success' => false, 'http_code' => 401, 'message' => 'invalid_client', 'response' => []];

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Oauth Fixture OAuth error: invalid_client');

        (new OauthFixtureProvider($this->http))->authenticate($this->oauthSettings());
    }

    #[Test]
    public function it_fetches_an_oauth_token_without_requiring_authentication_first(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'response' => ['access_token' => 'fresh']];

        self::assertSame('fresh', (new OauthFixtureProvider($this->http))->fetchToken($this->oauthSettings()));
        self::assertSame('https://auth.fixture.test/token', $this->http->calls[0][1]);
    }

    #[Test]
    public function it_returns_an_empty_token_when_the_oauth_response_carries_none(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'response' => []];

        self::assertSame('', (new OauthFixtureProvider($this->http))->fetchToken($this->oauthSettings()));
    }

    // ---- URL building --------------------------------------------------------------------------

    #[Test]
    public function it_joins_base_url_and_endpoint_with_exactly_one_slash(): void
    {
        $provider = new BearerFixtureProvider($this->http);
        $provider->authenticate(['access_token' => 't', 'api_base_url' => 'https://api.fixture.test/']);

        self::assertSame('https://api.fixture.test/v1/x', $provider->callBuildUrl('/v1/x'));
        self::assertSame('https://api.fixture.test/v1/x', $provider->callBuildUrl('v1/x'));
    }

    #[Test]
    public function it_appends_a_url_encoded_query_string_only_when_there_is_a_query(): void
    {
        $provider = new BearerFixtureProvider($this->http);
        $provider->authenticate(['access_token' => 't', 'api_base_url' => 'https://api.fixture.test']);

        self::assertSame('https://api.fixture.test/v1/x?a=1&b=x+y', $provider->callBuildUrl('/v1/x', ['a' => 1, 'b' => 'x y']));
        self::assertSame('https://api.fixture.test/v1/x', $provider->callBuildUrl('/v1/x', []));
    }

    // ---- request options -----------------------------------------------------------------------

    #[Test]
    public function it_sends_a_json_body_only_for_a_post_with_a_payload(): void
    {
        $provider = new BearerFixtureProvider($this->http);
        $provider->authenticate(['access_token' => 't', 'api_base_url' => 'https://x.test']);

        $provider->callRequest(RequestMethod::POST, 'https://x.test/a', ['k' => 'v']);
        $provider->callRequest(RequestMethod::POST, 'https://x.test/b', []);
        $provider->callRequest(RequestMethod::GET, 'https://x.test/c', ['k' => 'v']);

        self::assertSame(['k' => 'v'], $this->http->calls[0][2]['json']);
        self::assertArrayNotHasKey('json', $this->http->calls[1][2], 'An empty POST payload sends no body.');
        self::assertArrayNotHasKey('json', $this->http->calls[2][2], 'A GET never sends a body.');
    }

    #[Test]
    public function it_sends_multipart_instead_of_json_when_asked(): void
    {
        $provider = new BearerFixtureProvider($this->http);
        $provider->authenticate(['access_token' => 't', 'api_base_url' => 'https://x.test']);

        $provider->callRequest(RequestMethod::POST, 'https://x.test/a', [['name' => 'file']], true);

        self::assertSame([['name' => 'file']], $this->http->calls[0][2]['multipart']);
        self::assertArrayNotHasKey('json', $this->http->calls[0][2]);
    }

    #[Test]
    public function it_adds_extra_headers_only_when_the_provider_defines_some(): void
    {
        $plain = new BearerFixtureProvider($this->http);
        $plain->authenticate(['access_token' => 't', 'api_base_url' => 'https://x.test']);
        $plain->callRequest(RequestMethod::GET, 'https://x.test/a');

        $custom = new CustomTokenFixtureProvider($this->http);
        $custom->authenticate(['api_key' => 'k', 'api_base_url' => 'https://x.test']);
        $custom->callRequest(RequestMethod::GET, 'https://x.test/b');

        self::assertArrayNotHasKey('headers', $this->http->calls[0][2]);
        self::assertSame(['X-Fixture: yes'], $this->http->calls[1][2]['headers']);
    }

    #[Test]
    public function it_echoes_request_context_into_the_response_only_for_providers_that_opt_in(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'request' => ['url' => 'u'], 'response' => []];
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'request' => ['url' => 'u'], 'response' => []];
        $quiet               = new BearerFixtureProvider($this->http);
        $loud                = new CustomTokenFixtureProvider($this->http);
        $quiet->authenticate(['access_token' => 't', 'api_base_url' => 'https://x.test']);
        $loud->authenticate(['api_key' => 'k', 'api_base_url' => 'https://x.test']);

        $quietResponse = $quiet->callRequest(RequestMethod::GET, 'https://x.test/a', [], false, ['external_id' => 'e1']);
        $loudResponse  = $loud->callRequest(RequestMethod::GET, 'https://x.test/a', [], false, ['external_id' => 'e1']);

        self::assertSame(['url' => 'u'], $quietResponse['request']);
        self::assertSame(['url' => 'u', 'external_id' => 'e1'], $loudResponse['request']);
    }

    #[Test]
    public function it_does_not_add_a_request_key_when_there_is_no_context_to_echo(): void
    {
        $loud = new CustomTokenFixtureProvider($this->http);
        $loud->authenticate(['api_key' => 'k', 'api_base_url' => 'https://x.test']);

        $response = $loud->callRequest(RequestMethod::GET, 'https://x.test/a');

        self::assertArrayNotHasKey('request', $response);
    }

    // ---- setting guards ------------------------------------------------------------------------

    #[Test]
    public function it_requires_a_single_setting_by_name_using_the_provider_label(): void
    {
        $provider = new CustomTokenFixtureProvider($this->http);
        $provider->authenticate(['api_key' => 'k', 'api_base_url' => 'https://x.test']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Custom setting: missing_endpoint');

        $provider->callRequireSetting('missing_endpoint');
    }

    #[Test]
    public function it_accepts_a_setting_that_is_present(): void
    {
        $provider = new CustomTokenFixtureProvider($this->http);
        $provider->authenticate(['api_key' => 'k', 'api_base_url' => 'https://x.test']);

        $provider->callRequireSetting('api_key');

        $this->addToAssertionCount(1);
    }

    /** @return array<string, string> */
    private function oauthSettings(): array
    {
        return ['client_id' => 'cid', 'client_secret' => 'sec', 'token_url' => 'https://auth.fixture.test/token', 'api_base_url' => 'https://api.fixture.test'];
    }
}

abstract class FixtureProviderBase extends AbstractRestProvider
{
    public function sendInvoice(string $documentPath, array $metadata): array
    {
        return [];
    }

    public function getInvoiceStatus(string $externalId): array
    {
        return [];
    }

    public function receiveInvoices(array $filters = []): array
    {
        return [];
    }

    public function downloadInvoiceDocument(array $invoice): array
    {
        return [];
    }

    public function getInvoiceEvents(array $filters = []): array
    {
        return [];
    }

    public function callRequest(mixed ...$arguments): array
    {
        return $this->request(...$arguments);
    }

    public function callBuildUrl(string $endpoint, array $query = []): string
    {
        return $this->buildUrl($endpoint, $query);
    }

    public function callRequireSetting(string $key): void
    {
        $this->requireSetting($key);
    }
}

final class BearerFixtureProvider extends FixtureProviderBase
{
    protected static function definition(): array
    {
        return [
            'code'     => 'bearerfix',
            'name'     => 'Bearer Fixture',
            'auth'     => 'bearer',
            'settings' => [
                'access_token' => ['type' => 'password', 'required' => true, 'sensitive' => true],
                'api_base_url' => ['default' => 'https://api.fixture.test', 'type' => 'url', 'required' => true],
                'flag'         => ['default' => false, 'type' => 'checkbox'],
                'limit'        => ['default' => 25, 'type' => 'text'],
            ],
        ];
    }
}

final class OauthFixtureProvider extends FixtureProviderBase
{
    protected static function definition(): array
    {
        return [
            'code'     => 'oauthfix',
            'name'     => 'Oauth Fixture',
            'auth'     => 'oauth2',
            'settings' => [
                'client_id'     => ['type' => 'text', 'required' => true],
                'client_secret' => ['type' => 'password', 'required' => true, 'sensitive' => true],
                'token_url'     => ['type' => 'url', 'required' => true],
                'api_base_url'  => ['type' => 'url', 'required' => true],
            ],
        ];
    }
}

final class CustomTokenFixtureProvider extends FixtureProviderBase
{
    protected bool $mergeRequestDebug = true;

    protected static function definition(): array
    {
        return [
            'code'     => 'customfix',
            'name'     => 'Custom Token Fixture',
            'label'    => 'Custom',
            'auth'     => 'bearer',
            'token'    => 'api_key',
            'settings' => [
                'api_key'      => ['type' => 'password', 'required' => true, 'sensitive' => true],
                'api_base_url' => ['type' => 'url', 'required' => true],
            ],
        ];
    }

    protected function extraHeaders(): array
    {
        return ['X-Fixture: yes'];
    }
}
