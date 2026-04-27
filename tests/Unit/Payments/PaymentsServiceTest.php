<?php

namespace Tests\Unit\Payments;

use Mdl_Payments;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Payments::class)]
class PaymentsServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(PaymentsService::class);
    }

    #[Test]
    public function it_retrieves_payments_by_invoice_id(): void
    {
        /* Arrange */
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

        /* Act */
        $result = $this->service->whereInvoiceId($invoice->invoice_id);

        /* Assert */
        $this->assertInstanceOf(PaymentsService::class, $result);
    }

    #[Test]
    public function it_returns_db_array_with_correct_structure(): void
    {
        /* Act */
        $result = $this->service->dbArray();

        /* Assert */
        $this->assertIsArray($result);
    }


    // Migrated from BckpPaymentServiceTest.php
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('payment_method_id', $rules);
        $this->assertArrayHasKey('payment_amount', $rules);
        $this->assertArrayHasKey('payment_date', $rules);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_orders_payments_by_date_descending(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice');
        $this->seedModel('Payment', [
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(3),
        ]);
        $payment2 = $this->seedModel('Payment', [
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(1),
        ]);
        $this->seedModel('Payment', [
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(2),
        ]);

        /* Act */
        $result = $this->service->getAllWithRelations();

        /* Assert */
        $payments = $result->items();
        $this->assertGreaterThanOrEqual(3, count($payments));
        // Most recent should be first
        $this->assertEquals($payment2->payment_id, $payments[0]->payment_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_gets_payments_by_client_id(): void
    {
        /* Arrange */
        $client1  = $this->seedModel('Client');
        $client2  = $this->seedModel('Client');
        $invoice1 = $this->seedModel('Invoice', ['client_id' => $client1->client_id]);
        $invoice2 = $this->seedModel('Invoice', ['client_id' => $client2->client_id]);
        $payment1 = $this->seedModel('Payment', [
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
        $payment2 = $this->seedModel('Payment', [
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
        $payment3 = $this->seedModel('Payment', [
            'invoice_id' => $invoice2->invoice_id,
            'client_id'  => $client2->client_id,
        ]);

        /* Act */
        $result = $this->service->getByClientId($client1->client_id);

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('payment_id', $payment1->payment_id));
        $this->assertTrue($result->contains('payment_id', $payment2->payment_id));
        $this->assertFalse($result->contains('payment_id', $payment3->payment_id));
    }

}
