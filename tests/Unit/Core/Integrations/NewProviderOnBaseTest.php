<?php

namespace Tests\Unit\Core\Integrations;

use ApiClientInterface;
use DemoPdpClient;
use IntegrationClient;
use IntegrationClientRegistry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RequestMethod;
use RuntimeException;

/**
 * Executable answer to "how much does a new provider cost": DemoPdpClient (about 55 lines, in
 * tests/Fixtures/integrations/providers) is a bearer provider written against AbstractRestProvider.
 * Everything below works with no provider-specific plumbing.
 */
#[Group('unit')]
final class NewProviderOnBaseTest extends TestCase
{
    private object $http;

    protected function setUp(): void
    {
        require_once APPPATH . 'modules/integrations/libraries/IntegrationClientRegistry.php';
        $GLOBALS['unitCiInstance'] ??= new class () {
            public object $load;

            public function __construct()
            {
                $this->load = new class () {
                    public function helper(mixed $helpers): void {}
                };
            }
        };

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

    #[Test]
    public function it_is_discovered_from_a_directory_without_any_registration(): void
    {
        $registry = new IntegrationClientRegistry(dirname(__DIR__, 3) . '/Fixtures/integrations/providers');

        self::assertSame(['demopdp' => 'DemoPdpClient'], $registry->all());
    }

    #[Test]
    public function it_derives_its_defaults_and_settings_form_from_the_single_definition(): void
    {
        self::assertSame('demopdp', DemoPdpClient::clientCode());
        self::assertSame('bearer', DemoPdpClient::authType());
        self::assertSame(
            ['access_token' => '', 'api_base_url' => 'https://api.demo-pdp.test', 'invoice_endpoint' => '/v1/invoices', 'invoice_status_endpoint' => '/v1/invoices/{id}'],
            DemoPdpClient::defaultSettings()
        );
        self::assertSame(
            ['type' => 'password', 'label' => 'access_token', 'required' => true, 'sensitive' => true],
            DemoPdpClient::settingsSchema()['access_token']
        );
    }

    #[Test]
    public function it_sends_with_the_bearer_token_to_the_configured_endpoint(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 201, 'response' => ['invoice' => ['id' => 'dp-9', 'status' => 'queued']]];
        $client              = new IntegrationClient(new DemoPdpClient($this->http), ['access_token' => 'tok-1']);

        $result = $client->sendInvoice('/tmp/inv-7.pdf', []);

        [$method, $url, $options] = $this->http->calls[0];
        self::assertSame(RequestMethod::POST, $method);
        self::assertSame('https://api.demo-pdp.test/v1/invoices', $url);
        self::assertSame('tok-1', $options['bearer']);
        self::assertSame('dp-9', $result['external_id']);
        self::assertSame('queued', $result['status']);
    }

    #[Test]
    public function it_builds_the_status_url_from_the_endpoint_template(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'response' => ['id' => 'a/b', 'status' => 'sent']];
        $client              = new IntegrationClient(new DemoPdpClient($this->http), ['access_token' => 'tok-1', 'api_base_url' => 'https://alt.test/']);

        $result = $client->getInvoiceStatus('a/b');

        self::assertSame('https://alt.test/v1/invoices/a%2Fb', $this->http->calls[0][1]);
        self::assertSame('sent', $result['status']);
    }

    #[Test]
    public function it_lists_incoming_invoices_with_filters_as_a_query_string(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 200, 'response' => ['invoices' => [['id' => 'x1'], ['id' => 'x2']]]];
        $client              = new IntegrationClient(new DemoPdpClient($this->http), ['access_token' => 'tok-1']);

        $result = $client->receiveInvoices(['limit' => 2]);

        self::assertSame('https://api.demo-pdp.test/v1/invoices?limit=2', $this->http->calls[0][1]);
        self::assertSame([['id' => 'x1'], ['id' => 'x2']], $result['response']['invoices']);
    }

    #[Test]
    public function it_refuses_to_authenticate_without_its_token(): void
    {
        $client = new IntegrationClient(new DemoPdpClient($this->http), ['access_token' => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Demo PDP setting: access_token');

        $client->authenticate();
    }

    #[Test]
    public function it_reports_reachability_through_the_shared_ping(): void
    {
        $this->http->queue[] = ['success' => true, 'http_code' => 503, 'message' => 'Service Unavailable', 'response' => []];

        $ping = (new DemoPdpClient($this->http))->ping(array_replace(DemoPdpClient::defaultSettings(), ['access_token' => 'tok-1']));

        self::assertSame(['reachable' => true, 'http_code' => 503, 'message' => 'Service Unavailable'], $ping);
    }

    #[Test]
    public function it_is_unreachable_when_authentication_fails(): void
    {
        $ping = (new DemoPdpClient($this->http))->ping([]);

        self::assertFalse($ping['reachable']);
        self::assertSame('Missing Demo PDP setting: access_token', $ping['message']);
    }
}
