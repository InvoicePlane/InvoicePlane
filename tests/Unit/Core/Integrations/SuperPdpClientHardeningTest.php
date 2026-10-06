<?php

namespace Tests\Unit\Core\Integrations;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RequestMethod;
use RuntimeException;
use SuperPdpClient;
use Tests\Fakes\Integration\ApiClientFake;

/**
 * The SuperPDP paths SuperPdpClientTest does not exercise: upload guards and query building,
 * response normalisation, inline and remote document download, and the list endpoints.
 */
#[Group('unit')]
final class SuperPdpClientHardeningTest extends TestCase
{
    private const MEBIBYTE = 1024 * 1024;

    /** @var list<string> */
    private array $tempFiles = [];

    protected function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            @unlink($file);
        }
        $this->tempFiles = [];
    }

    /** @return array<string, array{array<string, string>, string}> */
    public static function incompleteConfigurations(): array
    {
        return [
            'no base url'         => [['api_base_url' => ''], 'Missing SuperPDP API base URL.'],
            'no invoice endpoint' => [['invoice_endpoint' => ''], 'Missing invoice endpoint configuration.'],
        ];
    }

    /** @return array<string, array{array<string, mixed>, array<string, string>}> */
    public static function externalIdSources(): array
    {
        return [
            'explicit external_id wins' => [['external_id' => 'ext-9', 'invoice_id' => 5], ['external_id' => 'ext-9']],
            'invoice id as fallback'    => [['invoice_id' => 5], ['external_id' => '5']],
            'empty string is omitted'   => [['external_id' => '', 'invoice_id' => 5], []],
            'array is omitted'          => [['external_id' => ['x']], []],
            'nothing given'             => [[], []],
        ];
    }

    /** @return array<string, array{array<string, mixed>}> */
    public static function invalidDocumentIds(): array
    {
        return ['absent' => [[]], 'empty' => [['id' => '']], 'numeric' => [['id' => 12]]];
    }

    // ---- sendInvoice ---------------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_path_that_is_not_a_regular_file(): void
    {
        [$client, $http] = $this->client();
        $directory       = sys_get_temp_dir() . '/superpdp-dir-' . bin2hex(random_bytes(4));
        mkdir($directory);

        try {
            $client->sendInvoice($directory, []);
            self::fail('A directory is not an invoice document.');
        } catch (RuntimeException $e) {
            self::assertSame('Invoice document not found: ' . $directory, $e->getMessage());
        } finally {
            rmdir($directory);
        }
        self::assertSame([], $http->requestLog);
    }

    #[Test]
    #[DataProvider('incompleteConfigurations')]
    public function it_refuses_to_upload_with_an_incomplete_configuration(array $override, string $message): void
    {
        [$client, $http] = $this->client([], $override);

        try {
            $client->sendInvoice($this->tempPdf(), []);
            self::fail('An incomplete configuration must not upload.');
        } catch (RuntimeException $e) {
            self::assertSame($message, $e->getMessage());
        }
        self::assertSame([], $http->requestLog);
    }

    #[Test]
    public function it_refuses_to_upload_without_an_access_token(): void
    {
        $http   = new ApiClientFake([], []);
        $client = new SuperPdpClient($http);
        try {
            $client->authenticate($this->settings());
            self::fail('A token response without access_token must fail authentication.');
        } catch (RuntimeException $e) {
            self::assertSame('SuperPDP OAuth failed: no access_token in response.', $e->getMessage());
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing SuperPDP access token.');

        $client->sendInvoice($this->tempPdf(), []);
    }

    #[Test]
    public function it_uploads_the_raw_pdf_as_the_body_with_bearer_and_content_type(): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['invoice' => ['id' => 'sp-1']]])]);
        $path            = $this->tempPdf('%PDF-1.4 body');

        $result = $client->sendInvoice($path, ['invoice_id' => 42]);

        $call = $http->requestLog[0];
        self::assertSame(RequestMethod::POST, $call['method']);
        self::assertSame('https://api.superpdp.tech/v1.beta/invoices', $call['url']);
        self::assertSame('fake-token', $call['options']['bearer']);
        self::assertSame('%PDF-1.4 body', $call['options']['body']);
        self::assertSame(['Content-Type: application/pdf'], $call['options']['headers']);
        self::assertSame(['external_id' => '42'], $call['options']['query']);
        self::assertTrue($result['success']);
        self::assertSame('sp-1', $result['external_id']);
    }

    #[Test]
    #[DataProvider('externalIdSources')]
    public function it_sends_the_caller_reference_as_external_id_only_when_it_is_a_non_empty_scalar(array $metadata, array $expectedQuery): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['id' => 'sp-1']])]);

        $client->sendInvoice($this->tempPdf(), $metadata);

        self::assertSame($expectedQuery, $http->requestLog[0]['options']['query']);
    }

    #[Test]
    public function it_asks_the_provider_to_skip_its_pre_check_only_when_configured(): void
    {
        [$on, $onHttp]   = $this->client([$this->envelope(['response' => ['id' => 'a']])], ['disable_pre_check' => true]);
        [$off, $offHttp] = $this->client([$this->envelope(['response' => ['id' => 'b']])], ['disable_pre_check' => false]);

        $on->sendInvoice($this->tempPdf(), []);
        $off->sendInvoice($this->tempPdf(), []);

        self::assertSame(['disable_pre_check' => true], $onHttp->requestLog[0]['options']['query']);
        self::assertSame([], $offHttp->requestLog[0]['options']['query']);
    }

    #[Test]
    public function it_turns_an_accepted_upload_without_an_id_into_an_error(): void
    {
        [$client] = $this->client([$this->envelope(['response' => ['invoice' => []]])]);

        $result = $client->sendInvoice($this->tempPdf(), []);

        self::assertFalse($result['success']);
        self::assertSame('error', $result['status']);
        self::assertSame('SuperPDP accepted the invoice but returned no external ID.', $result['message']);
    }

    #[Test]
    public function it_passes_a_provider_rejection_through_unchanged(): void
    {
        [$client] = $this->client([$this->envelope(['success' => false, 'http_code' => 422, 'message' => 'Invalid invoice'])]);

        $result = $client->sendInvoice($this->tempPdf(), []);

        self::assertFalse($result['success']);
        self::assertSame(422, $result['http_code']);
        self::assertSame('Invalid invoice', $result['message']);
    }

    // ---- status --------------------------------------------------------------------------------

    #[Test]
    public function it_requires_the_status_endpoint_and_url_encodes_the_external_id(): void
    {
        [$unconfigured] = $this->client([], ['invoice_status_endpoint' => '']);
        try {
            $unconfigured->getInvoiceStatus('x');
            self::fail('The status endpoint is required.');
        } catch (RuntimeException $e) {
            self::assertSame('Missing invoice status endpoint configuration.', $e->getMessage());
        }

        [$client, $http] = $this->client([$this->envelope(['response' => ['status' => 'sent']])]);
        $result          = $client->getInvoiceStatus('a/b');

        self::assertSame('https://api.superpdp.tech/v1.beta/invoices/a%2Fb', $http->requestLog[0]['url']);
        self::assertSame(RequestMethod::GET, $http->requestLog[0]['method']);
        self::assertFalse($http->requestLog[0]['multipart'], 'A status lookup carries no upload.');
        self::assertSame('a/b', $result['external_id'], 'The caller id is reported, not one parsed from the response.');
        self::assertSame('sent', $result['status']);
    }

    #[Test]
    public function it_echoes_the_looked_up_id_into_the_recorded_request(): void
    {
        [$client] = $this->client([$this->envelope(['request' => ['url' => 'u'], 'response' => ['status' => 'sent']])]);

        $result = $client->getInvoiceStatus('ext-7');

        self::assertSame(['url' => 'u', 'external_id' => 'ext-7'], $result['request']);
    }

    // ---- list endpoints ------------------------------------------------------------------------

    #[Test]
    public function it_normalises_incoming_invoices_from_each_documented_collection_shape(): void
    {
        $invoice = [['id' => 'i1'], ['id' => 'i2']];
        foreach ([['data' => $invoice], ['invoices' => $invoice], ['items' => $invoice], $invoice] as $payload) {
            [$client] = $this->client([$this->envelope(['response' => $payload])]);

            self::assertSame($invoice, $client->receiveInvoices()['response']['invoices']);
        }
    }

    #[Test]
    public function it_passes_filters_to_the_incoming_list_and_requires_its_endpoint(): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['data' => []]])]);
        $client->receiveInvoices(['limit' => 3]);
        self::assertSame('https://api.superpdp.tech/v1.beta/invoices?limit=3', $http->requestLog[0]['url']);

        [$unconfigured] = $this->client([], ['incoming_invoices_endpoint' => '']);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing incoming invoices endpoint configuration.');
        $unconfigured->receiveInvoices();
    }

    #[Test]
    public function it_normalises_events_and_requires_the_events_endpoint(): void
    {
        $events          = [['id' => 'e1']];
        [$client, $http] = $this->client([$this->envelope(['response' => ['events' => $events]])]);

        self::assertSame($events, $client->getInvoiceEvents(['since' => '2026-01-01'])['response']['events']);
        self::assertSame('https://api.superpdp.tech/v1.beta/invoice_events?since=2026-01-01', $http->requestLog[0]['url']);

        [$unconfigured] = $this->client([], ['invoice_events_endpoint' => '']);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing invoice events endpoint configuration.');
        $unconfigured->getInvoiceEvents();
    }

    // ---- document download ---------------------------------------------------------------------

    #[Test]
    public function it_decodes_an_inline_document_without_any_http_call(): void
    {
        [$client, $http] = $this->client();

        $result = $client->downloadInvoiceDocument(['content_base64' => base64_encode('<Invoice/>'), 'filename' => 'in.xml', 'mime_type' => 'text/xml']);

        self::assertSame([], $http->requestLog);
        self::assertTrue($result['success']);
        self::assertSame('<Invoice/>', $result['content']);
        self::assertSame('in.xml', $result['filename']);
        self::assertSame('text/xml', $result['mime_type']);
        self::assertSame('Inline SuperPDP document decoded.', $result['message']);
        self::assertSame(200, $result['http_code']);
    }

    #[Test]
    public function it_prefers_content_base64_then_document_content_then_content_and_defaults_the_name_and_type(): void
    {
        [$client] = $this->client();

        $first  = $client->downloadInvoiceDocument(['content_base64' => base64_encode('one'), 'document_content' => base64_encode('two'), 'content' => base64_encode('three')]);
        $second = $client->downloadInvoiceDocument(['document_content' => base64_encode('two'), 'content' => base64_encode('three'), 'file_name' => 'fn.xml']);
        $third  = $client->downloadInvoiceDocument(['content' => base64_encode('three')]);

        self::assertSame('one', $first['content']);
        self::assertSame('superpdp-invoice.xml', $first['filename']);
        self::assertSame('application/xml', $first['mime_type']);
        self::assertSame('two', $second['content']);
        self::assertSame('fn.xml', $second['filename']);
        self::assertSame('three', $third['content']);
    }

    #[Test]
    public function it_rejects_an_inline_document_that_is_not_valid_base64(): void
    {
        [$client] = $this->client();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('SuperPDP incoming document is not valid base64.');

        $client->downloadInvoiceDocument(['content_base64' => '***not base64***']);
    }

    #[Test]
    public function it_downloads_a_remote_document_with_bearer_binary_mode_and_a_size_cap(): void
    {
        [$client, $http] = $this->client([$this->envelope(['body' => '%PDF', 'content_type' => 'application/pdf', 'message' => 'fetched'])]);

        $result = $client->downloadInvoiceDocument(['id' => 'doc/1', 'file_name' => 'remote.pdf', 'mime_type' => 'application/x-ignored']);

        $call = $http->requestLog[0];
        self::assertSame('https://api.superpdp.tech/v1.beta/invoices/doc%2F1/document', $call['url']);
        self::assertSame('fake-token', $call['options']['bearer']);
        self::assertTrue($call['options']['binary']);
        self::assertSame(15 * self::MEBIBYTE, $call['options']['max_response_bytes']);
        self::assertSame('%PDF', $result['content']);
        self::assertSame('remote.pdf', $result['filename']);
        self::assertSame('application/pdf', $result['mime_type'], 'The transport type wins over the listed one.');
        self::assertSame('fetched', $result['message']);
        self::assertSame(['document_id' => 'doc/1'], $result['response']);
    }

    #[Test]
    public function it_takes_the_document_id_from_document_id_then_external_id_then_id_and_defaults_the_name(): void
    {
        [$client, $http] = $this->client([$this->envelope(['body' => 'a']), $this->envelope(['body' => 'b']), $this->envelope(['body' => 'c', 'content_type' => null])]);

        $client->downloadInvoiceDocument(['document_id' => 'd1', 'external_id' => 'e1', 'id' => 'i1']);
        $client->downloadInvoiceDocument(['external_id' => 'e2', 'id' => 'i2']);
        $last = $client->downloadInvoiceDocument(['id' => 'i3', 'mime_type' => 'application/pdf']);

        self::assertStringEndsWith('/invoices/d1/document', $http->requestLog[0]['url']);
        self::assertStringEndsWith('/invoices/e2/document', $http->requestLog[1]['url']);
        self::assertStringEndsWith('/invoices/i3/document', $http->requestLog[2]['url']);
        self::assertSame('superpdp-invoice.pdf', $last['filename']);
        self::assertSame('application/pdf', $last['mime_type'], 'The listed type is used when the transport reports none.');
    }

    #[Test]
    #[DataProvider('invalidDocumentIds')]
    public function it_refuses_an_incoming_invoice_without_a_usable_document_id(array $invoice): void
    {
        [$client, $http] = $this->client();

        try {
            $client->downloadInvoiceDocument($invoice);
            self::fail('A document id is required.');
        } catch (RuntimeException $e) {
            self::assertSame('SuperPDP incoming invoice has no document ID.', $e->getMessage());
        }
        self::assertSame([], $http->requestLog);
    }

    #[Test]
    public function it_requires_the_document_endpoint_for_remote_downloads(): void
    {
        [$client] = $this->client([], ['incoming_document_endpoint' => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing incoming document endpoint configuration.');

        $client->downloadInvoiceDocument(['id' => 'x']);
    }

    // ---- helpers -------------------------------------------------------------------------------

    /**
     * @param array<int, array<string, mixed>> $responses
     * @param array<string, mixed>             $overrides
     *
     * @return array{0: SuperPdpClient, 1: ApiClientFake}
     */
    private function client(array $responses = [], array $overrides = []): array
    {
        $http   = new ApiClientFake($responses);
        $client = new SuperPdpClient($http);
        $client->authenticate($this->settings($overrides));

        return [$client, $http];
    }

    /**
     * @param array<string, mixed> $overrides
     *
     * @return array<string, mixed>
     */
    private function settings(array $overrides = []): array
    {
        return array_replace(SuperPdpClient::defaultSettings(), ['client_id' => 'cid', 'client_secret' => 'secret'], $overrides);
    }

    /**
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

    private function tempPdf(string $content = '%PDF-1.4'): string
    {
        $path = tempnam(sys_get_temp_dir(), 'superpdp_') . '.pdf';
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;

        return $path;
    }
}
