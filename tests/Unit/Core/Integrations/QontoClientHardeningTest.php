<?php

namespace Tests\Unit\Core\Integrations;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use QontoClient;
use RemoteUrlGuard;
use RuntimeException;
use Tests\Fakes\Integration\ApiClientFake;

/**
 * The Qonto paths QontoClientTest does not exercise: upload guards, status mapping from lifecycle
 * events, incoming/outgoing event extraction and the attachment download (size limits, URL
 * pinning, metadata shapes). Every expected value is derived from the Qonto contract, not copied
 * from the implementation.
 */
#[Group('unit')]
final class QontoClientHardeningTest extends TestCase
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

    /** @return array<string, array{string}> */
    public static function sendEndpointSettings(): array
    {
        return ['import_endpoint' => ['import_endpoint'], 'send_invoice_endpoint' => ['send_invoice_endpoint']];
    }

    /** @return array<string, array{array<string, mixed>}> */
    public static function invalidAttachmentIds(): array
    {
        return ['absent' => [[]], 'empty' => [['attachment_id' => '']], 'not a string' => [['attachment_id' => 12]], 'null display id' => [['display_attachment_id' => null]]];
    }

    /** @return array<string, array{array<string, mixed>}> */
    public static function metadataShapes(): array
    {
        $url = ['url' => 'https://files.qonto.test/doc.pdf'];

        return [
            'attachment'      => [['attachment' => $url]],
            'data.attributes' => [['data' => ['attributes' => $url]]],
            'data'            => [['data' => $url]],
        ];
    }

    // ---- sendInvoice guards --------------------------------------------------------------------

    #[Test]
    public function it_rejects_a_path_that_is_not_a_regular_file(): void
    {
        [$client, $http] = $this->client();
        $directory       = sys_get_temp_dir() . '/qonto-dir-' . bin2hex(random_bytes(4)) . '.pdf';
        mkdir($directory);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invoice document not found: ' . $directory);

        try {
            $client->sendInvoice($directory, []);
        } finally {
            rmdir($directory);
            self::assertSame([], $http->requestLog);
        }
    }

    #[Test]
    public function it_rejects_a_document_that_is_not_a_pdf(): void
    {
        [$client, $http] = $this->client();

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Qonto e-invoice imports require a Factur-X PDF document.');

        try {
            $client->sendInvoice($this->tempFile('invoice.xml', '<Invoice/>'), []);
        } finally {
            self::assertSame([], $http->requestLog, 'Nothing may be uploaded.');
        }
    }

    #[Test]
    public function it_accepts_an_upper_case_pdf_extension(): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['client_invoices' => [['invoice_id' => 'ci-1']]]]), $this->envelope()]);

        $result = $client->sendInvoice($this->tempFile('INVOICE.PDF', '%PDF-1.4'), []);

        self::assertTrue($result['success']);
        self::assertCount(2, $http->requestLog);
    }

    #[Test]
    public function it_rejects_a_document_over_five_megabytes(): void
    {
        [$client, $http] = $this->client();
        $path            = $this->sparseFile('big.pdf', 5 * self::MEBIBYTE + 1);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Qonto e-invoice imports are limited to 5 MB per document.');

        try {
            $client->sendInvoice($path, []);
        } finally {
            self::assertSame([], $http->requestLog);
        }
    }

    #[Test]
    public function it_accepts_a_document_of_exactly_five_megabytes(): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['client_invoices' => [['invoice_id' => 'ci-2']]]]), $this->envelope()]);

        $result = $client->sendInvoice($this->sparseFile('limit.pdf', 5 * self::MEBIBYTE), []);

        self::assertTrue($result['success']);
        self::assertCount(2, $http->requestLog);
    }

    #[Test]
    #[DataProvider('sendEndpointSettings')]
    public function it_requires_both_send_endpoints_before_uploading(string $missing): void
    {
        [$client, $http] = $this->client([], [$missing => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: ' . $missing);

        try {
            $client->sendInvoice($this->tempFile('a.pdf', '%PDF'), []);
        } finally {
            self::assertSame([], $http->requestLog, 'No request is made with an incomplete configuration.');
        }
    }

    #[Test]
    public function it_keeps_the_provider_failure_and_does_not_report_pending_when_the_send_step_fails(): void
    {
        [$client] = $this->client([
            $this->envelope(['response' => ['client_invoices' => [['invoice_id' => 'ci-3']]]]),
            $this->envelope(['success' => false, 'status' => 'error', 'message' => 'Recipient rejected', 'http_code' => 422]),
        ]);

        $result = $client->sendInvoice($this->tempFile('a.pdf', '%PDF'), ['invoice_id' => 9]);

        self::assertFalse($result['success']);
        self::assertSame('error', $result['status']);
        self::assertSame('Recipient rejected', $result['message']);
        self::assertSame('ci-3', $result['external_id'], 'The imported invoice id is still reported so the operator can find it.');
        self::assertSame(9, $result['request']['invoice_id']);
    }

    #[Test]
    public function it_posts_the_send_by_einvoice_step_with_no_body(): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['client_invoices' => [['invoice_id' => 'ci-4']]]]), $this->envelope()]);

        $client->sendInvoice($this->tempFile('a.pdf', '%PDF'), []);

        self::assertTrue($http->requestLog[0]['multipart'], 'The import step uploads the PDF.');
        self::assertFalse($http->requestLog[1]['multipart'], 'The send step has no upload.');
        self::assertSame([], $http->requestLog[1]['payload']);
        self::assertArrayNotHasKey('json', $http->requestLog[1]['options']);
    }

    #[Test]
    public function it_url_encodes_the_client_invoice_id_in_the_send_url(): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['client_invoices' => [['invoice_id' => 'a/b c']]]]), $this->envelope()]);

        $client->sendInvoice($this->tempFile('a.pdf', '%PDF'), []);

        self::assertSame('https://thirdparty.qonto.com/v2/client_invoices/a%2Fb+c/send_by_einvoice', $http->requestLog[1]['url']);
    }

    // ---- status mapping ------------------------------------------------------------------------

    #[Test]
    public function it_takes_status_and_message_from_the_latest_lifecycle_event(): void
    {
        [$client] = $this->client([$this->envelope([
            'status'   => 'envelope-status',
            'message'  => 'envelope message',
            'response' => ['client_invoice' => [
                'status'                      => 'pending',
                'einvoicing_lifecycle_events' => [
                    ['timestamp' => '2026-03-02T10:00:00Z', 'status_code' => '203', 'reason_message' => 'Delivered'],
                    ['timestamp' => '2026-03-01T10:00:00Z', 'status_code' => '200', 'reason_message' => 'Deposited'],
                ],
            ]],
        ])]);

        $result = $client->getInvoiceStatus('ci-9');

        self::assertSame('203', $result['status']);
        self::assertSame('203', $result['status_code']);
        self::assertSame('Delivered', $result['message']);
        self::assertSame('ci-9', $result['external_id']);
    }

    #[Test]
    public function it_prefers_the_event_reason_when_it_has_no_reason_message_and_the_later_of_equal_timestamps(): void
    {
        [$client] = $this->client([$this->envelope([
            'response' => ['client_invoice' => ['einvoicing_lifecycle_events' => [
                ['timestamp' => '2026-03-01T10:00:00Z', 'status_code' => '200', 'reason' => 'first'],
                ['timestamp' => '2026-03-01T10:00:00Z', 'status_code' => '201', 'reason' => 'second'],
                'not-an-event',
            ]]],
        ])]);

        $result = $client->getInvoiceStatus('ci-9');

        self::assertSame('201', $result['status_code']);
        self::assertSame('second', $result['message']);
    }

    #[Test]
    public function it_does_not_let_an_event_without_a_timestamp_replace_a_timestamped_one(): void
    {
        [$client] = $this->client([$this->envelope([
            'response' => ['client_invoice' => ['einvoicing_lifecycle_events' => [
                ['timestamp' => '2026-03-01T10:00:00Z', 'status_code' => '200'],
                ['status_code' => '999'],
            ]]],
        ])]);

        self::assertSame('200', $this->statusCodeOf($client));
    }

    #[Test]
    public function it_ignores_lifecycle_entries_that_are_not_events(): void
    {
        [$client] = $this->client([$this->envelope(['status' => 'env', 'message' => 'envelope message', 'response' => ['client_invoice' => ['einvoicing_lifecycle_events' => ['junk']]]])]);

        $result = $client->getInvoiceStatus('x');

        self::assertSame('env', $result['status']);
        self::assertNull($result['status_code']);
        self::assertSame('envelope message', $result['message']);
    }

    #[Test]
    public function it_falls_back_through_einvoicing_status_status_and_the_envelope_without_events(): void
    {
        [$a] = $this->client([$this->envelope(['status' => 'env', 'response' => ['client_invoice' => ['einvoicing_status' => 'einv', 'status' => 'plain']]])]);
        [$b] = $this->client([$this->envelope(['status' => 'env', 'response' => ['client_invoice' => ['status' => 'plain']]])]);
        [$c] = $this->client([$this->envelope(['status' => 'env', 'message' => 'envelope message', 'response' => ['client_invoice' => []]])]);

        self::assertSame('einv', $a->getInvoiceStatus('x')['status']);
        self::assertSame('plain', $b->getInvoiceStatus('x')['status']);
        $fallback = $c->getInvoiceStatus('x');
        self::assertSame('env', $fallback['status']);
        self::assertNull($fallback['status_code']);
        self::assertSame('envelope message', $fallback['message'], 'Without lifecycle events the envelope message is kept.');
    }

    #[Test]
    public function it_url_encodes_the_external_id_in_the_status_url(): void
    {
        [$client, $http] = $this->client([$this->envelope()]);

        $client->getInvoiceStatus('a/b');

        self::assertSame('https://thirdparty.qonto.com/v2/client_invoices/a%2Fb', $http->requestLog[0]['url']);
    }

    // ---- incoming invoices and events ----------------------------------------------------------

    #[Test]
    public function it_exposes_supplier_invoices_under_the_invoices_key_and_requires_the_endpoint(): void
    {
        [$client] = $this->client([$this->envelope(['response' => ['supplier_invoices' => [['id' => 's1'], ['id' => 's2']]]])]);

        self::assertSame([['id' => 's1'], ['id' => 's2']], $client->receiveInvoices()['response']['invoices']);

        [$unconfigured] = $this->client([], ['incoming_invoices_endpoint' => '']);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: incoming_invoices_endpoint');
        $unconfigured->receiveInvoices();
    }

    #[Test]
    public function it_does_not_invent_an_invoices_list_when_the_response_has_none(): void
    {
        [$client] = $this->client([$this->envelope(['response' => ['supplier_invoices' => 'oops']])]);

        self::assertArrayNotHasKey('invoices', $client->receiveInvoices()['response']);
    }

    #[Test]
    public function it_flattens_lifecycle_events_of_every_client_invoice_and_tags_them_with_the_invoice(): void
    {
        [$client, $http] = $this->client([$this->envelope(['response' => ['client_invoices' => [
            ['id' => 'ci-1', 'number' => 'F-1', 'einvoicing_lifecycle_events' => [['status_code' => '200'], 'junk', ['status_code' => '201']]],
            'not-an-invoice',
            ['id' => 'ci-2', 'einvoicing_lifecycle_events' => [['status_code' => '210']]],
            ['id' => 'ci-3'],
        ]]])]);

        $events = $client->getInvoiceEvents(['per_page' => 5])['response']['events'];

        self::assertSame([
            ['status_code' => '200', 'invoice_id' => 'ci-1', 'external_id' => 'ci-1', 'invoice_number' => 'F-1'],
            ['status_code' => '201', 'invoice_id' => 'ci-1', 'external_id' => 'ci-1', 'invoice_number' => 'F-1'],
            ['status_code' => '210', 'invoice_id' => 'ci-2', 'external_id' => 'ci-2', 'invoice_number' => null],
        ], $events);
        self::assertStringContainsString('exclude_imported=false', $http->requestLog[0]['url']);
        self::assertStringContainsString('per_page=5', $http->requestLog[0]['url'], 'Caller filters override the defaults.');
    }

    #[Test]
    public function it_asks_for_a_hundred_client_invoices_per_page_by_default(): void
    {
        [$client, $http] = $this->client([$this->envelope()]);

        $client->getInvoiceEvents();

        self::assertStringContainsString('per_page=100', $http->requestLog[0]['url']);
    }

    #[Test]
    public function it_returns_no_events_when_the_response_has_no_invoice_list_and_requires_the_endpoint(): void
    {
        [$client] = $this->client([$this->envelope(['response' => ['client_invoices' => 'oops']])]);
        self::assertSame([], $client->getInvoiceEvents()['response']['events']);

        [$unconfigured] = $this->client([], ['client_invoices_endpoint' => '']);
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: client_invoices_endpoint');
        $unconfigured->getInvoiceEvents();
    }

    // ---- attachment download -------------------------------------------------------------------

    #[Test]
    public function it_requires_the_attachment_endpoint(): void
    {
        [$client] = $this->client([], ['attachment_endpoint' => '']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: attachment_endpoint');

        $client->downloadInvoiceDocument(['attachment_id' => 'att-1']);
    }

    #[Test]
    #[DataProvider('invalidAttachmentIds')]
    public function it_refuses_an_invoice_without_a_usable_attachment_id(array $invoice): void
    {
        [$client, $http] = $this->client();

        try {
            $client->downloadInvoiceDocument($invoice);
            self::fail('An invoice without attachment id must be refused.');
        } catch (RuntimeException $e) {
            self::assertSame('Qonto supplier invoice has no downloadable attachment ID.', $e->getMessage());
            self::assertSame([], $http->requestLog);
        }
    }

    #[Test]
    public function it_downloads_through_the_pinned_address_and_maps_the_result(): void
    {
        [$client, $http] = $this->clientWithGuard([
            $this->envelope(['response' => ['attachment' => ['url' => 'https://files.qonto.test/doc.pdf', 'file_name' => 'meta.pdf', 'file_content_type' => 'application/pdf', 'file_size' => '1200']]]),
            $this->envelope(['body' => '%PDF-body', 'content_type' => 'application/octet-stream', 'message' => 'downloaded', 'http_code' => 200]),
        ], ['93.184.216.34']);

        $result = $client->downloadInvoiceDocument(['attachment_id' => 'att/1', 'file_name' => 'supplier.pdf']);

        self::assertSame('https://thirdparty.qonto.com/v2/attachments/att%2F1', $http->requestLog[0]['url']);
        [$download] = [$http->requestLog[1]];
        self::assertSame('https://files.qonto.test/doc.pdf', $download['url']);
        self::assertTrue($download['options']['binary']);
        self::assertSame(15 * self::MEBIBYTE, $download['options']['max_response_bytes']);
        self::assertSame(['files.qonto.test', 443, '93.184.216.34'], $download['options']['resolve']);
        self::assertTrue($result['success']);
        self::assertSame('%PDF-body', $result['content']);
        self::assertSame('supplier.pdf', $result['filename'], 'The invoice file name wins over the attachment metadata.');
        self::assertSame('application/pdf', $result['mime_type'], 'The declared type wins over the transport type.');
        self::assertSame('downloaded', $result['message']);
        self::assertSame(['attachment_id' => 'att/1'], $result['response']);
    }

    #[Test]
    public function it_falls_back_to_metadata_names_the_display_attachment_id_and_a_default_name(): void
    {
        [$client, $http] = $this->clientWithGuard([
            $this->envelope(['response' => ['attachment' => ['url' => 'https://files.qonto.test/doc.pdf', 'file_name' => 'meta.pdf']]]),
            $this->envelope(['body' => 'x', 'content_type' => 'application/pdf']),
            $this->envelope(['response' => ['attachment' => ['url' => 'https://files.qonto.test/doc.pdf']]]),
            $this->envelope(['body' => 'y']),
        ], ['93.184.216.34']);

        $named   = $client->downloadInvoiceDocument(['display_attachment_id' => 'att-2']);
        $default = $client->downloadInvoiceDocument(['attachment_id' => 'att-3']);

        self::assertStringEndsWith('/v2/attachments/att-2', $http->requestLog[0]['url']);
        self::assertSame('meta.pdf', $named['filename']);
        self::assertSame('application/pdf', $named['mime_type'], 'The transport type is used when metadata declares none.');
        self::assertSame('qonto-invoice.pdf', $default['filename']);
        self::assertNull($default['mime_type']);
    }

    #[Test]
    #[DataProvider('metadataShapes')]
    public function it_reads_the_attachment_url_from_each_documented_metadata_shape(array $response): void
    {
        [$client, $http] = $this->clientWithGuard([$this->envelope(['response' => $response]), $this->envelope(['body' => 'z'])], ['93.184.216.34']);

        $client->downloadInvoiceDocument(['attachment_id' => 'att-1']);

        self::assertSame('https://files.qonto.test/doc.pdf', $http->requestLog[1]['url']);
    }

    #[Test]
    public function it_returns_the_metadata_failure_with_empty_content_when_the_metadata_call_fails(): void
    {
        [$client, $http] = $this->clientWithGuard([$this->envelope(['success' => false, 'http_code' => 404, 'message' => 'not found'])], ['93.184.216.34']);

        $result = $client->downloadInvoiceDocument(['attachment_id' => 'att-1']);

        self::assertFalse($result['success']);
        self::assertSame(404, $result['http_code']);
        self::assertNull($result['content']);
        self::assertNull($result['filename']);
        self::assertNull($result['mime_type']);
        self::assertCount(1, $http->requestLog, 'No download is attempted.');
    }

    #[Test]
    public function it_rejects_malformed_attachment_metadata_and_a_missing_download_url(): void
    {
        [$malformed] = $this->clientWithGuard([$this->envelope(['response' => ['attachment' => 'text']])], ['93.184.216.34']);
        try {
            $malformed->downloadInvoiceDocument(['attachment_id' => 'a']);
            self::fail('Malformed metadata must be refused.');
        } catch (RuntimeException $e) {
            self::assertSame('Qonto attachment metadata is malformed.', $e->getMessage());
        }

        foreach ([['attachment' => []], ['attachment' => ['url' => '']], ['attachment' => ['url' => 5]]] as $response) {
            [$noUrl] = $this->clientWithGuard([$this->envelope(['response' => $response])], ['93.184.216.34']);
            try {
                $noUrl->downloadInvoiceDocument(['attachment_id' => 'a']);
                self::fail('A missing download URL must be refused.');
            } catch (RuntimeException $e) {
                self::assertSame('Qonto attachment metadata has no download URL.', $e->getMessage());
            }
        }
    }

    #[Test]
    public function it_rejects_an_attachment_declared_larger_than_fifteen_megabytes_but_accepts_exactly_fifteen(): void
    {
        [$tooBig, $bigHttp] = $this->clientWithGuard([$this->envelope(['response' => ['attachment' => ['url' => 'https://f.test/d', 'file_size' => (string) (15 * self::MEBIBYTE + 1)]]])], ['93.184.216.34']);
        try {
            $tooBig->downloadInvoiceDocument(['attachment_id' => 'a']);
            self::fail('An oversized attachment must be refused.');
        } catch (RuntimeException $e) {
            self::assertSame('Qonto attachment exceeds the 15 MB incoming-document limit.', $e->getMessage());
            self::assertCount(1, $bigHttp->requestLog, 'Nothing is downloaded.');
        }

        [$limit, $limitHttp] = $this->clientWithGuard([
            $this->envelope(['response' => ['attachment' => ['url' => 'https://f.test/d', 'file_size' => 15 * self::MEBIBYTE]]]),
            $this->envelope(['body' => 'ok']),
        ], ['93.184.216.34']);
        self::assertSame('ok', $limit->downloadInvoiceDocument(['attachment_id' => 'a'])['content']);
        self::assertCount(2, $limitHttp->requestLog);

        [$undeclared, $undeclaredHttp] = $this->clientWithGuard([
            $this->envelope(['response' => ['attachment' => ['url' => 'https://f.test/d', 'file_size' => 'unknown']]]),
            $this->envelope(['body' => 'ok']),
        ], ['93.184.216.34']);
        self::assertSame('ok', $undeclared->downloadInvoiceDocument(['attachment_id' => 'a'])['content']);
        self::assertCount(2, $undeclaredHttp->requestLog, 'An undeclared size is left to the transport limit.');
    }

    #[Test]
    public function it_refuses_a_download_url_that_resolves_to_a_private_address(): void
    {
        [$client, $http] = $this->clientWithGuard([$this->envelope(['response' => ['attachment' => ['url' => 'https://internal.test/d']]])], ['10.0.0.5']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provider document URL resolves to a non-public address.');

        try {
            $client->downloadInvoiceDocument(['attachment_id' => 'a']);
        } finally {
            self::assertCount(1, $http->requestLog, 'The private address is never contacted.');
        }
    }

    #[Test]
    public function it_refuses_a_non_https_download_url(): void
    {
        [$client] = $this->clientWithGuard([$this->envelope(['response' => ['attachment' => ['url' => 'http://files.qonto.test/d']]])], ['93.184.216.34']);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Provider document URL must be an HTTPS URL without credentials.');

        $client->downloadInvoiceDocument(['attachment_id' => 'a']);
    }

    // ---- helpers -------------------------------------------------------------------------------

    private function statusCodeOf(QontoClient $client): mixed
    {
        return $client->getInvoiceStatus('x')['status_code'];
    }

    /**
     * @param array<int, array<string, mixed>> $responses
     * @param array<string, mixed>             $overrides
     *
     * @return array{0: QontoClient, 1: ApiClientFake}
     */
    private function client(array $responses = [], array $overrides = []): array
    {
        $http   = new ApiClientFake($responses);
        $client = new QontoClient($http);
        $client->authenticate(array_replace(QontoClient::defaultSettings(), ['access_token' => 'qonto-token'], $overrides));

        return [$client, $http];
    }

    /**
     * @param array<int, array<string, mixed>> $responses
     * @param list<string>                     $resolvedIps
     *
     * @return array{0: QontoClient, 1: ApiClientFake}
     */
    private function clientWithGuard(array $responses, array $resolvedIps): array
    {
        $guard = new class ($resolvedIps) extends RemoteUrlGuard {
            /** @param list<string> $ips */
            public function __construct(private array $ips) {}

            protected function resolve(string $host): array
            {
                return $this->ips;
            }
        };
        $http   = new ApiClientFake($responses);
        $client = new QontoClient($http, $guard);
        $client->authenticate(array_replace(QontoClient::defaultSettings(), ['access_token' => 'qonto-token']));

        return [$client, $http];
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

    private function tempFile(string $name, string $content): string
    {
        $dir = sys_get_temp_dir() . '/qonto-hardening-' . bin2hex(random_bytes(4));
        mkdir($dir);
        $path = $dir . '/' . $name;
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;

        return $path;
    }

    private function sparseFile(string $name, int $bytes): string
    {
        $path   = $this->tempFile($name, '');
        $handle = fopen($path, 'cb');
        ftruncate($handle, $bytes);
        fclose($handle);

        return $path;
    }
}
