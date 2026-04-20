<?php

namespace Modules\Payments\Tests\Unit;

use Modules\Invoices\Models\Invoice;
use Modules\Payments\Models\PaymentLog;
use Modules\Payments\Services\PaymentLogService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(PaymentLogService::class)]
class PaymentLogServiceTest extends AbstractServiceTestCase
{
    private PaymentLogService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentLogService();
    }

    #[Group('relationships')]
    #[Test]
    public function it_gets_all_payment_logs_with_relations_paginated(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        PaymentLog::factory()->count(3)->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(1),
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /* Assert */
        $this->assertGreaterThanOrEqual(3, $result->total());
        $this->assertTrue($result->first()->relationLoaded('invoice'));
    }

    #[Group('relationships')]
    #[Test]
    public function it_orders_payment_logs_by_date_descending(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        PaymentLog::factory()->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(3),
        ]);
        $log2 = PaymentLog::factory()->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(1),
        ]);
        PaymentLog::factory()->create([
            'invoice_id'       => $invoice->invoice_id,
            'payment_log_date' => now()->subDays(2),
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /** Assert */
        $logs = $result->items();
        $this->assertGreaterThanOrEqual(3, count($logs));
        // Most recent should be first
        $this->assertEquals($log2->payment_log_id, $logs[0]->payment_log_id);
    }

    #[Group('relationships')]
    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        PaymentLog::factory()->count(10)->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations(['invoice'], 5);

        /* Assert */
        $this->assertEquals(5, $result->perPage());
    }

    #[Group('relationships')]
    #[Test]
    public function it_loads_custom_relations(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        PaymentLog::factory()->create([
            'invoice_id' => $invoice->invoice_id,
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations(['invoice']);

        /* Assert */
        $this->assertTrue($result->first()->relationLoaded('invoice'));
        $this->assertNotNull($result->first()->invoice);
    }
}

/**
 * Unit tests for PaymentMethod deletion validation business logic.
 *
 * Tests the service layer validation rules for deleting payment methods.
 */
#[CoversClass(PaymentMethodService::class)]
#[Group('business-rules')]
#[Group('deletion')]
#[Group('payment-methods')]
class PaymentMethodDeletionValidationTest extends TestCase
{
    protected PaymentMethodService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentMethodService();
    }

    /**
     * Test that payment methods without payments can be deleted.
     */
    #[Test]
    public function it_allows_deletion_of_payment_method_without_payments(): void
    {
        /** Arrange */
        $paymentMethod = PaymentMethod::factory()->create();

        /** Act */
        $canDelete = $this->service->canDelete($paymentMethod->payment_method_id);

        /* Assert */
        $this->assertTrue($canDelete);
    }

    /**
     * Test that payment methods with payments cannot be deleted.
     */
    #[Test]
    public function it_prevents_deletion_of_payment_method_with_payments(): void
    {
        /** Arrange */
        $paymentMethod = PaymentMethod::factory()->create();
        Payment::factory()->create(['payment_method_id' => $paymentMethod->payment_method_id]);

        /** Act */
        $canDelete = $this->service->canDelete($paymentMethod->payment_method_id);

        /* Assert */
        $this->assertFalse($canDelete);
    }

    /**
     * Test that getDeletionBlockers returns correct payment count.
     */
    #[Test]
    public function it_returns_correct_payment_count_in_blockers(): void
    {
        /** Arrange */
        $paymentMethod = PaymentMethod::factory()->create();
        Payment::factory()->count(3)->create(['payment_method_id' => $paymentMethod->payment_method_id]);

        /** Act */
        $blockers = $this->service->getDeletionBlockers($paymentMethod->payment_method_id);

        /* Assert */
        $this->assertArrayHasKey('payments', $blockers);
        $this->assertEquals(3, $blockers['payments']);
    }

    /**
     * Test deletion blockers with no payments returns zero.
     */
    #[Test]
    public function it_returns_zero_payments_when_no_payments_exist(): void
    {
        /** Arrange */
        $paymentMethod = PaymentMethod::factory()->create();

        /** Act */
        $blockers = $this->service->getDeletionBlockers($paymentMethod->payment_method_id);

        /* Assert */
        $this->assertEquals(0, $blockers['payments']);
    }

    /**
     * Test that deletion is allowed after all payments are removed.
     */
    #[Test]
    public function it_allows_deletion_after_payments_removed(): void
    {
        /** Arrange */
        $paymentMethod = PaymentMethod::factory()->create();
        $payment       = Payment::factory()->create(['payment_method_id' => $paymentMethod->payment_method_id]);

        // Remove the payment
        $payment->delete();

        /** Act */
        $canDelete = $this->service->canDelete($paymentMethod->payment_method_id);

        /* Assert */
        $this->assertTrue($canDelete);
    }

    /**
     * Test handling of non-existent payment method.
     */
    #[Test]
    public function it_handles_non_existent_payment_method(): void
    {
        /** Arrange */
        $nonExistentId = 99999;

        /** Act */
        $canDelete = $this->service->canDelete($nonExistentId);
        $blockers  = $this->service->getDeletionBlockers($nonExistentId);

        /* Assert */
        $this->assertTrue($canDelete);
        $this->assertEquals(0, $blockers['payments']);
    }
}

#[CoversClass(PaymentService::class)]
class PaymentServiceTest extends AbstractServiceTestCase
{
    private PaymentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new PaymentService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('payment_method_id', $rules);
        $this->assertArrayHasKey('payment_amount', $rules);
        $this->assertArrayHasKey('payment_date', $rules);
    }

    #[Group('relationships')]
    #[Test]
    public function it_orders_payments_by_date_descending(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        Payment::factory()->create([
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(3),
        ]);
        $payment2 = Payment::factory()->create([
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(1),
        ]);
        Payment::factory()->create([
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(2),
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /** Assert */
        $payments = $result->items();
        $this->assertGreaterThanOrEqual(3, count($payments));
        // Most recent should be first
        $this->assertEquals($payment2->payment_id, $payments[0]->payment_id);
    }

    #[Group('queries')]
    #[Test]
    public function it_gets_payments_by_client_id(): void
    {
        /** Arrange */
        $client1  = \Modules\Crm\Models\Client::factory()->create();
        $client2  = \Modules\Crm\Models\Client::factory()->create();
        $invoice1 = \Modules\Invoices\Models\Invoice::factory()->create(['client_id' => $client1->client_id]);
        $invoice2 = \Modules\Invoices\Models\Invoice::factory()->create(['client_id' => $client2->client_id]);
        $payment1 = Payment::factory()->create([
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
        $payment2 = Payment::factory()->create([
            'invoice_id' => $invoice1->invoice_id,
            'client_id'  => $client1->client_id,
        ]);
        $payment3 = Payment::factory()->create([
            'invoice_id' => $invoice2->invoice_id,
            'client_id'  => $client2->client_id,
        ]);

        /** Act */
        $result = $this->service->getByClientId($client1->client_id);

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('payment_id', $payment1->payment_id));
        $this->assertTrue($result->contains('payment_id', $payment2->payment_id));
        $this->assertFalse($result->contains('payment_id', $payment3->payment_id));
    }
}

