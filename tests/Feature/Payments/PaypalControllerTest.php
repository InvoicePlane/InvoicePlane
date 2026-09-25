<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Paypal Feature Tests.
 *
 * Tests payment listing and management routes accessible to authenticated admins.
 */
class PaypalControllerTest extends AbstractTestCase
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
        $clientId  = $this->seedClient(['client_name' => 'Paypal Test Client']);
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_total' => '100.00']);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '50.00']);

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Response is successful */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<html');

        /* Assert: Payment data is present in response */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_id' => $paymentId]);
        self::assertNotNull($payment);
        // Rendered currency-formatted (e.g. '$50'), not the raw stored '50.00'.
        $this->assertResponseBodyContains($response, '$50');
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
        $this->seedPayment($invoiceId1, ['payment_amount' => '25.00']);
        $this->seedPayment($invoiceId2, ['payment_amount' => '75.00']);

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Multiple payments are listed */
        $this->assertResponseStatusCode($response, 200);
        // The list renders currency-formatted (e.g. '$25'), not the raw stored '25.00'.
        $this->assertResponseBodyContains($response, '$25');
        $this->assertResponseBodyContains($response, '$75');
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
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $this->seedPayment($invoiceId);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Error Semantics (C) */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/payments] must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertResponseStatusCode($response, 307);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);
        $this->assertResponseBodyNotContains($response, 'payment');

        /* Assert: Business Logic (A) */
        // Raw header() calls aren't exposed via headers_list() under PHP's CLI SAPI
        // (documented on assertResponseRedirectsToRoute()), so use that helper — it
        // already guards for an empty Location and still validates the route when
        // the execution environment does expose it.
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $invoiceId]);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);

        /* Assert: Boundary Cases (F) */
        // 'view' isn't a real method on this controller — CI3 404s it before ever
        // instantiating the controller (and so before the auth guard runs).
        $response2 = $this->get('/payments/view/' . $invoiceId);
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/payments');
        self::assertTrue($response3->isRedirect());
        $this->assertResponseStatusCode($response3, 307);
    }
}
