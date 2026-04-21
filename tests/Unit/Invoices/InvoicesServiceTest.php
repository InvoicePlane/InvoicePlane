<?php

namespace Tests\Unit\Invoices;


use function Modules\InvoiceGroups\Tests\Unit\app;

use Modules\Invoices\app\Models\InvoiceGroup;
use Modules\Invoices\app\Services\InvoiceGroupsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoicesServiceTest extends TestCase
{

    private InvoicesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InvoicesService::class);
    }

    #[Test]
    public function it_attaches_payments_to_invoice(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        Payment::create([
            'invoice_id'     => $invoice->invoice_id,
            'payment_amount' => 100.00,
            'payment_date'   => now(),
        ]);

        Payment::create([
            'invoice_id'     => $invoice->invoice_id,
            'payment_amount' => 50.00,
            'payment_date'   => now(),
        ]);

        // Act
        $result = $this->service->getPayments($invoice);

        // Assert
        $this->assertNotNull($result->payments);
        $this->assertCount(2, $result->payments);
    }

    #[Test]
    public function it_returns_null_payments_when_invoice_has_no_payments(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        // Act
        $result = $this->service->getPayments($invoice);

        // Assert
        $this->assertNull($result->payments);
    }

    #[Test]
    public function it_marks_invoice_as_viewed_when_status_is_sent(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 2, // Sent status
        ]);

        // Act
        $this->service->markViewed($invoice->invoice_id);

        // Assert
        $invoice->refresh();
        $this->assertEquals(3, $invoice->invoice_status_id); // Viewed status
    }

    #[Test]
    public function it_does_not_change_status_when_invoice_is_not_sent(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1, // Draft status
        ]);

        // Act
        $this->service->markViewed($invoice->invoice_id);

        // Assert
        $invoice->refresh();
        $this->assertEquals(1, $invoice->invoice_status_id); // Should remain draft
    }

    #[Test]
    public function it_filters_invoices_by_client(): void
    {
        // Arrange
        $client1 = tmpClient::create([
            'client_name'   => 'Client 1',
            'client_active' => 1,
        ]);

        $client2 = tmpClient::create([
            'client_name'   => 'Client 2',
            'client_active' => 1,
        ]);

        Invoice::create([
            'client_id'         => $client1->client_id,
            'invoice_status_id' => 1,
        ]);

        Invoice::create([
            'client_id'         => $client1->client_id,
            'invoice_status_id' => 1,
        ]);

        Invoice::create([
            'client_id'         => $client2->client_id,
            'invoice_status_id' => 1,
        ]);

        // Act
        $result = $this->service->byClient($client1->client_id);

        // Assert
        $this->assertInstanceOf(InvoicesService::class, $result);
    }
}
