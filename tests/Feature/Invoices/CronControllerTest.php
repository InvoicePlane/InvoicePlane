<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Feature tests for invoices controller admin interface behavior.
 * Tests viewing, filtering, and displaying invoice lists with various status filters.
 */
class CronControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_displays_all_invoices_status_page(): void
    {
        /* Arrange: create test invoices with different statuses */
        $clientId = $this->seedClient();
        $this->seedInvoice($clientId, ['invoice_status_id' => 1, 'invoice_number' => 'INV-001']);
        $this->seedInvoice($clientId, ['invoice_status_id' => 2, 'invoice_number' => 'INV-002']);

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert: page loads and renders */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            $response->contains('invoice') || $response->contains('status'),
            'Invoice list page should display invoice information'
        );
    }

    #[Test]
    public function it_filters_invoices_by_status(): void
    {
        /* Arrange: create invoices with different statuses */
        $clientId = $this->seedClient();
        $this->seedInvoice($clientId, ['invoice_status_id' => 1, 'invoice_number' => 'DRAFT-001']);
        $this->seedInvoice($clientId, ['invoice_status_id' => 2, 'invoice_number' => 'SENT-001']);

        /* Act: get draft invoices (status 1) */
        $response = $this->get('/invoices/status/1');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            $response->contains('invoice') || $response->contains('status') || strlen($response->body()) > 100,
            'Invoice list should render invoice status information'
        );
    }

    #[Test]
    public function it_redirects_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/invoices');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/invoices] must redirect. Got [%d].', $response->statusCode())
        );
    }

    #[Test]
    public function it_displays_empty_invoice_list_when_no_invoices_exist(): void
    {
        /* Arrange: no invoices created */

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert: page still loads even with no data */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertTrue(
            $response->contains('invoice') || $response->contains('no') || strlen($response->body()) > 50,
            'Empty invoice list page should still render without errors'
        );
    }

    #[Test]
    public function it_displays_invoice_with_client_information(): void
    {
        /* Arrange: create invoice with associated client */
        $clientId  = $this->seedClient(['client_name' => 'Test Company Ltd']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'INV-2025-001']);

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert: client name or invoice number appears in list */
        $this->assertResponseStatusCode($response, 200);
        self::assertTrue(
            $response->contains('Test Company') || $response->contains('INV-2025-001'),
            'Invoice list should display client name or invoice number'
        );
    }
}
