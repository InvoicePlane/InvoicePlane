<?php

namespace Modules\Invoices\tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\InvoiceGroups\Tests\Feature\WithFaker;
use Modules\Invoices\app\Http\Controllers\InvoiceGroupsController;
use Modules\Invoices\app\Models\InvoiceGroup;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

use function Tests\Feature\InvoiceGroups\route;

use Tests\TestCase;

#[CoversClass(InvoiceGroupsController::class)]

class RecurringInvoicesControllerTest extends TestCase
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
    public function it_displays_recurring_invoices_index(): void
    {
        $response = $this->get(route('invoices.recurring.index'));

        $response->assertSuccessful();
        $response->assertViewHas('recurring_invoices');
        $response->assertViewHas('recur_frequencies');
    }

    #[Test]
    public function it_stops_recurring_invoice(): void
    {
        $recurringInvoice = RecurringInvoice::factory()->create(['status' => 'active']);

        $response = $this->post(route('invoices.recurring.stop', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]));

        $response->assertRedirect(route('invoices.recurring.index'));
        $this->assertDatabaseHas('ip_invoices_recurring', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
            'status'               => 'stopped',
        ]);
    }

    #[Test]
    public function it_deletes_recurring_invoice(): void
    {
        $recurringInvoice = RecurringInvoice::factory()->create();

        $response = $this->delete(route('invoices.recurring.delete', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]));

        $response->assertRedirect(route('invoices.recurring.index'));
        $this->assertDatabaseMissing('ip_invoices_recurring', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]);
    }
}
