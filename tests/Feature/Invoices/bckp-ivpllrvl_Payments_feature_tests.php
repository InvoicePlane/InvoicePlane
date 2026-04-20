<?php

namespace Modules\Payments\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Payments\app\Http\Controllers\PaymentMethodsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\PaymentMethods\route;

use Tests\TestCase;

#[CoversClass(PaymentMethodsController::class)]
class PaymentMethodsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_payment_methods_list()
    {
        // Arrange: create payment methods
        $paymentMethod = \Modules\Payments\app\Models\PaymentMethod::factory()->create();

        // Act: visit payment methods index
        $response = $this->get(route('payment_methods.index'));

        // Assert: payment methods are displayed
        $response->assertStatus(200);
        $response->assertSee($paymentMethod->payment_method_name);
    }

    #[Test]
    public function it_displays_payment_method_form_for_new_method()
    {
        // Act: visit new payment method form
        $response = $this->get(route('payment_methods.form'));

        // Assert: form is displayed
        $response->assertStatus(200);
    }

    #[Test]
    public function it_redirects_when_cancel_button_is_clicked()
    {
        // Act: submit form with cancel button
        $response = $this->post(route('payment_methods.form'), [
            'btn_cancel' => true,
        ]);

        // Assert: redirects to payment methods index
        $response->assertRedirect(route('payment_methods'));
    }

    #[Test]
    public function it_deletes_payment_method()
    {
        // Arrange: create a payment method
        $paymentMethod = \Modules\Payments\app\Models\PaymentMethod::factory()->create();

        // Act: delete the payment method
        $response = $this->get(route('payment_methods.delete', ['id' => $paymentMethod->id]));

        // Assert: redirects and payment method is deleted
        $response->assertRedirect(route('payment_methods'));
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $paymentMethod->id]);
    }
}

#[CoversClass(AjaxController::class)]
class PaymentsAjaxControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected Invoice $invoice;

    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user          = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->invoice       = Invoice::factory()->create(['invoice_balance' => 100.00]);
        $this->paymentMethod = PaymentMethod::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_adds_payment_via_ajax_with_valid_data(): void
    {
        $paymentData = [
            'invoice_id'        => $this->invoice->invoice_id,
            'payment_amount'    => 50.00,
            'payment_method_id' => $this->paymentMethod->payment_method_id,
            'payment_date'      => now()->format('Y-m-d'),
        ];

        $response = $this->post(route('payments.ajax.add'), $paymentData);

        $response->assertSuccessful();
        $response->assertJson(['success' => 1]);
        $this->assertArrayHasKey('payment_id', $response->json());
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 50.00,
        ]);
    }

    #[Test]
    public function it_returns_validation_errors_for_invalid_payment(): void
    {
        $paymentData = [
            'invoice_id'     => null,
            'payment_amount' => -50.00, // Invalid amount
        ];

        $response = $this->post(route('payments.ajax.add'), $paymentData);

        $response->assertSuccessful();
        $response->assertJson(['success' => 0]);
        $this->assertArrayHasKey('validation_errors', $response->json());
    }

    #[Test]
    public function it_displays_modal_add_payment_form(): void
    {
        $response = $this->post(route('payments.ajax.modalAddPayment'), [
            'invoice_id'             => $this->invoice->invoice_id,
            'invoice_balance'        => $this->invoice->invoice_balance,
            'invoice_payment_method' => $this->invoice->payment_method,
            'payment_cf_exist'       => 'no',
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('payment_methods');
        $response->assertViewHas('invoice_id', $this->invoice->invoice_id);
        $response->assertViewHas('invoice_balance', $this->invoice->invoice_balance);
    }

    #[Test]
    public function it_sanitizes_invoice_id_in_modal(): void
    {
        $response = $this->post(route('payments.ajax.modalAddPayment'), [
            'invoice_id'       => '<script>alert("xss")</script>',
            'invoice_balance'  => 100,
            'payment_cf_exist' => 'no',
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('invoice_id', function ($id) {
            return ! str_contains($id, '<script>');
        });
    }
}

class PaymentMethodsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_payment_methods_index(): void
    {
        $response = $this->get(route('payment_methods.index'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_methods');
        $response->assertSee('Payment Methods');
    }

    #[Test]
    public function it_creates_new_payment_method(): void
    {
        $methodData = [
            'payment_method_name' => 'Test Payment Method',
        ];

        $response = $this->post(route('payment_methods.form'), $methodData);

        $response->assertRedirect(route('payment_methods.index'));
        $this->assertDatabaseHas('ip_payment_methods', [
            'payment_method_name' => 'Test Payment Method',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_payment_method_names(): void
    {
        PaymentMethod::factory()->create(['payment_method_name' => 'Existing Method']);

        $methodData = [
            'payment_method_name' => 'Existing Method',
            'is_update'           => 0,
        ];

        $response = $this->post(route('payment_methods.form'), $methodData);

        $response->assertRedirect(route('payment_methods.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_payment_method(): void
    {
        $method = PaymentMethod::factory()->create(['payment_method_name' => 'Original']);

        $updateData = [
            'payment_method_name' => 'Edited Payment Method',
        ];

        $response = $this->post(route('payment_methods.form', ['id' => $method->payment_method_id]), $updateData);

        $response->assertRedirect(route('payment_methods.index'));
        $this->assertDatabaseHas('ip_payment_methods', [
            'payment_method_id'   => $method->payment_method_id,
            'payment_method_name' => 'Edited Payment Method',
        ]);
    }

    #[Test]
    public function it_deletes_payment_method(): void
    {
        $method = PaymentMethod::factory()->create();

        $response = $this->delete(route('payment_methods.delete', ['id' => $method->payment_method_id]));

        $response->assertRedirect(route('payment_methods.index'));
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_id' => $method->payment_method_id]);
    }

    #[Test]
    public function it_cancels_payment_method_form_and_redirects(): void
    {
        $response = $this->post(route('payment_methods.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('payment_methods.index'));
    }
}

#[CoversClass(PaymentsController::class)]
class PaymentsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected Invoice $invoice;

    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user          = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->invoice       = Invoice::factory()->create(['invoice_balance' => 100.00]);
        $this->paymentMethod = PaymentMethod::factory()->create();
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_payments_index(): void
    {
        $response = $this->get(route('payments.index'));

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

        $response = $this->post(route('payments.form'), $paymentData);

        $response->assertRedirect(route('payments.index'));
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

        $response = $this->post(route('payments.form'), $paymentData);

        $response->assertRedirect(route('payments.index'));
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

        $response = $this->post(route('payments.form'), $paymentData);

        $response->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'payment_note' => $paymentData['payment_note'],
        ]);
    }

    #[Test]
    public function it_updates_existing_payment(): void
    {
        $payment = Payment::factory()->create([
            'invoice_id'     => $this->invoice->invoice_id,
            'payment_amount' => 50.00,
        ]);

        $updateData = [
            'payment_amount' => 75.00,
            'payment_note'   => 'Updated payment note',
        ];

        $response = $this->post(route('payments.form', ['id' => $payment->payment_id]), $updateData);

        $response->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('ip_payments', [
            'payment_id'     => $payment->payment_id,
            'payment_amount' => 75.00,
            'payment_note'   => 'Updated payment note',
        ]);
    }

    #[Test]
    public function it_views_payment_details(): void
    {
        $payment = Payment::factory()->create();

        $response = $this->get(route('payments.view', ['id' => $payment->payment_id]));

        $response->assertSuccessful();
        $response->assertViewHas('payment');
    }

    #[Test]
    public function it_deletes_payment(): void
    {
        $payment = Payment::factory()->create();

        $response = $this->delete(route('payments.delete', ['id' => $payment->payment_id]));

        $response->assertRedirect(route('payments.index'));
        $this->assertDatabaseMissing('ip_payments', ['payment_id' => $payment->payment_id]);
    }

    #[Test]
    public function it_loads_payment_form(): void
    {
        $response = $this->get(route('payments.form'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_methods');
        $response->assertViewHas('open_invoices');
    }

    #[Test]
    public function it_loads_payment_edit_form(): void
    {
        $payment = Payment::factory()->create();

        $response = $this->get(route('payments.form', ['id' => $payment->payment_id]));

        $response->assertSuccessful();
        $response->assertViewHas('payment');
    }

    #[Test]
    public function it_cancels_payment_form_and_redirects(): void
    {
        $response = $this->post(route('payments.form'), ['btn_cancel' => true]);

        $response->assertRedirect(route('payments.index'));
    }

    #[Test]
    public function it_saves_payment_custom_fields(): void
    {
        $customField = CustomField::factory()->create([
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

        $response = $this->post(route('payments.form'), $paymentData);

        $response->assertRedirect(route('payments.index'));
        $this->assertDatabaseHas('ip_payment_custom', [
            'payment_custom_fieldid'    => $customField->custom_field_id,
            'payment_custom_fieldvalue' => 'Custom value',
        ]);
    }

    #[Test]
    public function it_displays_online_payment_logs(): void
    {
        PaymentLog::factory()->count(5)->create();

        $response = $this->get(route('payments.onlineLogs'));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs) {
            return $logs->count() === 5;
        });
    }

    #[Test]
    public function it_filters_online_payment_logs_by_search(): void
    {
        PaymentLog::factory()->create(['transaction_id' => 'TXN123ABC']);
        PaymentLog::factory()->create(['transaction_id' => 'TXN456DEF']);

        $response = $this->get(route('payments.onlineLogs', ['search' => '123']));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs) {
            return $logs->count() === 1;
        });
    }

    #[Test]
    public function it_filters_online_payment_logs_by_date_range(): void
    {
        PaymentLog::factory()->create(['created_at' => now()->subDays(10)]);
        PaymentLog::factory()->create(['created_at' => now()->subDays(5)]);
        PaymentLog::factory()->create(['created_at' => now()]);

        $response = $this->get(route('payments.onlineLogs', [
            'date_from' => now()->subDays(6)->format('Y-m-d'),
            'date_to'   => now()->format('Y-m-d'),
        ]));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs) {
            return $logs->count() === 2;
        });
    }

    #[Test]
    public function it_filters_online_payment_logs_by_status(): void
    {
        PaymentLog::factory()->create(['status' => 'completed']);
        PaymentLog::factory()->create(['status' => 'completed']);
        PaymentLog::factory()->create(['status' => 'failed']);

        $response = $this->get(route('payments.onlineLogs', ['status' => 'completed']));

        $response->assertSuccessful();
        $response->assertViewHas('payment_logs', function ($logs) {
            return $logs->count() === 2;
        });
    }
}

