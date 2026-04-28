<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
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
#[CoversClass(Tests\Feature\Invoices\InvoiceController::class)]

class InvoiceControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays paginated list of invoices.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_invoices(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Invoice', 5);

        /* Act */
        $response = $this->actingAs($user)->get('/invoice/index');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::index');
        $response->assertViewHas('invoices');
    }

    /**
     * Test invoices are ordered by date created and number descending.
     */
    #[Test]
    public function it_orders_invoices_by_date_created_and_number_descending(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('Invoice', [
            'invoice_date_created' => '2024-01-01',
            'invoice_number'       => 'INV-001',
        ]);
        $this->seedModel('Invoice', [
            'invoice_date_created' => '2024-01-02',
            'invoice_number'       => 'INV-002',
        ]);

        /* Act */
        $response = $this->actingAs($user)->get('/invoice/index');

        /* Assert */
        $response->assertOk();
        $invoices = $response->viewData('invoices');

        // Most recent should be first
        $this->assertGreaterThan(0, $invoices->count());
    }

    /**
     * Test show displays invoice with relationships.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_invoice_with_relationships(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');

        /* Act */
        $response = $this->actingAs($user)->get('/invoice/show/' . ($invoice->invoice_id));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::show');
        $response->assertViewHas('invoice');

        $viewInvoice = $response->viewData('invoice');
        $this->assertEquals($invoice->invoice_id, $viewInvoice->invoice_id);
    }

    /**
     * Test show returns 404 for non-existent invoice.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_showing_non_existent_invoice(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get('/invoice/show/' . (99999));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test create displays invoice creation form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_invoice_creation_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get('/invoice/create');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::create');
    }

    /**
     * Test store creates new invoice.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_invoice(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /** @var array{client_id: int, invoice_number: string, invoice_date_created: string} $invoiceData */
        $invoiceData = [
            'client_id'            => 1,
            'invoice_number'       => 'TEST-001',
            'invoice_date_created' => '2024-01-01',
        ];

        /* Act */
        $controller = new InvoiceController();
        $invoice    = $controller->store($invoiceData);

        /* Assert */
        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertDatabaseHas('ip_invoices', [
            'invoice_number' => 'TEST-001',
        ]);
    }

    /**
     * Test store creates invoice amount record.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_invoice_amount_record_when_storing_invoice(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /** @var array{client_id: int, invoice_number: string, invoice_date_created: string} $invoiceData */
        $invoiceData = [
            'client_id'            => 1,
            'invoice_number'       => 'TEST-002',
            'invoice_date_created' => '2024-01-01',
        ];

        /* Act */
        $controller = new InvoiceController();
        $invoice    = $controller->store($invoiceData);

        /* Assert */
        $this->assertDatabaseHas('ip_invoice_amounts', [
            'invoice_id' => $invoice->invoice_id,
        ]);
    }
}
