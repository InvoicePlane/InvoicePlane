<?php

namespace Tests\Feature\Controllers;

use Modules\Crm\Models\Task;
use Modules\Invoices\Controllers\InvoicesController;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceTaxRate;
use Modules\Invoices\Models\Item;
use Modules\Products\Models\TaxRate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Test suite for InvoicesController.
 *
 * Tests invoice viewing, status filtering, PDF generation, and management
 */
#[CoversClass(InvoicesController::class)]
class InvoicesControllerTest extends TestCase
{
    private InvoicesController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new InvoicesController();
    }

    /**
     * Test index redirects to all status view.
     */
    #[Test]
    public function it_redirects_to_all_status_view_from_index(): void
    {
        /** Act */
        $response = $this->controller->index();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('invoices.status', ['status' => 'all']), $response->getTargetUrl());
    }

    /**
     * Test status method displays only draft invoices.
     */
    #[Test]
    public function it_displays_only_draft_invoices_when_draft_status_selected(): void
    {
        /** Arrange */
        $draftInvoice = Invoice::factory()->draft()->create();
        $sentInvoice  = Invoice::factory()->sent()->create();

        /** Act */
        $response = $this->controller->status('draft');

        /* Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();

        $invoiceIds = $viewData['invoices']->pluck('invoice_id')->toArray();
        $this->assertContains($draftInvoice->invoice_id, $invoiceIds);
        $this->assertNotContains($sentInvoice->invoice_id, $invoiceIds);
    }

    /**
     * Test status method displays all invoices when all selected.
     */
    #[Test]
    public function it_displays_all_invoices_when_all_status_selected(): void
    {
        /** Arrange */
        $draftInvoice = Invoice::factory()->draft()->create();
        $sentInvoice  = Invoice::factory()->sent()->create();
        $paidInvoice  = Invoice::factory()->paid()->create();

        /** Act */
        $response = $this->controller->status('all');

        /** Assert */
        $viewData   = $response->getData();
        $invoiceIds = $viewData['invoices']->pluck('invoice_id')->toArray();

        $this->assertContains($draftInvoice->invoice_id, $invoiceIds);
        $this->assertContains($sentInvoice->invoice_id, $invoiceIds);
        $this->assertContains($paidInvoice->invoice_id, $invoiceIds);
    }

    /**
     * Test status method includes invoice statuses in view data.
     */
    #[Test]
    public function it_includes_invoice_statuses_in_view_data_for_status_method(): void
    {
        /** Act */
        $response = $this->controller->status('all');

        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('invoice_statuses', $viewData);
        $this->assertArrayHasKey('status', $viewData);
        $this->assertEquals('all', $viewData['status']);
    }

    /**
     * Test view displays invoice details with items and amounts.
     */
    #[Test]
    public function it_displays_invoice_details_with_items_and_amounts(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        Item::factory()->count(3)->create(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->controller->view($invoice->invoice_id);

        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('invoice', $viewData);
        $this->assertArrayHasKey('items', $viewData);
        $this->assertEquals($invoice->invoice_id, $viewData['invoice']->invoice_id);
        $this->assertCount(3, $viewData['items']);
    }

    /**
     * Test view returns 404 for non-existent invoice.
     */
    #[Test]
    public function it_returns_404_when_viewing_non_existent_invoice(): void
    {
        /* Expect exception */
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        /* Act */
        $this->controller->view(99999);
    }

    /**
     * Test view includes custom fields in data.
     */
    #[Test]
    public function it_includes_custom_fields_in_invoice_view_data(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();

        /** Act */
        $response = $this->controller->view($invoice->invoice_id);

        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('custom_fields', $viewData);
        $this->assertArrayHasKey('custom_values', $viewData);
    }

    /**
     * Test view includes tax rates.
     */
    #[Test]
    public function it_includes_tax_rates_in_invoice_view_data(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        TaxRate::factory()->count(5)->create();

        /** Act */
        $response = $this->controller->view($invoice->invoice_id);

        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('tax_rates', $viewData);
        $this->assertGreaterThanOrEqual(5, count($viewData['tax_rates']));
    }

    /**
     * Test deleting draft invoice succeeds.
     */
    #[Test]
    public function it_deletes_draft_invoice_and_redirects_to_index(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();

        /** Act */
        $response = $this->controller->delete($invoice->invoice_id);

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertNull(Invoice::find($invoice->invoice_id));
    }

    /**
     * Test deleting draft invoice with tasks updates task status.
     */
    #[Test]
    public function it_updates_task_status_when_deleting_invoice_with_tasks(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $task    = Task::factory()->create(['invoice_id' => $invoice->invoice_id, 'task_status' => 4]);

        /* Act */
        $this->controller->delete($invoice->invoice_id);

        /** Assert */
        $updatedTask = Task::find($task->task_id);
        $this->assertEquals(3, $updatedTask->task_status); // 3 = Complete
    }

    /**
     * Test deleting non-draft invoice when deletion disabled shows error.
     */
    #[Test]
    public function it_shows_error_when_deleting_non_draft_invoice_and_deletion_disabled(): void
    {
        /* Arrange */
        config(['settings.enable_invoice_deletion' => false]);
        $invoice = Invoice::factory()->sent()->create(); // Not a draft

        /** Act */
        $response = $this->controller->delete($invoice->invoice_id);

        /* Assert */
        $this->assertNotNull(Invoice::find($invoice->invoice_id)); // Still exists
        $this->assertTrue(session()->has('alert_error'));
    }

    /**
     * Test archive displays archived invoices.
     */
    #[Test]
    public function it_displays_archived_invoices_list(): void
    {
        /** Act */
        $response = $this->controller->archive();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('invoices_archive', $viewData);
    }

    /**
     * Test download validates file path for security.
     */
    #[Test]
    public function it_prevents_directory_traversal_when_downloading_invoice(): void
    {
        /* Expect 404 for invalid path */
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        /* Act - Try directory traversal */
        $this->controller->download('../../../etc/passwd');
    }

    /**
     * Test download returns 404 for non-existent file.
     */
    #[Test]
    public function it_returns_404_when_downloading_non_existent_file(): void
    {
        /* Expect 404 */
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);

        /* Act */
        $this->controller->download('non-existent-file.pdf');
    }

    /**
     * Test deleting invoice tax rate triggers recalculation.
     */
    #[Test]
    public function it_recalculates_invoice_after_deleting_tax_rate(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        $taxRate = InvoiceTaxRate::factory()->create(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->controller->deleteInvoiceTax($invoice->invoice_id, $taxRate->invoice_tax_rate_id);

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertNull(InvoiceTaxRate::find($taxRate->invoice_tax_rate_id));
    }

    /**
     * Test deleting tax rate redirects to invoice view.
     */
    #[Test]
    public function it_redirects_to_invoice_view_after_deleting_tax_rate(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create();
        $taxRate = InvoiceTaxRate::factory()->create(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->controller->deleteInvoiceTax($invoice->invoice_id, $taxRate->invoice_tax_rate_id);

        /** Assert */
        $expectedUrl = route('invoices.view', ['invoiceId' => $invoice->invoice_id]);
        $this->assertEquals($expectedUrl, $response->getTargetUrl());
    }

    /**
     * Test recalculating all invoices processes all records.
     */
    #[Test]
    public function it_recalculates_all_invoices_in_system(): void
    {
        /* Arrange */
        Invoice::factory()->count(5)->create();
        $initialCount = Invoice::count();

        /** Act */
        $response = $this->controller->recalculateAllInvoices();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals($initialCount, Invoice::count()); // All still exist
        $this->assertTrue(session()->has('alert_success'));
    }

    /**
     * Test recalculating invoices handles empty list.
     */
    #[Test]
    public function it_handles_empty_invoice_list_when_recalculating_all(): void
    {
        /* Arrange - No invoices */
        Invoice::truncate();

        /** Act */
        $response = $this->controller->recalculateAllInvoices();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        /* Should not throw exception */
    }

    /**
     * Test generating PDF marks invoice as sent when configured.
     */
    #[Test]
    public function it_marks_invoice_as_sent_when_generating_pdf_and_setting_enabled(): void
    {
        /* Arrange */
        config(['settings.mark_invoices_sent_pdf' => 1]);
        $invoice = Invoice::factory()->draft()->create();

        /* Act */
        $this->controller->generatePdf($invoice->invoice_id);

        /** Assert */
        $updatedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertNotEquals(1, $updatedInvoice->invoice_status_id); // Not draft anymore
    }

    /**
     * Test displaying paid invoices filters correctly.
     */
    #[Test]
    public function it_displays_only_paid_invoices_when_paid_status_selected(): void
    {
        /** Arrange */
        $draftInvoice = Invoice::factory()->draft()->create();
        $paidInvoice  = Invoice::factory()->paid()->create();

        /** Act */
        $response = $this->controller->status('paid');

        /** Assert */
        $viewData   = $response->getData();
        $invoiceIds = $viewData['invoices']->pluck('invoice_id')->toArray();

        $this->assertContains($paidInvoice->invoice_id, $invoiceIds);
        $this->assertNotContains($draftInvoice->invoice_id, $invoiceIds);
    }

    /**
     * Test displaying overdue invoices filters correctly.
     */
    #[Test]
    public function it_displays_only_overdue_invoices_when_overdue_status_selected(): void
    {
        /** Arrange */
        $overdueInvoice = Invoice::factory()->overdue()->create();
        $paidInvoice    = Invoice::factory()->paid()->create();

        /** Act */
        $response = $this->controller->status('overdue');

        /** Assert */
        $viewData   = $response->getData();
        $invoiceIds = $viewData['invoices']->pluck('invoice_id')->toArray();

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
        $invoice = Invoice::factory()->create(['sumex_id' => 12345]);

        /** Act */
        $response = $this->controller->view($invoice->invoice_id);

        /* Assert */
        $this->assertEquals('invoices::view_sumex', $response->name());
    }

    /**
     * Test view pagination works correctly.
     */
    #[Test]
    public function it_paginates_invoice_results_correctly(): void
    {
        /* Arrange */
        Invoice::factory()->count(30)->create();

        /** Act */
        $response = $this->controller->status('all', 0);

        /** Assert */
        $viewData = $response->getData();
        $this->assertLessThanOrEqual(15, $viewData['invoices']->count());
    }
}
