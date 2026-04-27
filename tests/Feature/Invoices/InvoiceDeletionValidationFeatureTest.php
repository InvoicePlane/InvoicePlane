<?php

namespace Tests\Feature\Invoices;

use Modules\Crm\Controllers\InvoicesController as GuestInvoicesController;
use Modules\Invoices\Models\Invoice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * InvoicesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal invoice viewing.
 */
#[CoversClass(GuestInvoicesController::class)]
#[CoversClass(Tests\Feature\Invoices\InvoiceDeletionValidationFeature::class)]

class InvoiceDeletionValidationFeatureTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test that draft invoice (status = 1) can be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_draft_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 1, // Draft
            'invoice_number'    => 'DRAFT-001',
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');

        // Verify invoice was deleted
        $this->assertDatabaseMissing('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
        ]);
    }

    /**
     * Test that sent invoice (status = 2) cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_sent_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 2, // Sent
            'invoice_number'    => 'INV-001',
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');
        $response->assertSessionHas('alert_error');

        // Verify invoice still exists
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_id'        => $invoice->invoice_id,
            'invoice_status_id' => 2,
        ]);
    }

    /**
     * Test that viewed invoice (status = 3) cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_viewed_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 3, // Viewed
            'invoice_number'    => 'INV-002',
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    /**
     * Test that paid invoice (status = 4) cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_paid_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 4, // Paid
            'invoice_number'    => 'INV-003',
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    /**
     * Test that overdue invoice (status = 5) cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_overdue_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 5, // Overdue
            'invoice_number'    => 'INV-004',
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    /**
     * Test that deleting draft invoice with tasks unmarks the tasks.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_unmarks_tasks_when_deleting_draft_invoice(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 1, // Draft
        ]);

        // Create tasks assigned to this invoice
        $task1 = $this->seedModel('Task', [
            'invoice_id'  => $invoice->invoice_id,
            'task_status' => 4, // On Hold (invoiced)
        ]);
        $task2 = $this->seedModel('Task', [
            'invoice_id'  => $invoice->invoice_id,
            'task_status' => 4,
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');

        // Verify invoice was deleted
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoice->invoice_id]);

        // Verify tasks were updated to Complete status (3)
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'     => $task1->task_id,
            'task_status' => 3, // Complete
        ]);
        $this->assertDatabaseHas('ip_tasks', [
            'task_id'     => $task2->task_id,
            'task_status' => 3, // Complete
        ]);
    }

    /**
     * Test that all non-draft statuses are blocked.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_blocks_deletion_for_all_non_draft_statuses(): void
    {
        /* Arrange */
        $nonDraftStatuses = [2, 3, 4, 5]; // Sent, Viewed, Paid, Overdue

        foreach ($nonDraftStatuses as $status) {
            $invoice = $this->seedModel('Invoice', [
                'invoice_status_id' => $status,
            ]);

            /* Act */
            $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

            /* Assert */
            $response->assertSessionHas('alert_error');
            $this->assertDatabaseHas('ip_invoices', [
                'invoice_id'        => $invoice->invoice_id,
                'invoice_status_id' => $status,
            ]);
        }
    }

    /**
     * Test that config setting can override deletion restriction.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_when_config_enabled(): void
    {
        /* Arrange */
        // Enable invoice deletion in config
        $originalConfig = config('settings.enable_invoice_deletion');
        config(['settings.enable_invoice_deletion' => true]);

        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 2, // Sent (normally not deletable)
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');

        // Verify invoice was deleted despite being sent
        $this->assertDatabaseMissing('ip_invoices', [
            'invoice_id' => $invoice->invoice_id,
        ]);

        // Restore original config
        config(['settings.enable_invoice_deletion' => $originalConfig]);
    }

    /**
     * Test deletion of draft invoice without tasks.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_draft_invoice_without_tasks(): void
    {
        /* Arrange */
        $invoice = $this->seedModel('Invoice', [
            'invoice_status_id' => 1, // Draft
        ]);

        /* Act */
        $response = $this->post('/invoices/delete/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertRedirect('/invoices/index');
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoice->invoice_id]);
    }

    /**
     * Test that invoice with invalid ID is handled properly.
     */
    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_invoice_id(): void
    {
        /* Arrange */
        $invalidId = 99999;

        /* Act & Assert */
        // This should throw a ModelNotFoundException or return 404
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $this->post('/invoices/delete/' . ($invalidId));
    }
}
