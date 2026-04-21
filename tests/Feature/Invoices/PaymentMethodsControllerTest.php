<?php

namespace Tests\Feature\Invoices;

use Modules\Payments\app\Http\Controllers\PaymentMethodsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

use function Tests\Feature\PaymentMethods\route;

use Tests\TestCase;

#[CoversClass(PaymentMethodsController::class)]

class PaymentMethodsControllerTest extends TestCase
{
    use InteractsWithDatabase;

    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
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
        $this->seedModel('PaymentMethod', ['payment_method_name' => 'Existing Method']);

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
        $method = $this->seedModel('PaymentMethod', ['payment_method_name' => 'Original']);

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
        $method = $this->seedModel('PaymentMethod');

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
