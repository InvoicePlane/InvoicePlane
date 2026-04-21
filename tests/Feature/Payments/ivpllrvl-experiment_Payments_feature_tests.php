<?php

namespace Modules\Payments\Tests\Feature;

use Modules\Core\Models\User;
use Modules\Payments\Controllers\PaymentMethodsController;
use Modules\Payments\Models\PaymentMethod;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * PaymentMethodsController Feature Tests.
 *
 * Tests payment method management (Cash, Check, Credit Card, PayPal, etc.)
 */
#[CoversClass(PaymentMethodsController::class)]
class PaymentMethodsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of payment methods.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_payment_methods(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        PaymentMethod::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('payment_methods.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('payments::payment_methods_index');
        $response->assertViewHas('payment_methods');
    }

    /**
     * Test payment methods are ordered alphabetically.
     */
    #[Test]
    public function it_orders_payment_methods_alphabetically(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        PaymentMethod::factory()->create(['payment_method_name' => 'Wire Transfer']);
        PaymentMethod::factory()->create(['payment_method_name' => 'Cash']);
        PaymentMethod::factory()->create(['payment_method_name' => 'Check']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('payment_methods.index'));

        /* Assert */
        $response->assertOk();
        $paymentMethods = $response->viewData('payment_methods');
        $names          = $paymentMethods->pluck('payment_method_name')->toArray();

        $this->assertEquals('Cash', $names[0]);
        $this->assertEquals('Check', $names[1]);
        $this->assertEquals('Wire Transfer', $names[2]);
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('payment_methods.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('payments::payment_methods_form');
        $response->assertViewHas('payment_method');
        $response->assertViewHas('is_update', false);
    }

    /**
     * Test form displays edit form with existing payment method.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_payment_method(): void
    {
        /** Arrange */
        $user          = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('payment_methods.form', ['payment_method_id' => $paymentMethod->payment_method_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('payments::payment_methods_form');
        $response->assertViewHas('payment_method');
        $response->assertViewHas('is_update', true);

        $viewPaymentMethod = $response->viewData('payment_method');
        $this->assertEquals($paymentMethod->payment_method_id, $viewPaymentMethod->payment_method_id);
    }

    /**
     * Test form creates new payment method.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_payment_method_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "payment_method_name": "Credit Card",
         *     "btn_submit": "1"
         * }.
         */
        $data = [
            'payment_method_name' => 'Credit Card',
            'btn_submit'          => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('payment_methods.form'), $data);

        /* Assert */
        $response->assertRedirect(route('payment_methods.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_payment_methods', [
            'payment_method_name' => 'Credit Card',
        ]);
    }

    /**
     * Test form updates existing payment method.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_payment_method(): void
    {
        /** Arrange */
        $user          = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create(['payment_method_name' => 'Old Name']);

        /**
         * {
         *     "payment_method_name": "Updated Name",
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'payment_method_name' => 'Updated Name',
            'btn_submit'          => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('payment_methods.form', ['payment_method_id' => $paymentMethod->payment_method_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('payment_methods.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_payment_methods', [
            'payment_method_id'   => $paymentMethod->payment_method_id,
            'payment_method_name' => 'Updated Name',
        ]);
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('payment_methods.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('payment_methods.index'));
    }

    /**
     * Test form validates required name.
     */
    #[Test]
    public function it_validates_required_payment_method_name(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "payment_method_name": "",
         *     "btn_submit": "1"
         * }.
         */
        $invalidData = [
            'payment_method_name' => '',
            'btn_submit'          => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('payment_methods.form'), $invalidData);

        /* Assert */
        $response->assertSessionHasErrors('payment_method_name');
    }

    /**
     * Test form validates unique name.
     */
    #[Test]
    public function it_validates_unique_payment_method_name(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        PaymentMethod::factory()->create(['payment_method_name' => 'Cash']);

        /**
         * {
         *     "payment_method_name": "Cash",
         *     "btn_submit": "1"
         * }.
         */
        $duplicateData = [
            'payment_method_name' => 'Cash',
            'btn_submit'          => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('payment_methods.form'), $duplicateData);

        /* Assert */
        $response->assertSessionHasErrors('payment_method_name');
    }

    /**
     * Test delete removes payment method.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_payment_method(): void
    {
        /** Arrange */
        $user          = User::factory()->create();
        $paymentMethod = PaymentMethod::factory()->create();

        /**
         * {
         *     "payment_method_id": 1
         * }.
         */
        $deletePayload = [
            'payment_method_id' => $paymentMethod->payment_method_id,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('payment_methods.delete', ['payment_method_id' => $paymentMethod->payment_method_id]),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect(route('payment_methods.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_payment_methods', [
            'payment_method_id' => $paymentMethod->payment_method_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent payment method.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_payment_method(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "payment_method_id": 99999
         * }.
         */
        $deletePayload = [
            'payment_method_id' => 99999,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('payment_methods.delete', ['payment_method_id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }
}

/**
 * PaypalController Feature Tests.
 *
 * Tests PayPal payment gateway integration.
 */
#[CoversClass(PaypalController::class)]
class PaypalControllerTest extends FeatureTestCase
{
    /**
     * Test notify handles PayPal IPN notification.
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_paypal_ipn_notification(): void
    {
        /** Arrange */
        // PayPal IPN notifications require specific fields for validation
        // Note: Current implementation is a stub/TODO but test reflects real IPN data

        /** Act */
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

        $response = $this->post(route('gateways.paypal.notify'), $payload);

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
        /** Arrange */
        // Webhook endpoints should not require authentication
        // Note: Current implementation is a stub/TODO but test reflects real IPN data

        /** Act */
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

        $response = $this->post(route('gateways.paypal.notify'), $payload);

        /* Assert */
        // Note: Current stub implementation returns OK without validation
        // Future implementation should handle pending payments differently than completed
        $response->assertOk();
    }
}

/**
 * StripeController Feature Tests.
 *
 * Tests Stripe payment gateway integration.
 */
#[CoversClass(StripeController::class)]
class StripeControllerTest extends FeatureTestCase
{
    /**
     * Test notify handles Stripe webhook notification.
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_stripe_webhook_notification(): void
    {
        /** Arrange */
        // Stripe webhooks require event type and data object
        // Note: Current implementation is a stub/TODO but test reflects real webhook data

        /** Act */
        /**
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
         * }.
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

        $response = $this->post(route('gateways.stripe.notify'), $payload);

        /* Assert */
        // Note: Current stub implementation returns OK without validation
        // Future implementation should verify webhook signature, handle event types, update payment records
        $response->assertOk();
        $this->assertEquals('OK', $response->getContent());
    }

    /**
     * Test notify is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // Webhook endpoints should not require authentication
        // Note: Current implementation is a stub/TODO but test reflects real webhook data

        /** Act */
        /**
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
         * }.
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

        $response = $this->post(route('gateways.stripe.notify'), $payload);

        /* Assert */
        // Note: Current stub implementation returns OK without validation
        // Future implementation should handle different event types (refunds, disputes, etc.)
        $response->assertOk();
    }
}
