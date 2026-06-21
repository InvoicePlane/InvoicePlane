<?php

namespace Tests\Unit\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Smoke test for the InvoiceItemsServiceTest module via CI3 HTTP harness.
 */
class InvoiceItemsServiceTest extends AbstractTestCase
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
        $clientId = $this->seedClient(['client_name' => 'Inv Items Service Client Sigma']);
        $this->seedInvoice($clientId, ['invoice_number' => 'INV-SIGMA-001']);

        /* Act */
        $response = $this->get('/invoices/status/all');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'INV-SIGMA-001');
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
