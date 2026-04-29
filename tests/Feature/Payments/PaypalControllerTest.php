<?php

namespace Tests\Feature\Payments;

use Paypal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Paypal Feature Tests.
 *
 * Tests Paypal management (Cash, Check, Credit Card, PayPal, etc.)
 */
#[CoversClass(Paypal::class)]
class PaypalControllerTest extends AbstractTestCase
{
    #[Group('exotic')]
    #[Test]
    public function it_handles_paypal_ipn_notification(): void
    {
        /**
         * Payload:
         * {
         *     "txn_id": "1234567890ABCDEF",
         *     "payment_status": "Completed",
         *     "mc_gross": "100.00",
         *     "mc_currency": "USD",
         *     "receiver_email": "merchant@example.com",
         *     "payer_email": "buyer@example.com",
         *     "custom": "invoice_123"
         * }
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

        $this->assertResponseStatusCode($response, 200);
    }

    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /**
         * Payload:
         * {
         *     "txn_id": "0987654321ZYXWVU",
         *     "payment_status": "Pending",
         *     "mc_gross": "50.00",
         *     "mc_currency": "EUR"
         * }
         */
        $payload = [
            'txn_id'         => '0987654321ZYXWVU',
            'payment_status' => 'Pending',
            'mc_gross'       => '50.00',
            'mc_currency'    => 'EUR',
        ];

        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment', $payload);

        $this->assertResponseStatusCode($response, 200);
    }
}
