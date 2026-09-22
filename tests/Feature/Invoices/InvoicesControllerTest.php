<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Invoices controller — application/modules/invoices/controllers/Invoices.php.
 *
 * Invoices are created/edited through invoices/ajax/* (see
 * InvoicesAjaxControllerTest). This controller lists, views, deletes and
 * strips tax rates. Invoices::delete() only removes the row when the invoice
 * is still a draft (invoice_status_id === 1) or ENABLE_INVOICE_DELETION is on;
 * any other status is silently refused. Absorbs
 * InvoiceDeletionValidationFeatureTest, Issue1694InvoiceDeleteCsrfTest and
 * Issue1694InvoiceTaxRateDeleteCsrfTest.
 */
#[Group('invoices')]
#[CoversClass(\Invoices::class)]
class InvoicesControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Invoice List Client']);
        $this->seedInvoice($clientId, ['invoice_number' => 'INV-LIST-0001']);
        $this->seedInvoice($clientId, ['invoice_number' => 'INV-LIST-0002']);

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert */
        $this->assertResponseBodyContains($response, 'INV-LIST-0001');
        $this->assertResponseBodyContains($response, 'INV-LIST-0002');
    }

    #[Test]
    public function it_shows_a_single_invoice(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Invoice View Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'INV-VIEW-0007']);

        /* Act */
        $response = $this->get('/invoices/view/' . $invoiceId);

        /* Assert */
        $this->assertResponseBodyContains($response, 'INV-VIEW-0007');
        $this->assertResponseBodyContains($response, 'Invoice View Client');
    }

    // -------------------------------------------------------------------------
    // Delete — business rule: only drafts are deletable by default
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_draft_invoice(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);
        $keepId    = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);

        /* Act */
        $response = $this->post('/invoices/delete/' . $invoiceId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete redirects back to the invoice list.');
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $keepId]);
    }

    #[Test]
    public function it_refuses_to_delete_a_sent_invoice_while_deletion_is_disabled(): void
    {
        /* Arrange */
        $this->withEnvironment(['ENABLE_INVOICE_DELETION' => 'false']);
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 2]);

        /* Act */
        $response = $this->post('/invoices/delete/' . $invoiceId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A refused delete still redirects back to the list.');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
    }

    #[Test]
    public function it_refuses_to_delete_a_paid_invoice_while_deletion_is_disabled(): void
    {
        /* Arrange */
        $this->withEnvironment(['ENABLE_INVOICE_DELETION' => 'false']);
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 4, 'invoice_number' => 'INV-PAID-KEPT']);

        /* Act */
        $response = $this->post('/invoices/delete/' . $invoiceId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A refused delete still redirects back to the list.');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_number' => 'INV-PAID-KEPT']);
    }

    #[Test]
    public function it_deletes_a_sent_invoice_when_global_invoice_deletion_is_enabled(): void
    {
        /* Arrange */
        $this->withEnvironment(['ENABLE_INVOICE_DELETION' => 'true']);
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 2]);
        $keepId    = $this->seedInvoice($clientId, ['invoice_status_id' => 2]);

        /* Act */
        $response = $this->post('/invoices/delete/' . $invoiceId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete redirects back to the invoice list.');
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $keepId, 'invoice_status_id' => 2]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_draft_invoice_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 1]);

        /* Act */
        $response = $this->postWithValidCsrfToken('/invoices/delete/' . $invoiceId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token delete redirects.');
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_does_not_delete_an_invoice_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 1, 'invoice_number' => 'INV-CSRF-KEPT']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoices/delete/' . $invoiceId);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_number' => 'INV-CSRF-KEPT']);
    }

    // -------------------------------------------------------------------------
    // Invoice tax rates — Invoices::delete_invoice_tax (#1694 regression)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_removes_a_tax_rate_from_an_invoice(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient());
        $rateId    = $this->databaseInsert('ip_invoice_tax_rates', [
            'invoice_id'              => $invoiceId,
            'tax_rate_id'             => 1,
            'include_item_tax'        => 0,
            'invoice_tax_rate_amount' => '0.00',
        ]);
        $keepId = $this->databaseInsert('ip_invoice_tax_rates', [
            'invoice_id'              => $invoiceId,
            'tax_rate_id'             => 1,
            'include_item_tax'        => 0,
            'invoice_tax_rate_amount' => '0.00',
        ]);

        /* Act */
        $response = $this->post('/invoices/delete_invoice_tax/' . $invoiceId . '/' . $rateId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Removing a tax rate redirects back to the invoice.');
        $this->assertDatabaseMissing('ip_invoice_tax_rates', ['invoice_tax_rate_id' => $rateId]);
        $this->assertDatabaseHas('ip_invoice_tax_rates', ['invoice_tax_rate_id' => $keepId]);
    }

    #[Test]
    public function it_does_not_remove_an_invoice_tax_rate_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $invoiceId = $this->seedInvoice($this->seedClient());
        $rateId    = $this->databaseInsert('ip_invoice_tax_rates', [
            'invoice_id'              => $invoiceId,
            'tax_rate_id'             => 1,
            'include_item_tax'        => 0,
            'invoice_tax_rate_amount' => '0.00',
        ]);

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoices/delete_invoice_tax/' . $invoiceId . '/' . $rateId);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less request must not reach the controller.');
        $this->assertDatabaseHas('ip_invoice_tax_rates', ['invoice_tax_rate_id' => $rateId]);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Secret Invoice Client']);
        $this->seedInvoice($clientId, ['invoice_number' => 'INV-SECRET-0001']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'INV-SECRET-0001');
    }
}
