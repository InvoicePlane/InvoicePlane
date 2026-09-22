<?php

namespace Tests\Feature\Payments;

use Payment_Information;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Payment_Information.
 *
 * Tests HTTP endpoints for the payments list.
 */
#[CoversClass(Payment_Information::class)]
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
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);

        /* Assert: Business Logic (A) + State Isolation (B) */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId]);
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $invoiceId]);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $invoiceId2 = $this->seedInvoice($clientId);
        $this->seedPayment($invoiceId2);
        $response2 = $this->get('/payments');
        $this->assertResponseStatusCode($response2, 200);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/payments');
        $this->assertResponseStatusCode($response3, 200);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId]);
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
        $this->assertResponseStatusCode($response, 302);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);
        $this->assertResponseBodyNotContains($response, 'payment');

        /* Assert: Business Logic (A) */
        $this->assertStringContainsString('/login', $response->headers()['Location'] ?? '');

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $invoiceId]);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/payments/view/' . $invoiceId);
        self::assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/payments');
        self::assertTrue($response3->isRedirect());
        $this->assertResponseStatusCode($response3, 302);
    }
}
