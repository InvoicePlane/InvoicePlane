<?php

namespace Tests\Unit\Integration;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use QontoClient;
use RequestMethod;
use RuntimeException;
use Tests\Fakes\Integration\ApiClientFake;

/**
 * Unit tests for QontoClient using ApiClientFake.
 *
 * The client's low-level request() delegates to an injected ApiClientInterface,
 * so a fake HTTP adapter lets us drive the full upload -> create -> send
 * orchestration and assert on the exact requests emitted without any network.
 */
#[Group('unit')]
class QontoClientTest extends TestCase
{
    /** @var array<int, string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (is_file($file)) {
                @unlink($file);
            }
        }

        $this->tempFiles = [];

        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // Static metadata
    // -------------------------------------------------------------------------

    #[Test]
    public function it_exposes_client_code_and_name(): void
    {
        /* Arrange */
        $expectedCode = 'qonto';
        $expectedName = 'Qonto PA';

        /* Act */
        $clientCode = QontoClient::clientCode();
        $clientName = QontoClient::clientName();

        /* Assert */
        self::assertSame($expectedCode, $clientCode);
        self::assertSame($expectedName, $clientName);
    }

    #[Test]
    public function it_provides_default_settings_with_all_endpoints(): void
    {
        /* Act */
        $defaults = QontoClient::defaultSettings();

        /* Assert */
        self::assertArrayHasKey('access_token', $defaults);
        self::assertSame('https://thirdparty.qonto.com', $defaults['api_base_url']);
        self::assertSame('/v2/client_invoices/uploads', $defaults['upload_endpoint']);
        self::assertSame('/v2/client_invoices', $defaults['invoice_endpoint']);
        self::assertSame('/v2/client_invoices/{id}/send_by_einvoice', $defaults['send_invoice_endpoint']);
    }

    // -------------------------------------------------------------------------
    // authenticate / fetchToken
    // -------------------------------------------------------------------------

    #[Test]
    public function it_authenticates_when_required_settings_are_present(): void
    {
        /* Arrange */
        $client = new QontoClient(new ApiClientFake());

        /* Act */
        $authenticated = $client->authenticate($this->settings());

        /* Assert */
        self::assertTrue($authenticated);
    }

    #[Test]
    public function it_rejects_authentication_without_access_token(): void
    {
        /* Arrange */
        $client = new QontoClient(new ApiClientFake());

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: access_token');

        /* Act */
        $client->authenticate($this->settings(['access_token' => '']));
    }

    #[Test]
    public function it_rejects_authentication_without_api_base_url(): void
    {
        /* Arrange */
        $client = new QontoClient(new ApiClientFake());

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: api_base_url');

        /* Act */
        $client->authenticate($this->settings(['api_base_url' => '']));
    }

    #[Test]
    public function it_returns_the_access_token_from_fetch_token(): void
    {
        /* Arrange */
        $client = new QontoClient(new ApiClientFake());

        /* Act & Assert */
        self::assertSame('abc-123', $client->fetchToken(['access_token' => 'abc-123']));
        self::assertSame('', $client->fetchToken([]));
    }

    // -------------------------------------------------------------------------
    // request layer — bearer token, staging header, method
    // -------------------------------------------------------------------------

    #[Test]
    public function it_attaches_the_bearer_token_to_every_request(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();

        /* Act */
        $client->receiveInvoices();

        /* Assert */
        self::assertSame('qonto-token', $http->requestLog[0]['bearerToken']);
    }

    #[Test]
    public function it_adds_the_staging_token_header_when_configured(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient([], $this->settings(['staging_token' => 'stg-1']));

        /* Act */
        $client->receiveInvoices();

        /* Assert */
        self::assertContains(
            'X-Qonto-Staging-Token: stg-1',
            $http->requestLog[0]['options']['headers']
        );
    }

    #[Test]
    public function it_omits_the_staging_header_when_not_configured(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();

        /* Act */
        $client->receiveInvoices();

        /* Assert */
        self::assertArrayNotHasKey('headers', $http->requestLog[0]['options']);
    }

    // -------------------------------------------------------------------------
    // buildUrl — via receiveInvoices / getInvoiceEvents query handling
    // -------------------------------------------------------------------------

    #[Test]
    public function it_builds_the_incoming_invoices_url_with_query_filters(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();

        /* Act */
        $client->receiveInvoices(['status' => 'pending', 'page' => 2]);

        /* Assert */
        self::assertSame(RequestMethod::GET, $http->requestLog[0]['method']);
        self::assertSame(
            'https://thirdparty.qonto.com/v2/supplier_invoices?status=pending&page=2',
            $http->requestLog[0]['url']
        );
    }

    #[Test]
    public function it_does_not_append_a_query_string_when_no_filters_are_given(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();

        /* Act */
        $client->receiveInvoices();

        /* Assert */
        self::assertSame(
            'https://thirdparty.qonto.com/v2/supplier_invoices',
            $http->requestLog[0]['url']
        );
    }

    // -------------------------------------------------------------------------
    // getInvoiceStatus — status extraction from the various payload shapes
    // -------------------------------------------------------------------------

    #[Test]
    public function it_interpolates_the_external_id_into_the_status_endpoint(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient([
            $this->envelope(['response' => ['client_invoice' => ['status' => 'sent']]]),
        ]);

        /* Act */
        $result = $client->getInvoiceStatus('ci 99/1');

        /* Assert */
        self::assertSame(
            'https://thirdparty.qonto.com/v2/client_invoices/ci+99%2F1',
            $http->requestLog[0]['url']
        );
        self::assertSame('ci 99/1', $result['external_id']);
        self::assertSame('sent', $result['status']);
    }

    #[Test]
    public function it_reads_status_from_the_json_api_attributes_shape(): void
    {
        /* Arrange */
        [$client] = $this->makeClient([
            $this->envelope(['response' => ['data' => ['attributes' => ['status' => 'paid']]]]),
        ]);

        /* Act */
        $result = $client->getInvoiceStatus('ci-1');

        /* Assert */
        self::assertSame('paid', $result['status']);
    }

    #[Test]
    public function it_falls_back_to_the_envelope_status_when_body_has_no_status(): void
    {
        /* Arrange */
        [$client] = $this->makeClient([
            $this->envelope(['status' => 'sent', 'response' => []]),
        ]);

        /* Act */
        $result = $client->getInvoiceStatus('ci-1');

        /* Assert */
        self::assertSame('sent', $result['status']);
    }

    // -------------------------------------------------------------------------
    // sendInvoice — full upload -> create -> send orchestration
    // -------------------------------------------------------------------------

    #[Test]
    public function it_sends_an_invoice_through_the_upload_create_send_pipeline(): void
    {
        /* Arrange */
        $document        = $this->tempPdf();
        [$client, $http] = $this->makeClient([
            $this->envelope(['response' => ['data' => ['id' => 'upload-1']]]),
            $this->envelope(['response' => ['client_invoice' => ['id' => 'ci-7']]]),
            $this->envelope(['status' => 'sent']),
        ]);

        $metadata = ['qonto_invoice_payload' => ['client_invoice' => ['number' => 'INV-1']]];

        /* Act */
        $result = $client->sendInvoice($document, $metadata);

        /* Assert */
        /* three HTTP calls in order */
        self::assertCount(3, $http->requestLog);
        self::assertTrue($http->requestLog[0]['multipart']);
        self::assertSame(RequestMethod::POST, $http->requestLog[1]['method']);

        /* the create call carries the upload id inside the invoice payload */
        self::assertSame('upload-1', $http->requestLog[1]['payload']['upload_id']);

        /* the send call targets the created client invoice */
        self::assertSame(
            'https://thirdparty.qonto.com/v2/client_invoices/ci-7/send_by_einvoice',
            $http->requestLog[2]['url']
        );

        /* final envelope reflects the created invoice */
        self::assertTrue($result['success']);
        self::assertSame('ci-7', $result['external_id']);
        self::assertSame('upload-1', $result['request']['upload_id']);
        self::assertSame('ci-7', $result['request']['client_invoice_id']);
    }

    #[Test]
    public function it_throws_when_the_document_to_send_is_missing(): void
    {
        /* Arrange */
        [$client] = $this->makeClient();

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invoice document not found');

        /* Act */
        $client->sendInvoice('/no/such/file.pdf', []);
    }

    #[Test]
    public function it_stops_after_upload_when_invoice_payload_metadata_is_missing(): void
    {
        /* Arrange */
        $document        = $this->tempPdf();
        [$client, $http] = $this->makeClient([
            $this->envelope(['response' => ['data' => ['id' => 'upload-1']]]),
        ]);

        /* Act */
        $result = $client->sendInvoice($document, []);

        /* Assert */
        /* only the upload call was made, and an error is returned */
        self::assertCount(1, $http->requestLog);
        self::assertFalse($result['success']);
        self::assertStringContainsString('qonto_invoice_payload', $result['message']);
    }

    #[Test]
    public function it_fails_when_the_upload_returns_no_upload_id(): void
    {
        /* Arrange */
        $document        = $this->tempPdf();
        [$client, $http] = $this->makeClient([
            $this->envelope(['response' => ['data' => []]]),
        ]);

        /* Act */
        $result = $client->sendInvoice($document, ['qonto_invoice_payload' => ['x' => 1]]);

        /* Assert */
        self::assertCount(1, $http->requestLog);
        self::assertFalse($result['success']);
        self::assertSame('error', $result['status']);
        self::assertStringContainsString('no upload ID', $result['message']);
    }

    // -------------------------------------------------------------------------
    // requireSetting — missing endpoint guards
    // -------------------------------------------------------------------------

    #[Test]
    public function it_requires_the_status_endpoint_setting(): void
    {
        /* Arrange */
        [$client] = $this->makeClient([], $this->settings(['invoice_status_endpoint' => '']));

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: invoice_status_endpoint');

        /* Act */
        $client->getInvoiceStatus('ci-1');
    }

    // -------------------------------------------------------------------------
    // buildInvoicePayload — maps InvoicePlane objects to the Qonto shape
    // -------------------------------------------------------------------------

    #[Test]
    public function it_builds_the_qonto_invoice_payload_from_invoice_and_items(): void
    {
        /* Arrange */
        $client  = new QontoClient(new ApiClientFake());
        $invoice = (object) [
            'invoice_number'       => 'INV-42',
            'invoice_date_created' => '2026-01-01',
            'invoice_date_due'     => '2026-01-31',
            'client_currency'      => 'USD',
            'client_name'          => 'Acme Ltd',
            'client_email'         => 'billing@acme.test',
        ];
        $items = [
            (object) [
                'item_name'        => 'Widget',
                'item_description' => 'A widget',
                'item_quantity'    => '2',
                'item_price'       => '9.5',
            ],
        ];

        /* Act */
        $metadata = $client->buildInvoicePayload($invoice, $items, ['keep' => 'me']);
        $payload  = $metadata['qonto_invoice_payload']['client_invoice'];

        /* Assert */
        self::assertSame('me', $metadata['keep']);
        self::assertSame('INV-42', $payload['number']);
        self::assertSame('USD', $payload['currency']);
        self::assertSame('Acme Ltd', $payload['client']['name']);
        self::assertSame(2.0, $payload['line_items'][0]['quantity']);
        self::assertSame(9.5, $payload['line_items'][0]['unit_price']);
    }

    #[Test]
    public function it_defaults_the_invoice_currency_to_eur_when_missing(): void
    {
        /* Arrange */
        $client  = new QontoClient(new ApiClientFake());
        $invoice = (object) [
            'invoice_number'       => 'INV-1',
            'invoice_date_created' => '2026-01-01',
            'invoice_date_due'     => '2026-01-31',
            'client_currency'      => '',
            'client_name'          => 'Acme',
            'client_email'         => 'a@b.test',
        ];

        /* Act */
        $metadata = $client->buildInvoicePayload($invoice, []);

        /* Assert */
        self::assertSame('EUR', $metadata['qonto_invoice_payload']['client_invoice']['currency']);
    }

    private function settings(array $overrides = []): array
    {
        return array_merge(QontoClient::defaultSettings(), [
            'access_token' => 'qonto-token',
            'api_base_url' => 'https://thirdparty.qonto.com',
        ], $overrides);
    }

    /**
     * @param array<int, array<string, mixed>> $responses
     *
     * @return array{0: QontoClient, 1: ApiClientFake}
     */
    private function makeClient(array $responses = [], array $settings = []): array
    {
        $http   = new ApiClientFake($responses);
        $client = new QontoClient($http);
        $client->authenticate($settings ?: $this->settings());

        return [$client, $http];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /**
     * Build a response envelope, overriding only the keys a test cares about.
     *
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function envelope(array $overrides = []): array
    {
        return array_merge([
            'success'     => true,
            'external_id' => null,
            'status'      => 'ok',
            'message'     => 'ok',
            'http_code'   => 200,
            'request'     => [],
            'response'    => [],
        ], $overrides);
    }

    private function tempPdf(): string
    {
        $path = tempnam(sys_get_temp_dir(), 'qonto_') . '.pdf';
        file_put_contents($path, '%PDF-1.4 test');
        $this->tempFiles[] = $path;

        return $path;
    }
}
