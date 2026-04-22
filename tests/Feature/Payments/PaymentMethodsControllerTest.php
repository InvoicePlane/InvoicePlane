<?php

namespace Tests\Feature\Payments;

use Modules\Payments\Controllers\PaymentMethodsController;
use Payment_Methods;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * PaymentMethodsController Feature Tests.
 *
 * Tests payment method management (Cash, Check, Credit Card, PayPal, etc.)
 */
#[CoversClass(Payment_Methods::class)]

class PaymentMethodsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays paginated list of payment methods.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_payment_methods(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('PaymentMethod', 5);

        /* Act */
        $response = $this->actingAs($user)->get(route('payment_methods.index'));

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
        /* Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('PaymentMethod', ['payment_method_name' => 'Wire Transfer']);
        $this->seedModel('PaymentMethod', ['payment_method_name' => 'Cash']);
        $this->seedModel('PaymentMethod', ['payment_method_name' => 'Check']);

        /* Act */
        $response = $this->actingAs($user)->get(route('payment_methods.index'));

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
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('payment_methods.form'));

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
        /* Arrange */
        $user          = $this->seedModel('User');
        $paymentMethod = $this->seedModel('PaymentMethod');

        /* Act */
        $response = $this->actingAs($user)->get(route('payment_methods.form', ['id' => $paymentMethod->payment_method_id]));

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
        /* Arrange */
        $user = $this->seedModel('User');

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
        $response = $this->actingAs($user)->post(route('payment_methods.form'), $data);

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
        /* Arrange */
        $user          = $this->seedModel('User');
        $paymentMethod = $this->seedModel('PaymentMethod', ['payment_method_name' => 'Old Name']);

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
        $response = $this->actingAs($user)->post(route('payment_methods.form', ['id' => $paymentMethod->payment_method_id]), $updateData);

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
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('payment_methods.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('payment_methods.index'));
    }

    /**
     * Test form validates required name.
     */
    #[Test]
    public function it_validates_required_payment_method_name(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

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
        $response = $this->actingAs($user)->post(route('payment_methods.form'), $invalidData);

        /* Assert */
        $response->assertSessionHasErrors('payment_method_name');
    }

    /**
     * Test form validates unique name.
     */
    #[Test]
    public function it_validates_unique_payment_method_name(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModel('PaymentMethod', ['payment_method_name' => 'Cash']);

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
        $response = $this->actingAs($user)->post(route('payment_methods.form'), $duplicateData);

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
        /* Arrange */
        $user          = $this->seedModel('User');
        $paymentMethod = $this->seedModel('PaymentMethod');

        /**
         * {
         *     "payment_method_id": 1
         * }.
         */
        $deletePayload = [
            'payment_method_id' => $paymentMethod->payment_method_id,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(
            route('payment_methods.delete', ['id' => $paymentMethod->payment_method_id]),
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
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "payment_method_id": 99999
         * }.
         */
        $deletePayload = [
            'payment_method_id' => 99999,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(
            route('payment_methods.delete', ['id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }
}
