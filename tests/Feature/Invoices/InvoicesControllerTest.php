<?php

namespace Modules\Invoices\Tests\Feature;

use Modules\Crm\Controllers\InvoicesController as GuestInvoicesController;
use Modules\Invoices\Models\Invoice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * InvoicesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal invoice viewing.
 */
#[CoversClass(GuestInvoicesController::class)]

class InvoicesControllerTest extends FeatureTestCase
{
    /**
     * Test index redirects to all status view.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_all_status_view_from_index(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('invoices.index'));

        /* Assert */
        $response->assertRedirect(route('invoices.status', ['status' => 'all']));
    }

    /**
     * Test status method displays only draft invoices.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_draft_invoices_when_draft_status_selected(): void
    {
        /** Arrange */
        $user         = User::factory()->create();
        $draftInvoice = Invoice::factory()->draft()->create();
        $sentInvoice  = Invoice::factory()->sent()->create();

        /** Act */
        $response = $this->actingAs($user)->get('/invoices/status/draft');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::index');
        $response->assertViewHas('invoices');
        $response->assertViewHas('status', 'draft');

        $invoices   = $response->viewData('invoices');
        $invoiceIds = $invoices->pluck('invoice_id')->toArray();
        $this->assertContains($draftInvoice->invoice_id, $invoiceIds);
        $this->assertNotContains($sentInvoice->invoice_id, $invoiceIds);
    }

    /**
     * Test status method displays all invoices when all selected.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_all_invoices_when_all_status_selected(): void
    {
        /** Arrange */
        $user         = User::factory()->create();
        $draftInvoice = Invoice::factory()->draft()->create();
        $sentInvoice  = Invoice::factory()->sent()->create();
        $paidInvoice  = Invoice::factory()->paid()->create();

        /** Act */
        $response = $this->actingAs($user)->get('/invoices/status/all');

        /* Assert */
        $response->assertOk();
        $invoices   = $response->viewData('invoices');
        $invoiceIds = $invoices->pluck('invoice_id')->toArray();

        $this->assertContains($draftInvoice->invoice_id, $invoiceIds);
        $this->assertContains($sentInvoice->invoice_id, $invoiceIds);
        $this->assertContains($paidInvoice->invoice_id, $invoiceIds);
    }

    /**
     * Test status method includes invoice statuses in view data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_includes_invoice_statuses_in_view_data_for_status_method(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get('/invoices/status/all');

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('invoice_statuses');
        $response->assertViewHas('status', 'all');
    }

    /**
     * Test view displays invoice details with items and amounts.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_invoice_details_with_items_and_amounts(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->create();
        Item::factory()->count(3)->create(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->actingAs($user)->get(route('invoices.view', ['invoiceId' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('invoice');
        $response->assertViewHas('items');
        $invoice_id = $response->viewData('invoice_id');
        $items      = $response->viewData('items');
        $this->assertEquals($invoice->invoice_id, $invoice_id);
        $this->assertCount(3, $items);
    }

    /**
     * Test view returns 404 for non-existent invoice.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_viewing_non_existent_invoice(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.view', ['invoiceId' => 99999]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test view includes custom fields in data.
     */
    #[Group('exotic')]
    #[Test]
    public function it_includes_custom_fields_in_invoice_view_data(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('invoices.view', ['invoiceId' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('custom_fields');
        $response->assertViewHas('custom_values');
    }

    /**
     * Test view includes tax rates.
     */
    #[Group('exotic')]
    #[Test]
    public function it_includes_tax_rates_in_invoice_view_data(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->create();
        TaxRate::factory()->count(5)->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('invoices.view', ['invoiceId' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('tax_rates');
        $taxRates = $response->viewData('tax_rates');
        $this->assertGreaterThanOrEqual(5, count($taxRates));
    }

    /**
     * Test deleting draft invoice succeeds.
     */
    #[Group('smoke')]
    #[Test]
    public function it_deletes_draft_invoice_and_redirects_to_index(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->draft()->create();

        /**
         * {
         *     "invoiceId": 1
         * }.
         */
        $deleteParams = [
            'invoiceId' => $invoice->invoice_id,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('invoices.delete', $deleteParams));

        /* Assert */
        $response->assertRedirect();
        $this->assertNull(Invoice::find($invoice->invoice_id));
    }

    /**
     * Test deleting draft invoice with tasks updates task status.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_task_status_when_deleting_invoice_with_tasks(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->draft()->create();
        $task    = Task::factory()->create(['invoice_id' => $invoice->invoice_id, 'task_status' => 4]);

        /**
         * {
         *     "invoiceId": 1
         * }.
         */
        $deleteParams = [
            'invoiceId' => $invoice->invoice_id,
        ];

        /* Act */
        $this->actingAs($user)->post(route('invoices.delete', $deleteParams));

        /** Assert */
        $updatedTask = Task::find($task->task_id);
        $this->assertEquals(3, $updatedTask->task_status); // 3 = Complete
    }

    /**
     * Test deleting non-draft invoice when deletion disabled shows error.
     */
    #[Group('smoke')]
    #[Test]
    public function it_shows_error_when_deleting_non_draft_invoice_and_deletion_disabled(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        config(['settings.enable_invoice_deletion' => false]);
        $invoice = Invoice::factory()->sent()->create(); // Not a draft

        /**
         * {
         *     "invoiceId": 1
         * }.
         */
        $deleteParams = [
            'invoiceId' => $invoice->invoice_id,
        ];

        /** Act */
        $response = $this->actingAs($user)->post(route('invoices.delete', $deleteParams));

        /* Assert */
        $this->assertNotNull(Invoice::find($invoice->invoice_id)); // Still exists
        $response->assertSessionHas('alert_error');
    }

    /**
     * Test archive displays archived invoices.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_archived_invoices_list(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        $response = $this->actingAs($user)->get(route('invoices.archive'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::archive');
        $response->assertViewHas('invoices_archive');
    }

    /**
     * Test download validates file path for security.
     */
    #[Group('exotic')]
    #[Test]
    public function it_prevents_directory_traversal_when_downloading_invoice(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.download', ['filename' => '../../../etc/passwd']));

        /* Assert - Expect 404 for invalid path */
        $response->assertNotFound();
    }

    /**
     * Test download returns 404 for non-existent file.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_downloading_non_existent_file(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.download', ['filename' => 'non-existent-file.pdf']));

        /* Assert - Expect 404 */
        $response->assertNotFound();
    }

    /**
     * Test deleting invoice tax rate triggers recalculation.
     */
    #[Group('exotic')]
    #[Test]
    public function it_recalculates_invoice_after_deleting_tax_rate(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->create();
        $taxRate = InvoiceTaxRate::factory()->create(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        /**
         * Note: Empty payload is correct - IDs are passed via route parameters
         * Route: POST /invoices/delete-tax/{invoiceId}/{taxRateId}.
         */
        $payload = [];

        $response = $this->actingAs($user)->post(
            route('invoices.delete_tax', [
                'invoiceId' => $invoice->invoice_id,
                'taxRateId' => $taxRate->invoice_tax_rate_id,
            ]),
            $payload
        );

        /* Assert */
        $response->assertRedirect();
        $this->assertNull(InvoiceTaxRate::find($taxRate->invoice_tax_rate_id));
    }

    /**
     * Test deleting tax rate redirects to invoice view.
     */
    #[Test]
    public function it_redirects_to_invoice_view_after_deleting_tax_rate(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->create();
        $taxRate = InvoiceTaxRate::factory()->create(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        /**
         * Note: Empty payload is correct - IDs are passed via route parameters
         * Route: POST /invoices/delete-tax/{invoiceId}/{taxRateId}.
         */
        $payload = [];

        $response = $this->actingAs($user)->post(
            route('invoices.delete_tax', [
                'invoiceId' => $invoice->invoice_id,
                'taxRateId' => $taxRate->invoice_tax_rate_id,
            ]),
            $payload
        );

        /* Assert */
        $response->assertRedirect(route('invoices.view', ['invoiceId' => $invoice->invoice_id]));
    }

    /**
     * Test recalculating all invoices processes all records.
     */
    #[Group('exotic')]
    #[Test]
    public function it_recalculates_all_invoices_in_system(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        Invoice::factory()->count(5)->create();
        $initialCount = Invoice::count();

        /** Act */
        /**
         * {}.
         */
        $recalculatePayload = [];

        $response = $this->actingAs($user)->post(route('invoices.recalculate_all'), $recalculatePayload);

        /* Assert */
        $response->assertRedirect();
        $this->assertEquals($initialCount, Invoice::count()); // All still exist
        $response->assertSessionHas('alert_success');
    }

    /**
     * Test recalculating invoices handles empty list.
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_empty_invoice_list_when_recalculating_all(): void
    {
        /* Arrange - No invoices */
        $user = User::factory()->create();
        Invoice::truncate();

        /** Act */
        /**
         * {}.
         */
        $recalculatePayload = [];

        $response = $this->actingAs($user)->post(route('invoices.recalculate_all'), $recalculatePayload);

        /* Assert */
        $response->assertRedirect();
        /* Should not throw exception */
    }

    /**
     * Test generating PDF marks invoice as sent when configured.
     */
    #[Test]
    public function it_marks_invoice_as_sent_when_generating_pdf_and_setting_enabled(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        config(['settings.mark_invoices_sent_pdf' => 1]);
        $invoice = Invoice::factory()->draft()->create();

        /* Act */
        $this->actingAs($user)->get(route('invoices.generate_pdf', ['id' => $invoice->invoice_id]));

        /** Assert */
        $updatedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertNotEquals(1, $updatedInvoice->invoice_status_id); // Not draft anymore
    }

    /**
     * Test displaying paid invoices filters correctly.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_paid_invoices_when_paid_status_selected(): void
    {
        /** Arrange */
        $user         = User::factory()->create();
        $draftInvoice = Invoice::factory()->draft()->create();
        $paidInvoice  = Invoice::factory()->paid()->create();

        /** Act */
        $response = $this->actingAs($user)->get('/invoices/status/paid');

        /* Assert */
        $response->assertOk();
        $invoices   = $response->viewData('invoices');
        $invoiceIds = $invoices->pluck('invoice_id')->toArray();

        $this->assertContains($paidInvoice->invoice_id, $invoiceIds);
        $this->assertNotContains($draftInvoice->invoice_id, $invoiceIds);
    }

    /**
     * Test displaying overdue invoices filters correctly.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_overdue_invoices_when_overdue_status_selected(): void
    {
        /** Arrange */
        $user           = User::factory()->create();
        $overdueInvoice = Invoice::factory()->overdue()->create();
        $paidInvoice    = Invoice::factory()->paid()->create();

        /** Act */
        $response = $this->actingAs($user)->get('/invoices/status/overdue');

        /* Assert */
        $response->assertOk();
        $invoices   = $response->viewData('invoices');
        $invoiceIds = $invoices->pluck('invoice_id')->toArray();

        $this->assertContains($overdueInvoice->invoice_id, $invoiceIds);
        $this->assertNotContains($paidInvoice->invoice_id, $invoiceIds);
    }

    /**
     * Test view uses SUMEX template when invoice has sumex_id.
     */
    #[Test]
    public function it_uses_sumex_template_when_invoice_has_sumex_id(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $invoice = Invoice::factory()->create(['sumex_id' => 12345]);

        /** Act */
        $response = $this->actingAs($user)->get(route('invoices.view', ['invoiceId' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::view_sumex');
    }

    /**
     * Test view pagination works correctly.
     */
    #[Test]
    public function it_paginates_invoice_results_correctly(): void
    {
        /* Arrange */
        $user = User::factory()->create();
        Invoice::factory()->count(30)->create();

        /** Act */
        $response = $this->actingAs($user)->get('/invoices/status/all');

        /* Assert */
        $response->assertOk();
        $invoices = $response->viewData('invoices');
        $this->assertLessThanOrEqual(15, $invoices->count());
    }
}
