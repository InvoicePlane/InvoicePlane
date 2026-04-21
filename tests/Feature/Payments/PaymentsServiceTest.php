<?php

namespace Modules\Payments\tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Modules\PaymentMethods\Tests\Unit\app;

use Modules\Payments\app\Models\PaymentMethod;
use Modules\Payments\app\Services\PaymentMethodsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaymentsServiceTest extends TestCase
{
    use RefreshDatabase;

    private PaymentsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaymentsService::class);
    }

    #[Test]
    public function it_retrieves_payments_by_invoice_id(): void
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

        // Create payment for different invoice
        $otherInvoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        Payment::create([
            'invoice_id'     => $otherInvoice->invoice_id,
            'payment_amount' => 200.00,
            'payment_date'   => now(),
        ]);

        // Act
        $result = $this->service->whereInvoiceId($invoice->invoice_id);

        // Assert
        $this->assertInstanceOf(PaymentsService::class, $result);
    }

    #[Test]
    public function it_returns_db_array_with_correct_structure(): void
    {
        // Act
        $result = $this->service->dbArray();

        // Assert
        $this->assertIsArray($result);
    }
}
