<?php

namespace Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Payment_Information.
 *
 * Tests HTTP endpoints for the payments list.
 */
class PaymentInformationControllerTest extends AbstractTestCase
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
        $clientId  = $this->seedClient(['client_name' => 'Payment Info Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $this->seedPayment($invoiceId);

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/payments] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
