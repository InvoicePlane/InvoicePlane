<?php

namespace Tests\Unit\Integration;

use IntegrationClient;
use IntegrationClientInterface;
use PHPUnit\Framework\TestCase;

class IntegrationClientTest extends TestCase
{
    private function makeProvider(array $overrides = []): IntegrationClientInterface
    {
        $stub = $this->createMock(IntegrationClientInterface::class);

        $stub->method('authenticate')->willReturn($overrides['authenticate'] ?? true);
        $stub->method('sendInvoice')->willReturn($overrides['sendInvoice'] ?? ['success' => true, 'external_id' => 'inv-1', 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => []]);
        $stub->method('getInvoiceStatus')->willReturn($overrides['getInvoiceStatus'] ?? ['success' => true, 'external_id' => 'inv-1', 'status' => 'sent', 'message' => 'ok', 'http_code' => 200, 'request' => [], 'response' => []]);
        $stub->method('receiveInvoices')->willReturn($overrides['receiveInvoices'] ?? ['success' => true, 'status' => 'received', 'message' => 'ok', 'http_code' => 200, 'response' => []]);
        $stub->method('getInvoiceEvents')->willReturn($overrides['getInvoiceEvents'] ?? ['success' => true, 'status' => 'events_received', 'message' => 'ok', 'http_code' => 200, 'response' => []]);

        return $stub;
    }

    private function settings(): array
    {
        return ['access_token' => 'tok', 'api_base_url' => 'https://example.com'];
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_delegates_authenticate_to_the_provider(): void
    {
        /* Arrange */
        $provider = $this->createMock(IntegrationClientInterface::class);
        $provider->expects($this->once())->method('authenticate')->with($this->settings())->willReturn(true);
        $client = new IntegrationClient($provider, $this->settings());

        /* Act */
        $result = $client->authenticate();

        /* Assert */
        $this->assertTrue($result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_authenticates_before_sending_an_invoice(): void
    {
        /* Arrange */
        $provider = $this->createMock(IntegrationClientInterface::class);
        $provider->expects($this->once())->method('authenticate')->willReturn(true);
        $provider->expects($this->once())->method('sendInvoice')->willReturn(['success' => true, 'external_id' => 'x', 'status' => 'sent', 'message' => '', 'http_code' => 200, 'request' => [], 'response' => []]);
        $client = new IntegrationClient($provider, $this->settings());

        /* Act */
        $result = $client->sendInvoice('/tmp/invoice.pdf', []);

        /* Assert */
        $this->assertTrue($result['success']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_authenticates_before_fetching_invoice_status(): void
    {
        /* Arrange */
        $provider = $this->createMock(IntegrationClientInterface::class);
        $provider->expects($this->once())->method('authenticate')->willReturn(true);
        $provider->expects($this->once())->method('getInvoiceStatus')->with('inv-42')->willReturn(['success' => true, 'external_id' => 'inv-42', 'status' => 'sent', 'message' => '', 'http_code' => 200, 'request' => [], 'response' => []]);
        $client = new IntegrationClient($provider, $this->settings());

        /* Act */
        $result = $client->getInvoiceStatus('inv-42');

        /* Assert */
        $this->assertSame('inv-42', $result['external_id']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_authenticates_before_receiving_invoices(): void
    {
        /* Arrange */
        $provider = $this->createMock(IntegrationClientInterface::class);
        $provider->expects($this->once())->method('authenticate')->willReturn(true);
        $provider->expects($this->once())->method('receiveInvoices')->with(['status' => 'pending'])->willReturn(['success' => true, 'status' => 'received', 'message' => '', 'http_code' => 200, 'response' => []]);
        $client = new IntegrationClient($provider, $this->settings());

        /* Act */
        $result = $client->receiveInvoices(['status' => 'pending']);

        /* Assert */
        $this->assertTrue($result['success']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_authenticates_before_fetching_invoice_events(): void
    {
        /* Arrange */
        $provider = $this->createMock(IntegrationClientInterface::class);
        $provider->expects($this->once())->method('authenticate')->willReturn(true);
        $provider->expects($this->once())->method('getInvoiceEvents')->willReturn(['success' => true, 'status' => 'events_received', 'message' => '', 'http_code' => 200, 'response' => []]);
        $client = new IntegrationClient($provider, $this->settings());

        /* Act */
        $result = $client->getInvoiceEvents();

        /* Assert */
        $this->assertTrue($result['success']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_propagates_a_provider_send_failure(): void
    {
        /* Arrange */
        $provider = $this->makeProvider(['sendInvoice' => ['success' => false, 'external_id' => null, 'status' => 'error', 'message' => 'Upload failed', 'http_code' => 500, 'request' => [], 'response' => []]]);
        $client = new IntegrationClient($provider, $this->settings());

        /* Act */
        $result = $client->sendInvoice('/tmp/invoice.pdf', []);

        /* Assert */
        $this->assertFalse($result['success']);
        $this->assertSame('error', $result['status']);
    }
}
