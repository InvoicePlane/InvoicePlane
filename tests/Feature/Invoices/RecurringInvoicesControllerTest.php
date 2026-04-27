<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Recurring;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Recurring::class)]
#[CoversClass(Tests\Feature\Invoices\RecurringInvoicesController::class)]

class RecurringInvoicesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_recurring_invoices_index(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get('/invoices/recurring/index');

        $response->assertSuccessful();
        $response->assertViewHas('recurring_invoices');
        $response->assertViewHas('recur_frequencies');
    }

    #[Test]
    public function it_stops_recurring_invoice(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $recurringInvoice = $this->seedModel('RecurringInvoice', ['status' => 'active']);

        $response = $this->post('/invoices/recurring/stop/' . ($recurringInvoice->invoice_recurring_id));

        $response->assertRedirect('/invoices/recurring/index');
        $this->assertDatabaseHas('ip_invoices_recurring', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
            'status'               => 'stopped',
        ]);
    }

    #[Test]
    public function it_deletes_recurring_invoice(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $recurringInvoice = $this->seedModel('RecurringInvoice');

        $response = $this->delete('/invoices/recurring/delete/' . ($recurringInvoice->invoice_recurring_id));

        $response->assertRedirect('/invoices/recurring/index');
        $this->assertDatabaseMissing('ip_invoices_recurring', [
            'invoice_recurring_id' => $recurringInvoice->invoice_recurring_id,
        ]);
    }
}
