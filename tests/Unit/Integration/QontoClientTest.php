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
 * so a fake HTTP adapter lets us drive the import -> send-by-einvoice
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
        self::assertSame('qonto', QontoClient::clientCode());
        self::assertSame('Qonto PA', QontoClient::clientName());
    }

    #[Test]
    public function it_provides_default_settings_with_all_endpoints(): void
    {
        /* Act */
        $defaults = QontoClient::defaultSettings();

        /* Assert */
        self::assertArrayHasKey('access_token', $defaults);
        self::assertSame('https://thirdparty.qonto.com', $defaults['api_base_url']);
        self::assertSame('/v2/client_invoices/bulk', $defaults['import_endpoint']);
        self::assertSame('/v2/client_invoices', $defaults['client_invoices_endpoint']);
        self::assertSame('/v2/client_invoices/{id}/send_by_einvoice', $defaults['send_invoice_endpoint']);
        self::assertSame('/v2/supplier_invoices', $defaults['incoming_invoices_endpoint']);
    }

    // -------------------------------------------------------------------------
    // authenticate / fetchToken
    // -------------------------------------------------------------------------

    #[Test]
    public function it_authenticates_when_required_settings_are_present(): void
    {
        /* Arrange */
        $client = new QontoClient(new ApiClientFake());

        /* Act & Assert */
        self::assertTrue($client->authenticate($this->settings()));
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

        /* Assert — caller filters first, then the always-on e-invoicing scope */
        self::assertSame(RequestMethod::GET, $http->requestLog[0]['method']);
        self::assertSame(
            'https://thirdparty.qonto.com/v2/supplier_invoices?status=pending&page=2'
            . '&filter%5Bsource%5D%5B%5D=e_invoicing&per_page=100',
            $http->requestLog[0]['url']
        );
    }

    #[Test]
    public function it_always_scopes_incoming_invoices_to_the_e_invoicing_source(): void
    {
        /* Arrange */
        [$client, $http] = $this->makeClient();

        /* Act */
        $client->receiveInvoices();

        /* Assert */
        self::assertSame(
            'https://thirdparty.qonto.com/v2/supplier_invoices'
            . '?filter%5Bsource%5D%5B%5D=e_invoicing&per_page=100',
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
    public function it_sends_an_invoice_through_the_import_then_send_by_einvoice_pipeline(): void
    {
        /* Arrange */
        $document        = $this->tempPdf();
        [$client, $http] = $this->makeClient([
            $this->envelope(['response' => ['client_invoices' => [['invoice_id' => 'ci-7']]]]),
            $this->envelope(['status' => 'sent']),
        ]);

        /* Act */
        $result = $client->sendInvoice($document, ['invoice_id' => 42]);

        /* Assert — two HTTP calls: multipart bulk import, then send_by_einvoice */
        self::assertCount(2, $http->requestLog);
        self::assertTrue($http->requestLog[0]['multipart']);
        self::assertStringContainsString('/v2/client_invoices/bulk', $http->requestLog[0]['url']);
        self::assertSame(RequestMethod::POST, $http->requestLog[1]['method']);
        self::assertSame(
            'https://thirdparty.qonto.com/v2/client_invoices/ci-7/send_by_einvoice',
            $http->requestLog[1]['url']
        );

        /* final envelope reflects the imported client invoice */
        self::assertTrue($result['success']);
        self::assertSame('ci-7', $result['external_id']);
        self::assertSame('ci-7', $result['request']['client_invoice_id']);
        self::assertSame(42, $result['request']['invoice_id']);
        self::assertSame('pending', $result['status']);
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
    public function it_stops_after_import_when_no_client_invoice_id_is_returned(): void
    {
        /* Arrange */
        $document        = $this->tempPdf();
        [$client, $http] = $this->makeClient([
            $this->envelope(['response' => ['client_invoices' => [[]]]]),
        ]);

        /* Act */
        $result = $client->sendInvoice($document, []);

        /* Assert — only the import call was made, and an error is returned */
        self::assertCount(1, $http->requestLog);
        self::assertFalse($result['success']);
        self::assertSame('error', $result['status']);
        self::assertStringContainsString('no client invoice ID', $result['message']);
    }

    #[Test]
    public function it_returns_the_import_failure_without_attempting_send_by_einvoice(): void
    {
        /* Arrange */
        $document        = $this->tempPdf();
        [$client, $http] = $this->makeClient([
            $this->envelope(['success' => false, 'status' => 'error', 'message' => 'Unprocessable Factur-X', 'http_code' => 422]),
        ]);

        /* Act */
        $result = $client->sendInvoice($document, []);

        /* Assert */
        self::assertCount(1, $http->requestLog);
        self::assertFalse($result['success']);
        self::assertStringContainsString('Unprocessable', $result['message']);
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
    // buildInvoicePayload — the import path issues an already-rendered Factur-X
    // PDF, so this only threads the invoice number onto the metadata.
    // -------------------------------------------------------------------------

    #[Test]
    public function it_threads_the_invoice_number_onto_the_metadata(): void
    {
        /* Arrange */
        $client  = new QontoClient(new ApiClientFake());
        $invoice = (object) ['invoice_number' => 'INV-42'];

        /* Act */
        $metadata = $client->buildInvoicePayload($invoice, [], ['keep' => 'me']);

        /* Assert */
        self::assertSame('me', $metadata['keep']);
        self::assertSame('INV-42', $metadata['invoice_number']);
    }

    #[Test]
    public function it_sets_a_null_invoice_number_when_the_invoice_has_none(): void
    {
        /* Arrange */
        $client = new QontoClient(new ApiClientFake());

        /* Act */
        $metadata = $client->buildInvoicePayload((object) [], []);

        /* Assert */
        self::assertNull($metadata['invoice_number']);
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
