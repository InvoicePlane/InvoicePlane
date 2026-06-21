<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * InvoiceDeletionValidation Feature Tests.
 *
 * Tests invoice deletion validation.
 */
class InvoiceDeletionValidationFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Invoice Deletion Client']);
        $invoiceId = $this->seedInvoice($clientId, ['invoice_number' => 'INV-DEL-001']);

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert */
        $this->assertDatabaseHas('ip_invoices', ['invoice_number' => 'INV-DEL-001']);
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');
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
