<?php

namespace Feature\Payments;

use Modules\Payments\Services\PaymentLogService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\Invoices\AbstractTestCase;
use Tests\Feature\Invoices\PaymentMethodService;

#[CoversClass(PaymentLogService::class)]
#[CoversClass(Feature\Payments\PaymentMethodDeletionValidation::class)]

class PaymentMethodDeletionValidationTest extends AbstractTestCase
{
    use InteractsWithDatabase;

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
        /* Arrange */
        $paymentMethod = $this->seedModel('PaymentMethod');

        /* Act */
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
        /* Arrange */
        $paymentMethod = $this->seedModel('PaymentMethod');
        $this->seedModel('Payment', ['payment_method_id' => $paymentMethod->payment_method_id]);

        /* Act */
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
        /* Arrange */
        $paymentMethod = $this->seedModel('PaymentMethod');
        $this->seedModelMany('Payment', 3, ['payment_method_id' => $paymentMethod->payment_method_id]);

        /* Act */
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
        /* Arrange */
        $paymentMethod = $this->seedModel('PaymentMethod');

        /* Act */
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
        /* Arrange */
        $paymentMethod = $this->seedModel('PaymentMethod');
        $payment       = $this->seedModel('Payment', ['payment_method_id' => $paymentMethod->payment_method_id]);

        // Remove the payment
        $payment->delete();

        /* Act */
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
        /* Arrange */
        $nonExistentId = 99999;

        /* Act */
        $canDelete = $this->service->canDelete($nonExistentId);
        $blockers  = $this->service->getDeletionBlockers($nonExistentId);

        /* Assert */
        $this->assertTrue($canDelete);
        $this->assertEquals(0, $blockers['payments']);
    }
}
