<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Test coverage for Invoices Controller (application/modules/invoices/controllers/Invoices.php).
 *
 * InvoicesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal invoice viewing.
 */
class InvoicesControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_lists_invoices_for_authenticated_admin(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Invoice List Client']);
        $this->seedInvoice($clientId, ['invoice_number' => 'INV-LIST-001']);

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'INV-LIST-001');
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
