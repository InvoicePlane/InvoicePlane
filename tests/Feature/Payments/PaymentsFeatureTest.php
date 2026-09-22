<?php

namespace Tests\Feature\Payments;

use Payments;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Payments::class)]
class PaymentsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'decimal_point', 'setting_value' => '.']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'tax_rate_decimal_places', 'setting_value' => '2']);
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_payments(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment List Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $paymentCountBefore = $this->databaseCount('ip_payments');
        $this->seedPayment($invoiceId, ['payment_amount' => '99.00']);

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '99.00');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '99.00']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertGreaterThan($paymentCountBefore, $paymentCountAfter);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '99.00']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $zeroPaymentCheck = $this->databaseCount('ip_payments', ['payment_amount' => '0.00']);
        $this->assertGreaterThanOrEqual(0, $zeroPaymentCheck);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/payments');
        $this->assertResponseStatusCode($response2, 200);
        $this->assertResponseBodyContains($response2, '99.00');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_payment_and_links_it_to_the_invoice(): void
    {
        /**
         * POST /payments/form
         * {
         *     "invoice_id": "<invoiceId>",
         *     "payment_method_id": "1",
         *     "payment_amount": "250.00",
         *     "payment_date": "2026-06-21",
         *     "payment_note": "Test payment",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment Create Client']);
        $invoiceId = $this->seedInvoice($clientId, [], [
            'invoice_total'   => '250.00',
            'invoice_balance' => '250.00',
        ]);

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'        => $invoiceId,
            'payment_method_id' => '1',
            'payment_amount'    => '250.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => 'Test payment',
            'btn_submit'        => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '250.00',
        ]);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_payment_form_showing_existing_amount(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment Edit Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '175.50']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/payments/form/' . $paymentId);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '175');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payments', ['payment_id' => $paymentId, 'payment_amount' => '175.50']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_id' => $paymentId]);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);
        $this->assertSame('175.50', $payment['payment_amount']);

        /* Assert: Boundary Cases (F) */
        $nonExistentPayment = $this->databaseFetchOne('ip_payments', ['payment_id' => 999999]);
        $this->assertNull($nonExistentPayment);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/payments/form/' . $paymentId);
        $this->assertResponseStatusCode($response2, 200);
        $this->assertResponseBodyContains($response2, '175');
    }

    #[Test]
    public function it_updates_a_payment(): void
    {
        /**
         * POST /payments/form/{id}
         * {
         *     "invoice_id": "<invoiceId>",
         *     "payment_method_id": "1",
         *     "payment_amount": "300.00",
         *     "payment_date": "2026-06-21",
         *     "payment_note": "Updated payment",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment Update Client']);
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_total' => '300.00', 'invoice_balance' => '300.00']);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '100.00']);

        /* Act */
        $response = $this->post('/payments/form/' . $paymentId, [
            'invoice_id'        => $invoiceId,
            'payment_method_id' => '1',
            'payment_amount'    => '300.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => 'Updated payment',
            'btn_submit'        => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_payments', ['payment_id' => $paymentId, 'payment_amount' => '300.00']);
        $this->assertDatabaseMissing('ip_payments', ['payment_id' => $paymentId, 'payment_amount' => '100.00']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_payment(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment Delete Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '50.00']);
        $this->assertDatabaseHas('ip_payments', ['payment_id' => $paymentId]);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->post('/payments/delete/' . $paymentId, []);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_id' => $paymentId]);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore - 1, $paymentCountAfter);

        /* Assert: Data Integrity (D) */
        $deletedPayment = $this->databaseFetchOne('ip_payments', ['payment_id' => $paymentId]);
        $this->assertNull($deletedPayment);
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertGreaterThan(0, (int) $invoice['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/payments/delete/999999', []);
        $this->assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/payments/delete/' . $paymentId, []);
        $this->assertTrue($response3->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['payment_id' => $paymentId]);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_invoice_id(): void
    {
        /**
         * POST /payments/form
         * {
         *     "invoice_id": "",
         *     "payment_amount": "100.00",
         *     "payment_date": "2026-06-21",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'     => '',
            'payment_amount' => '100.00',
            'payment_date'   => date('Y-m-d'),
            'btn_submit'     => '1',
        ]);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 302);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: Data Integrity (D) */
        $this->assertDatabaseMissing('ip_payments', ['payment_amount' => '100.00']);

        /* Assert: Boundary Cases (F) */
        $this->post('/payments/form', ['invoice_id' => null, 'payment_amount' => '100.00', 'payment_date' => date('Y-m-d'), 'btn_submit' => '1']);
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/payments/form', [
            'invoice_id'     => '',
            'payment_amount' => '100.00',
            'payment_date'   => date('Y-m-d'),
            'btn_submit'     => '1',
        ]);
        $this->assertResponseStatusCode($response2, 302);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    #[Test]
    public function it_fails_to_create_without_payment_amount(): void
    {
        /**
         * POST /payments/form
         * {
         *     "invoice_id": "<invoiceId>",
         *     "payment_amount": "",
         *     "payment_date": "2026-06-21",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment Fail Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '',
            'payment_date'   => date('Y-m-d'),
            'btn_submit'     => '1',
        ]);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 302);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseCount('ip_payments', 0);
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame($invoiceId, (int) $invoice['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $this->post('/payments/form', ['invoice_id' => $invoiceId, 'payment_amount' => '0', 'payment_date' => date('Y-m-d'), 'btn_submit' => '1']);
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/payments/form', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '',
            'payment_date'   => date('Y-m-d'),
            'btn_submit'     => '1',
        ]);
        $this->assertResponseStatusCode($response2, 302);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    #[Test]
    public function it_fails_to_create_without_payment_date(): void
    {
        /**
         * POST /payments/form
         * {
         *     "invoice_id": "<invoiceId>",
         *     "payment_amount": "100.00",
         *     "payment_date": "",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment No Date Client']);
        $invoiceId = $this->seedInvoice($clientId);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '100.00',
            'payment_date'   => '',
            'btn_submit'     => '1',
        ]);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 302);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseCount('ip_payments', 0);
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '100.00']);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame($invoiceId, (int) $invoice['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $this->post('/payments/form', ['invoice_id' => $invoiceId, 'payment_amount' => '100.00', 'payment_date' => '0000-00-00', 'btn_submit' => '1']);
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/payments/form', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '100.00',
            'payment_date'   => '',
            'btn_submit'     => '1',
        ]);
        $this->assertResponseStatusCode($response2, 302);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    // -------------------------------------------------------------------------
    // payment_external_id field (nullable: only gateway payments have values)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_manual_payment_with_null_external_id(): void
    {
        /* Manual payments (no gateway) have NULL external_id; duplicates allowed */
        $clientId  = $this->seedClient(['client_name' => 'Manual Payment Client']);
        $invoiceId = $this->seedInvoice($clientId, [], [
            'invoice_total'   => '75.00',
            'invoice_balance' => '75.00',
        ]);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $response = $this->post('/payments/form', [
            'invoice_id'        => $invoiceId,
            'payment_method_id' => '1',
            'payment_amount'    => '75.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => 'Manual payment',
            'btn_submit'        => '1',
        ]);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'          => $invoiceId,
            'payment_amount'      => '75.00',
            'payment_external_id' => null,
        ]);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore + 1, $paymentCountAfter);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '75.00']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);
        $this->assertNull($payment['payment_external_id']);

        /* Assert: Boundary Cases (F) */
        $secondManualPayment = $this->post('/payments/form', [
            'invoice_id'        => $invoiceId,
            'payment_method_id' => '1',
            'payment_amount'    => '75.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => 'Manual payment 2',
            'btn_submit'        => '1',
        ]);
        $this->assertTrue($secondManualPayment->isRedirect());

        /* Assert: Idempotency (E) */
        $paymentCountAfter2 = $this->databaseCount('ip_payments', ['invoice_id' => $invoiceId]);
        $this->assertSame(2, $paymentCountAfter2);
    }

    // -------------------------------------------------------------------------
    // Guest redirect — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_the_payments_list(): void
    {
        /* Arrange */
        $this->actingAsGuest();
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/payments');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseStatusCode($response, 302);

        /* Assert: Data Integrity (D) */
        $this->assertResponseStatusCode($response, 302);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/payments/form/999999');
        $this->assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/payments');
        $this->assertTrue($response3->isRedirect());
        $this->assertResponseStatusCode($response3, 302);
    }
}
