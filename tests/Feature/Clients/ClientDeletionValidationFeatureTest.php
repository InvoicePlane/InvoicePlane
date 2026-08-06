<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * ClientsController deletion behavior.
 *
 * Deleting a client is not blocked by related records — Mdl_clients::delete()
 * cascades: it removes the client row, then delete_orphans() (orphan_helper.php)
 * cleans up every row left dangling by that deletion, including the client's
 * own invoices and notes.
 */
class ClientDeletionValidationFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_deletes_a_client_and_cascades_its_orphaned_invoice_and_notes(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Cascade Delete Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $noteId    = $this->databaseInsert('ip_client_notes', [
            'client_id'        => $clientId,
            'client_note_date' => date('Y-m-d'),
            'client_note'      => 'A note that should be cleaned up',
        ]);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_client_notes', ['client_note_id' => $noteId]);

        /* Act */
        $response = $this->post('/clients/delete/' . $clientId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $clientId]);
        $this->assertDatabaseMissing('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertDatabaseMissing('ip_client_notes', ['client_note_id' => $noteId]);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/clients/status/active');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/clients] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
