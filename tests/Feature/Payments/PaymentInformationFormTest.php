<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/controllers/Payment_information.php::form() — invoice lookup and
 * payability checks. The provider-allowlist dispatch itself is covered by
 * PaymentProviderAllowlistTest.php.
 */
class PaymentInformationFormTest extends AbstractTestCase
{
    #[Test]
    public function it_redirects_for_an_unknown_invoice_key(): void
    {
        /* Arrange */
        $invoiceCountBefore = $this->databaseCount('ip_invoices');

        /* Act */
        $response = $this->get('/guest/payment_information/form/does-not-exist');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());
        $this->assertResponseStatusCode($response, 302);

        /* Assert: State Isolation (B) */
        $invoiceCountAfter = $this->databaseCount('ip_invoices');
        $this->assertSame($invoiceCountBefore, $invoiceCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyNotContains($response, 'payment');

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/guest/payment_information/form/');
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/payment_information/form/does-not-exist');
        self::assertTrue($response3->isRedirect());
    }

    #[Test]
    public function it_redirects_for_a_draft_invoice_key(): void
    {
        /* Arrange: draft (status 1) invoices are never guest_visible() */
        $clientId = $this->seedClient();
        $urlKey   = 'draft-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 1]);
        $invoiceCountBefore = $this->databaseCount('ip_invoices');

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());
        $this->assertResponseStatusCode($response, 302);

        /* Assert: State Isolation (B) */
        $invoiceCountAfter = $this->databaseCount('ip_invoices');
        $this->assertSame($invoiceCountBefore, $invoiceCountAfter);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_url_key' => $urlKey]);
        $this->assertSame(1, (int) $invoice['invoice_status_id']);

        /* Assert: Boundary Cases (F) */
        $urlKey2 = 'draft-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey2, 'invoice_status_id' => 1]);
        $response2 = $this->get('/guest/payment_information/form/' . $urlKey2);
        self::assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/payment_information/form/' . $urlKey);
        self::assertTrue($response3->isRedirect());
    }

    #[Test]
    public function it_returns_404_for_an_already_paid_invoice_when_unauthenticated(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $clientId = $this->seedClient();
        $urlKey   = 'paid-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);
        $invoiceCountBefore = $this->databaseCount('ip_invoices');

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);
        $this->assertResponseBodyNotContains($response, 'payment');

        /* Assert: State Isolation (B) */
        $invoiceCountAfter = $this->databaseCount('ip_invoices');
        $this->assertSame($invoiceCountBefore, $invoiceCountAfter);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_url_key' => $urlKey]);
        $this->assertSame(4, (int) $invoice['invoice_status_id']);
        $this->assertSame('0.00', $invoice['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $urlKey2 = 'paid-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey2, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);
        $response2 = $this->get('/guest/payment_information/form/' . $urlKey2);
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/payment_information/form/' . $urlKey);
        $this->assertResponseStatusCode($response3, 404);
    }

    #[Test]
    public function it_renders_the_form_for_a_payable_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'payable-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, [
            'invoice_url_key'   => $urlKey,
            'invoice_status_id' => 2,
            'payment_method'    => 0,
        ], ['invoice_balance' => '100.00', 'invoice_total' => '100.00']);
        $invoiceCountBefore = $this->databaseCount('ip_invoices');

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseStatusCode($response, 200);

        /* Assert: State Isolation (B) */
        $invoiceCountAfter = $this->databaseCount('ip_invoices');
        $this->assertSame($invoiceCountBefore, $invoiceCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyContains($response, 'payment');

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_url_key' => $urlKey]);
        $this->assertSame(2, (int) $invoice['invoice_status_id']);
        $this->assertSame('100.00', $invoice['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $urlKey2 = 'payable-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, [
            'invoice_url_key'   => $urlKey2,
            'invoice_status_id' => 2,
            'payment_method'    => 0,
        ], ['invoice_balance' => '50.00', 'invoice_total' => '100.00']);
        $response2 = $this->get('/guest/payment_information/form/' . $urlKey2);
        $this->assertResponseStatusCode($response2, 200);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/payment_information/form/' . $urlKey);
        $this->assertResponseStatusCode($response3, 200);
        $this->assertResponseHasNoPhpErrors($response3);
    }

    #[Test]
    public function it_does_not_expose_php_errors_for_an_already_paid_invoice(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $urlKey   = 'paid-noerr-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);
        $invoiceCountBefore = $this->databaseCount('ip_invoices');

        /* Act */
        $response = $this->get('/guest/payment_information/form/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $invoiceCountAfter = $this->databaseCount('ip_invoices');
        $this->assertSame($invoiceCountBefore, $invoiceCountAfter);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_url_key' => $urlKey]);
        $this->assertSame(4, (int) $invoice['invoice_status_id']);
        $this->assertSame('0.00', $invoice['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $urlKey2 = 'paid-noerr-key-' . bin2hex(random_bytes(4));
        $this->seedInvoice($clientId, ['invoice_url_key' => $urlKey2, 'invoice_status_id' => 4], ['invoice_balance' => '0.00']);
        $response2 = $this->get('/guest/payment_information/form/' . $urlKey2);
        $this->assertResponseHasNoPhpErrors($response2);
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/payment_information/form/' . $urlKey);
        $this->assertResponseHasNoPhpErrors($response3);
        $this->assertResponseStatusCode($response3, 404);
    }
}
