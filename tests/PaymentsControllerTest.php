<?php

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(\Payments::class)]
#[CoversClass(PaymentsController::class)]

class PaymentsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected $invoice;

    protected $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user          = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->invoice       = $this->seedModel('Invoice', ['invoice_balance' => 100.00]);
        $this->paymentMethod = $this->seedModel('PaymentMethod');
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_payments_index(): void
    {
        $response = $this->get(\Tests\Feature\Invoices\route('payments.index'));

        $response->assertSuccessful();
        $response->assertViewHas('payments');
    }

    #[Test]
    public function it_creates_new_payment_with_valid_data(): void
    {
        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_date'      => now()->format('Y-m-d'),
            'payment_amount'    => 50.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'payment_note'      => 'Test payment note.',
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 50.00,
        ]);
    }

    #[Test]
    public function it_creates_payment_with_minimum_required_fields(): void
    {
        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_amount'    => 25.50,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 25.50,
        ]);
    }

    #[Test]
    public function it_creates_payment_with_note(): void
    {
        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_date'      => now()->format('Y-m-d'),
            'payment_amount'    => 100.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'payment_note'      => $this->faker->sentence(),
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'payment_note' => $paymentData['payment_note'],
        ]);
    }

    #[Test]
    public function it_updates_existing_payment(): void
    {
        $payment = $this->seedModel('Payment', [
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 50.00,
        ]);

        $updateData = [
            'payment_amount' => 75.00,
            'payment_note'   => 'Updated payment note',
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form', ['id' => $payment->payment_id]), $updateData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'payment_id'     => $payment->payment_id,
            'payment_amount' => 75.00,
            'payment_note'   => 'Updated payment note',
        ]);
    }

    #[Test]
    public function it_views_payment_details(): void
    {
        $payment = $this->seedModel('Payment');

        $response = $this->get(\Tests\Feature\Invoices\route('payments.view', ['id' => $payment->payment_id]));

        $response->assertSuccessful();
        $response->assertViewHas('payment');
    }

    #[Test]
    public function it_deletes_payment(): void
    {
        $payment = $this->seedModel('Payment');

        $response = $this->delete(\Tests\Feature\Invoices\route('payments.delete', ['id' => $payment->payment_id]));

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseMissing('ip_payments', ['payment_id' => $payment->payment_id]);
    }

    #[Test]
    public function it_loads_payment_form(): void
    {
        $response = $this->get(\Tests\Feature\Invoices\route('payments.form'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_methods');
        $response->assertViewHas('open_invoices');
    }

    #[Test]
    public function it_loads_payment_edit_form(): void
    {
        $payment = $this->seedModel('Payment');

        $response = $this->get(\Tests\Feature\Invoices\route('payments.form', ['id' => $payment->payment_id]));

        $response->assertSuccessful();
        $response->assertViewHas('payment');
    }

    #[Test]
    public function it_cancels_payment_form_and_redirects(): void
    {
        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), ['btn_cancel' => true]);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
    }

    #[Test]
    public function it_saves_payment_custom_fields(): void
    {
        $customField = $this->seedModel('CustomField', [
            'custom_field_table' => 'ip_payment_custom',
        ]);

        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_amount'    => 100.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'custom'            => [
                $customField->custom_field_id => 'Custom value',
            ],
        ];

        $response = $this->post(\Tests\Feature\Invoices\route('payments.form'), $paymentData);

        $response->assertRedirect(\Tests\Feature\Invoices\route('payments.index'));
        $this->assertDatabaseHas('ip_payment_custom', [
            'payment_custom_fieldid'    => $customField->custom_field_id,
            'payment_custom_fieldvalue' => 'Custom value',
        ]);
    }

    #[Test]
    public function it_displays_online_payment_logs(): void
    {
        $this->seedModelMany('PaymentLog', 5);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 5;
        });
    }

    #[Test]
    public function it_filters_online_payment_logs_by_search(): void
    {
        $this->seedModel('PaymentLog', ['transaction_id' => 'TXN123ABC']);
        $this->seedModel('PaymentLog', ['transaction_id' => 'TXN456DEF']);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs', ['search' => '123']));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 1;
        });
    }

    #[Test]
    public function it_filters_online_payment_logs_by_date_range(): void
    {
        $this->seedModel('PaymentLog', ['created_at' => now()->subDays(10)]);
        $this->seedModel('PaymentLog', ['created_at' => now()->subDays(5)]);
        $this->seedModel('PaymentLog', ['created_at' => now()]);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs', [
            'date_from' => now()->subDays(6)->format('Y-m-d'),
            'date_to'   => now()->format('Y-m-d'),
        ]));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 2;
        });
    }

    #[Test]
    public function it_filters_online_payment_logs_by_status(): void
    {
        $this->seedModel('PaymentLog', ['status' => 'completed']);
        $this->seedModel('PaymentLog', ['status' => 'completed']);
        $this->seedModel('PaymentLog', ['status' => 'failed']);

        $response = $this->get(\Tests\Feature\Invoices\route('payments.onlineLogs', ['status' => 'completed']));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs): bool {
            return $logs->count() === 2;
        });
    }
}
