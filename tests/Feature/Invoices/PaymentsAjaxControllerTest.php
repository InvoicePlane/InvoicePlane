<?php

namespace Modules\Payments\tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Payments\app\Http\Controllers\PaymentMethodsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\PaymentMethods\route;

use Tests\TestCase;

#[CoversClass(PaymentMethodsController::class)]

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
