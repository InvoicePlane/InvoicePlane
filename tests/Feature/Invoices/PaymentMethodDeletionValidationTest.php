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
