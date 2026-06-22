<?php

namespace Tests\Unit\Integration;

use PHPUnit\Framework\TestCase;
use QontoClient;
use RuntimeException;

/**
 * Stubs request() so no real HTTP is made.
 */
class FakeQontoClient extends QontoClient
{
    public array $requestLog = [];
    private array $responses;
    private int $callIndex = 0;

    public function __construct(array $responses = [])
    {
        $this->responses = $responses;
    }

    protected function request(
        \RequestMethod $method,
        string $url,
        array $payload = [],
        bool $multipart = false,
        array $requestDebug = []
    ): array {
        $this->requestLog[] = compact('method', 'url', 'payload', 'multipart', 'requestDebug');

        return $this->responses[$this->callIndex++] ?? [
            'success' => true,
            'external_id' => null,
            'status' => 'sent',
            'message' => 'ok',
            'http_code' => 200,
            'request' => ['url' => $url, 'method' => $method->value],
            'response' => [],
        ];
    }
}

class QontoClientTest extends TestCase
{
    private function defaultSettings(): array
    {
        return [
            'access_token' => 'test-token',
            'staging_token' => '',
            'api_base_url' => 'https://thirdparty.qonto.com',
            'upload_endpoint' => '/v2/client_invoices/uploads',
            'invoice_endpoint' => '/v2/client_invoices',
            'send_invoice_endpoint' => '/v2/client_invoices/{id}/send_by_einvoice',
            'invoice_status_endpoint' => '/v2/client_invoices/{id}',
            'incoming_invoices_endpoint' => '/v2/supplier_invoices',
            'invoice_events_endpoint' => '/v2/client_invoices/{id}',
        ];
    }

    // --- authenticate ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_accepts_valid_settings_on_authenticate(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient();

        /* Act */
        $result = $provider->authenticate($this->defaultSettings());

        /* Assert */
        $this->assertTrue($result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_access_token_is_missing(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient();
        $settings = $this->defaultSettings();
        $settings['access_token'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: access_token');

        /* Assert */
        $provider->authenticate($settings);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_authenticate_when_api_base_url_is_missing(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient();
        $settings = $this->defaultSettings();
        $settings['api_base_url'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing Qonto setting: api_base_url');

        /* Assert */
        $provider->authenticate($settings);
    }

    // --- sendInvoice ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_send_when_the_invoice_file_does_not_exist(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient();
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invoice document not found');

        /* Assert */
        $provider->sendInvoice('/nonexistent/invoice.pdf', []);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_an_error_when_the_upload_step_fails(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient([
            ['success' => false, 'external_id' => null, 'status' => 'error', 'message' => 'Upload failed', 'http_code' => 500, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $result = $provider->sendInvoice($tmp, []);
        unlink($tmp);

        /* Assert */
        $this->assertFalse($result['success']);
        $this->assertCount(1, $provider->requestLog);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_an_error_when_qonto_invoice_payload_is_absent(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient([
            ['success' => true, 'external_id' => 'upload-1', 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => ['data' => ['id' => 'upload-1']]],
        ]);
        $provider->authenticate($this->defaultSettings());
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');

        /* Act */
        $result = $provider->sendInvoice($tmp, []);
        unlink($tmp);

        /* Assert */
        $this->assertFalse($result['success']);
        $this->assertStringContainsString('qonto_invoice_payload', $result['message']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_sends_an_invoice_through_all_three_steps(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient([
            ['success' => true, 'external_id' => 'upload-1', 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => ['data' => ['id' => 'upload-1']]],
            ['success' => true, 'external_id' => 'cinv-1', 'status' => 'sent', 'message' => 'ok', 'http_code' => 201, 'request' => [], 'response' => ['client_invoice' => ['id' => 'cinv-1']]],
            ['success' => true, 'external_id' => 'cinv-1', 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());
        $tmp = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmp, '%PDF-1.4');
        $metadata = ['qonto_invoice_payload' => ['client_invoice' => ['number' => 'INV-001']]];

        /* Act */
        $result = $provider->sendInvoice($tmp, $metadata);
        unlink($tmp);

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertCount(3, $provider->requestLog);
        $this->assertSame('cinv-1', $result['external_id']);
    }

    // --- getInvoiceStatus ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_interpolates_the_invoice_id_into_the_status_url(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient([
            ['success' => true, 'external_id' => 'inv-99', 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => ['client_invoice' => ['status' => 'paid']]],
        ]);
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $result = $provider->getInvoiceStatus('inv-99');

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertStringContainsString('inv-99', $provider->requestLog[0]['url']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_rejects_get_status_when_endpoint_setting_is_missing(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient();
        $settings = $this->defaultSettings();
        $settings['invoice_status_endpoint'] = '';
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
        $provider = new FakeQontoClient([
            ['success' => true, 'external_id' => null, 'status' => 'received', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $provider->receiveInvoices(['status' => 'pending', 'page' => 2]);

        /* Assert */
        $this->assertStringContainsString('status=pending', $provider->requestLog[0]['url']);
        $this->assertStringContainsString('page=2', $provider->requestLog[0]['url']);
    }

    // --- getInvoiceEvents ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_fetches_invoice_events_with_a_get_request(): void
    {
        /* Arrange */
        $provider = new FakeQontoClient([
            ['success' => true, 'external_id' => null, 'status' => 'events_received', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => []],
        ]);
        $provider->authenticate($this->defaultSettings());

        /* Act */
        $provider->getInvoiceEvents();

        /* Assert */
        $this->assertSame(\RequestMethod::GET, $provider->requestLog[0]['method']);
    }

    // --- static metadata ---

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_qonto_as_the_provider_code(): void
    {
        /* Arrange */

        /* Act */
        $code = QontoClient::clientCode();

        /* Assert */
        $this->assertSame('qonto', $code);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_qonto_pa_as_the_provider_name(): void
    {
        /* Arrange */

        /* Act */
        $name = QontoClient::clientName();

        /* Assert */
        $this->assertSame('Qonto PA', $name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_includes_all_required_keys_in_default_settings(): void
    {
        /* Arrange */

        /* Act */
        $settings = QontoClient::defaultSettings();

        /* Assert */
        foreach (['access_token', 'api_base_url', 'upload_endpoint', 'invoice_endpoint', 'send_invoice_endpoint'] as $key) {
            $this->assertArrayHasKey($key, $settings);
        }
    }
}
