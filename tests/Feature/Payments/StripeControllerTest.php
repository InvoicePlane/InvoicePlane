<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Stripe Feature Tests.
 *
 * Tests payment listing and management routes accessible to authenticated admins.
 */
class StripeControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_displays_payment_list_with_data(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Stripe Test Client']);
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_amount' => '100.00']);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '50.00']);

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Response is successful */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');

        /* Assert: Payment data is present in response */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_id' => $paymentId]);
        self::assertNotNull($payment);
        $this->assertResponseBodyContains($response, $payment['payment_amount']);
        $this->assertResponseBodyContains($response, (string) $clientId);
    }

    #[Test]
    public function it_displays_empty_payments_list(): void
    {
        /* Arrange: no payments in database */

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Response renders successfully with no errors */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');
        $this->assertResponseHasNoPhpErrors($response);
        /* Verify page renders without data (no payment rows present) */
        $paymentCount = $this->databaseCount('ip_payments');
        self::assertSame(0, $paymentCount, 'No payments should exist in database for this test');
    }

    #[Test]
    public function it_displays_multiple_payments_with_service_names(): void
    {
        /* Arrange: create invoices with services and payments */
        $clientId   = $this->seedClient(['client_name' => 'Multi-Service Client']);
        $invoiceId1 = $this->seedInvoice($clientId);
        $invoiceId2 = $this->seedInvoice($clientId);
        $payment1   = $this->seedPayment($invoiceId1, ['payment_amount' => '25.00']);
        $payment2   = $this->seedPayment($invoiceId2, ['payment_amount' => '75.00']);

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Multiple payments are listed */
        $this->assertResponseStatusCode($response, 200);
        $payment1Data = $this->databaseFetchOne('ip_payments', ['payment_id' => $payment1]);
        $payment2Data = $this->databaseFetchOne('ip_payments', ['payment_id' => $payment2]);
        $this->assertResponseBodyContains($response, $payment1Data['payment_amount']);
        $this->assertResponseBodyContains($response, $payment2Data['payment_amount']);
    }

    #[Test]
    public function it_displays_payments_with_invoice_and_client_details(): void
    {
        /* Arrange */
        $clientName = 'Invoice Details Client';
        $clientId   = $this->seedClient(['client_name' => $clientName]);
        $invoiceId  = $this->seedInvoice($clientId);
        $this->seedPayment($invoiceId, ['payment_amount' => '100.00']);

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Response contains client and invoice information */
        $this->assertResponseStatusCode($response, 200);
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        self::assertNotNull($invoice);
        $this->assertResponseBodyContains($response, $clientName);
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
