<?php

namespace Tests\Unit\Core;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use SuperPdpClient;
use Tests\Fakes\Integration\FakeSuperPdpClient;

class SuperPdpClientEdgeCasesTest extends TestCase
{
    #[Test]
    public function it_rejects_authenticate_with_missing_client_id(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $settings = $this->defaultSettings();
        $settings['client_id'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing SuperPDP OAuth2 settings');

        /* Assert */
        $provider->authenticate($settings);
    }

    #[Test]
    public function it_rejects_authenticate_with_missing_client_secret(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $settings = $this->defaultSettings();
        $settings['client_secret'] = '';

        /* Act */
        $this->expectException(RuntimeException::class);

        /* Assert */
        $provider->authenticate($settings);
    }

    #[Test]
    public function it_rejects_authenticate_when_oauth_token_fetch_fails(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient([], ['access_token' => 'tok'], 'Connection refused');
        $settings = $this->defaultSettings();

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Connection refused');

        /* Assert */
        $provider->authenticate($settings);
    }

    #[Test]
    public function it_rejects_send_invoice_with_missing_access_token(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient([], []);
        $settings = $this->defaultSettings();
        unset($settings['api_base_url']);
        $tmpFile = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmpFile, '%PDF-1.4');

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Missing SuperPDP');

        /* Assert */
        try {
            $provider->sendInvoice($tmpFile, []);
        } finally {
            unlink($tmpFile);
        }
    }

    #[Test]
    public function it_rejects_send_invoice_when_document_not_found(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient();
        $provider->authenticate($this->defaultSettings());
        $nonexistentPath = '/nonexistent/invoice.pdf';

        /* Act */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Invoice document not found');

        /* Assert */
        $provider->sendInvoice($nonexistentPath, []);
    }

    #[Test]
    public function it_sends_invoice_with_special_characters_in_metadata(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient([
            ['success' => true, 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => ['id' => 'inv-123']],
        ]);
        $provider->authenticate($this->defaultSettings());
        $tmpFile = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmpFile, '%PDF-1.4');

        /* Act */
        $result = $provider->sendInvoice($tmpFile, [
            'invoice_id' => 'INV-2026/001-A&B',
            'external_id' => 'Société Générale SARL',
        ]);
        unlink($tmpFile);

        /* Assert */
        $this->assertTrue($result['success']);
        $this->assertSame('sent', $result['status']);
    }

    #[Test]
    public function it_includes_external_id_in_query_when_provided(): void
    {
        /* Arrange */
        $provider = new FakeSuperPdpClient([
            ['success' => true, 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => ['id' => 'inv-456']],
        ]);
        $provider->authenticate($this->defaultSettings());
        $tmpFile = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmpFile, '%PDF-1.4');

        /* Act */
        $provider->sendInvoice($tmpFile, ['external_id' => 'ext-ref-123']);
        unlink($tmpFile);

        /* Assert */
        $this->assertArrayHasKey('options', $provider->requestLog[0]);
        $this->assertSame('ext-ref-123', $provider->requestLog[0]['options']['query']['external_id']);
    }

    #[Test]
    public function it_respects_disable_pre_check_setting(): void
    {
        /* Arrange */
        $settings = $this->defaultSettings();
        $settings['disable_pre_check'] = true;
        $provider = new FakeSuperPdpClient([
            ['success' => true, 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => ['id' => 'inv-789']],
        ]);
        $provider->authenticate($settings);
        $tmpFile = tempnam(sys_get_temp_dir(), 'inv') . '.pdf';
        file_put_contents($tmpFile, '%PDF-1.4');

        /* Act */
        $provider->sendInvoice($tmpFile, []);
        unlink($tmpFile);

        /* Assert */
        $this->assertTrue($provider->requestLog[0]['options']['query']['disable_pre_check']);
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
