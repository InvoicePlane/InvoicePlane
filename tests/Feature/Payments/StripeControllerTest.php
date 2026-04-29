<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Stripe;
use Tests\AbstractTestCase;

/**
 * Stripe Feature Tests.
 *
 * Tests Stripe webhook handling.
 */
#[CoversClass(Stripe::class)]
class StripeControllerTest extends AbstractTestCase
{
    #[Group('exotic')]
    #[Test]
    public function it_handles_stripe_webhook_notification(): void
    {
        /**
         * Payload:
         * {
         *     "id": "evt_1234567890",
         *     "type": "payment_intent.succeeded",
         *     "data": {
         *         "object": {
         *             "id": "pi_1234567890",
         *             "amount": 10000,
         *             "currency": "usd",
         *             "status": "succeeded",
         *             "metadata": {
         *                 "invoice_id": "123"
         *             }
         *         }
         *     }
         * }
         */
        $payload = [
            'id'   => 'evt_1234567890',
            'type' => 'payment_intent.succeeded',
            'data' => [
                'object' => [
                    'id'       => 'pi_1234567890',
                    'amount'   => 10000,
                    'currency' => 'usd',
                    'status'   => 'succeeded',
                    'metadata' => [
                        'invoice_id' => '123',
                    ],
                ],
            ],
        ];

        $response = $this->post('/guest/gateways/stripe/callback', $payload);

        $this->assertResponseStatusCode($response, 200);
    }

    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /**
         * Payload:
         * {
         *     "id": "evt_0987654321",
         *     "type": "charge.refunded",
         *     "data": {
         *         "object": {
         *             "id": "ch_0987654321",
         *             "amount": 5000,
         *             "refunded": true
         *         }
         *     }
         * }
         */
        $payload = [
            'id'   => 'evt_0987654321',
            'type' => 'charge.refunded',
            'data' => [
                'object' => [
                    'id'       => 'ch_0987654321',
                    'amount'   => 5000,
                    'refunded' => true,
                ],
            ],
        ];

        $response = $this->post('/guest/gateways/stripe/callback', $payload);

        $this->assertResponseStatusCode($response, 200);
    }
}
