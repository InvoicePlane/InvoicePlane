<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Payments;

use Paypal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

/**
 * Paypal Feature Tests.
 *
 * Tests Paypal management (Cash, Check, Credit Card, PayPal, etc.)
 */
#[CoversClass(Paypal::class)]
#[CoversClass(Tests\Feature\Payments\PaypalController::class)]

class PaypalControllerTest extends AbstractTestCase
{
    /**
     * Test notify handles PayPal IPN notification.
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_paypal_ipn_notification(): void
    {
        /* Arrange */
        // PayPal IPN notifications require specific fields for validation
        // Note: Current implementation is a stub/TODO but test reflects real IPN data

        /* Act */
        /**
         * {
         *     "txn_id": "1234567890ABCDEF",
         *     "payment_status": "Completed",
         *     "mc_gross": "100.00",
         *     "mc_currency": "USD",
         *     "receiver_email": "merchant@example.com",
         *     "payer_email": "buyer@example.com",
         *     "custom": "invoice_123"
         * }.
         */
        $payload = [
            'txn_id'         => '1234567890ABCDEF',
            'payment_status' => 'Completed',
            'mc_gross'       => '100.00',
            'mc_currency'    => 'USD',
            'receiver_email' => 'merchant@example.com',
            'payer_email'    => 'buyer@example.com',
            'custom'         => 'invoice_123',
        ];

        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment', $payload);

        /* Assert */
        // Note: Current stub implementation returns OK without validation
        // Future implementation should verify IPN signature, validate txn_id, update payment status
        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * Test notify is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /* Arrange */
        // Webhook endpoints should not require authentication
        // Note: Current implementation is a stub/TODO but test reflects real IPN data

        /* Act */
        /**
         * {
         *     "txn_id": "0987654321ZYXWVU",
         *     "payment_status": "Pending",
         *     "mc_gross": "50.00",
         *     "mc_currency": "EUR"
         * }.
         */
        $payload = [
            'txn_id'         => '0987654321ZYXWVU',
            'payment_status' => 'Pending',
            'mc_gross'       => '50.00',
            'mc_currency'    => 'EUR',
        ];

        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment', $payload);

        /* Assert */
        // Note: Current stub implementation returns OK without validation
        // Future implementation should handle pending payments differently than completed
        $response->assertOk();
    }
}
