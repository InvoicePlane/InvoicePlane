<?php

namespace Tests\Feature\Invoices;

use Modules\Core\Models\User;
use Modules\InvoiceGroups\Tests\Feature\WithFaker;
use Modules\Invoices\app\Http\Controllers\InvoiceGroupsController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;


use Tests\TestCase;

#[CoversClass(InvoiceGroupsController::class)]

class RecurringInvoicesControllerTest extends TestCase
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
        $recurringInvoice = $this->seedModel('RecurringInvoice', ['status' => 'active']);

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
        $recurringInvoice = $this->seedModel('RecurringInvoice');

        $response = $this->delete(route('invoices.recurring.delete', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]));

        $response->assertRedirect(route('invoices.recurring.index'));
        $this->assertDatabaseMissing('ip_invoices_recurring', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]);
    }
}
