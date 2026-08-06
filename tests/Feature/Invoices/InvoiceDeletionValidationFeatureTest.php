<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * InvoicesController deletion business rule.
 *
 * Invoices::delete() only performs the delete when the invoice is still a
 * draft (invoice_status_id === 1) or the enable_invoice_deletion config flag
 * is on (default false, see ipconfig.php.example). Any other status is
 * silently refused — the invoice is left in place and a flash error is set.
 */
class InvoiceDeletionValidationFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_deletes_a_draft_invoice(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Draft Invoice Delete Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 1]);

        /* Act */
        $response = $this->post('/invoices/delete/' . $invoiceId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_does_not_delete_a_sent_invoice_when_deletion_is_disabled(): void
    {
        /**
         * ENABLE_INVOICE_DELETION defaults to false (ipconfig.php.example), so
         * a non-draft invoice must survive a delete attempt.
         */

        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Sent Invoice Delete Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_status_id' => 2]);

        /* Act */
        $response = $this->post('/invoices/delete/' . $invoiceId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Blocked delete still redirects back to the invoice list.');
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
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
}
