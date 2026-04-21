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

class CrmInvoicesControllerTest extends FeatureTestCase
{
    /**
     * Test index displays guest invoices list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_invoices_list(): void
    {
        /** Arrange */
        // Guest portal accessible without authentication

        /** Act */
        $response = $this->get(route('guest.invoices'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_invoices');
    }

    /**
     * Test view displays specific invoice by URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_invoice_by_url_key(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create(['invoice_url_key' => 'test-key-123']);

        /** Act */
        $response = $this->get(route('guest.invoices.view', ['urlKey' => 'test-key-123']));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_invoice_view');
        $response->assertViewHas('invoice');

        $viewInvoice = $response->viewData('invoice');
        $this->assertEquals($invoice->invoice_id, $viewInvoice->invoice_id);
    }

    /**
     * Test view returns 404 for invalid URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_for_invalid_url_key(): void
    {
        /** Arrange */
        // No invoice with this URL key

        /** Act */
        $response = $this->get(route('guest.invoices.view', ['urlKey' => 'non-existent-key']));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test invoice view is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->create(['invoice_url_key' => 'guest-key']);

        /** Act */
        $response = $this->get(route('guest.invoices.view', ['urlKey' => 'guest-key']));

        /* Assert */
        $response->assertOk();
    }
}
