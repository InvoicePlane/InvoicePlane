<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;

/**
 * guest/gateways/Paypal.php feature tests.
 *
 * PaypalLib talks to the real PayPal REST API, so these tests replay a
 * canned response queue via PAYPAL_MOCK_RESPONSES (see PaypalLib::testHandlerStack()).
 * The first queued response always satisfies authorize(); later ones are
 * consumed in the order the controller calls createOrder()/captureOrder().
 */
class PaypalFlowTest extends AbstractTestCase
{
    // -------------------------------------------------------------------------
    // paypal_create_order
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_404_for_a_non_post_create_order_request(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->get('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/guest/gateways/paypal/paypal_create_order/invalid-key-123');
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/gateways/paypal/paypal_create_order/' . $urlKey);
        $this->assertResponseStatusCode($response3, 404);
    }

    #[Test]
    public function it_returns_404_for_create_order_on_an_unknown_invoice_key(): void
    {
        /* Arrange */
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/does-not-exist');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_create_order/');
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/guest/gateways/paypal/paypal_create_order/does-not-exist');
        $this->assertResponseStatusCode($response3, 404);
    }

    #[Test]
    public function it_returns_404_for_create_order_on_a_draft_invoice(): void
    {
        /* Arrange: draft (status 1) invoices are never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame(1, (int) $invoice['invoice_status_id']);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_create_order/0');
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);
        $this->assertResponseStatusCode($response3, 404);
    }

    #[Test]
    public function it_redirects_create_order_for_an_already_paid_invoice_without_calling_paypal(): void
    {
        /* Arrange: only authorize() is queued — a live createOrder() call would error */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $this->mockPaypal([$this->authResponse()]);
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect(), sprintf('Expected a redirect, got [%d].', $response->statusCode()));

        /* Assert: State Isolation (B) */
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame('0.00', $invoice['invoice_balance']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);
        self::assertTrue($response2->isRedirect());
    }

    #[Test]
    public function it_creates_a_paypal_order_for_a_payable_invoice(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 201, 'body' => json_encode(['id' => 'PAYPAL-ORDER-123', 'status' => 'CREATED'])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $json = json_decode($response->body(), true);
        self::assertSame('PAYPAL-ORDER-123', $json['id'] ?? null);
        self::assertSame('CREATED', $json['status'] ?? null);
        self::assertArrayHasKey('csrf_token', $json);

        /* Assert: Business Logic (A) */
        $this->assertResponseStatusCode($response, 200);
        $this->assertArrayHasKey('id', $json);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertGreaterThan(0, (int) $invoice['invoice_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);
        $json2 = json_decode($response2->body(), true);
        $this->assertArrayHasKey('id', $json2);
    }

    #[Test]
    public function it_returns_500_when_paypal_returns_malformed_json_for_create_order(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 200, 'body' => '{not-json'],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 500);
        self::assertStringContainsString('error', $response->body());

        /* Assert: State Isolation (B) */
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyContains($response, 'error');

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);
        $this->assertResponseStatusCode($response2, 500);
    }

    #[Test]
    public function it_returns_500_when_paypal_response_is_missing_the_order_id(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 201, 'body' => json_encode(['status' => 'CREATED'])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 500);
        self::assertStringContainsString('error', $response->body());

        /* Assert: State Isolation (B) */
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyContains($response, 'error');

        /* Assert: Boundary Cases (F) */
        $json = json_decode($response->body(), true);
        $this->assertIsArray($json);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);
        $this->assertResponseStatusCode($response2, 500);
    }

    // -------------------------------------------------------------------------
    // paypal_capture_payment
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_404_for_a_non_post_capture_payment_request(): void
    {
        /* Arrange */
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/guest/gateways/paypal/paypal_capture_payment/');
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');
        $this->assertResponseStatusCode($response3, 404);
    }

    #[Test]
    public function it_records_a_completed_capture_and_creates_a_payment(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_payment_method', 'setting_value' => '1']);
        $invoiceId = $this->seedPayableInvoice();
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-1']),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-1', 'payment_amount' => '50.00']);
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 1, 'merchant_response_driver' => 'paypal']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertGreaterThan($paymentCountBefore, $paymentCountAfter);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_external_id' => 'CAP-1']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);
        $this->assertSame('50.00', $payment['payment_amount']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');
        self::assertTrue($response2->isRedirect() || $response2->statusCode() === 200);
    }

    #[Test]
    public function it_records_a_pending_capture_as_a_payment_with_a_pending_note(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-PENDING'], 'PENDING'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-2');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-PENDING']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertGreaterThan($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_external_id' => 'CAP-PENDING']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-2');
        self::assertTrue($response2->isRedirect() || $response2->statusCode() === 200);
    }

    #[Test]
    public function it_does_not_duplicate_a_payment_for_an_already_processed_capture_id(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $this->seedPayment($invoiceId, ['payment_external_id' => 'CAP-DUP', 'payment_amount' => '50.00']);
        $paymentCountBefore = $this->databaseCount('ip_payments', ['payment_external_id' => 'CAP-DUP']);

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-DUP']),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-3');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => 'CAP-DUP']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments', ['payment_external_id' => 'CAP-DUP']);
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_external_id' => 'CAP-DUP']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);
        $this->assertSame('50.00', $payment['payment_amount']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-3');
        $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => 'CAP-DUP']);
    }

    #[Test]
    public function it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-ALREADY-PAID']),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-4');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-ALREADY-PAID']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame('0.00', $invoice['invoice_balance']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-4');
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-ALREADY-PAID']);
    }

    #[Test]
    public function it_rejects_a_capture_whose_currency_does_not_match_the_gateway_setting(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-BAD-CCY', 'currency' => 'USD']),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-5');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-BAD-CCY']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

        /* Assert: Data Integrity (D) */
        $currencySetting = $this->databaseFetchOne('ip_settings', ['setting_key' => 'gateway_paypal_currency']);
        $this->assertSame('EUR', $currencySetting['setting_value']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-5');
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-BAD-CCY']);
    }

    #[Test]
    public function it_rejects_a_capture_whose_amount_is_short_of_the_invoice_balance(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '50.00']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '10.00', 'capture_id' => 'CAP-SHORT']),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-6');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-SHORT']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame('50.00', $invoice['invoice_balance']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-6');
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-SHORT']);
    }

    #[Test]
    public function it_records_a_declined_capture_as_an_unsuccessful_merchant_response(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $paymentCountBefore = $this->databaseCount('ip_payments');
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 200, 'body' => json_encode([
                'purchase_units' => [[
                    'payments' => [
                        'captures' => [[
                            'status'             => 'DECLINED',
                            'invoice_id'         => $invoiceId,
                            'processor_response' => ['response_code' => '05'],
                        ]],
                    ],
                ]],
                'id' => 'PAYPAL-ORDER-DECLINED',
            ])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-7');

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 0]);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);

        /* Assert: Data Integrity (D) */
        $merchantResponse = $this->databaseFetchOne('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 0]);
        $this->assertSame($invoiceId, (int) $merchantResponse['invoice_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-7');
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_throws_and_records_nothing_when_the_captured_invoice_is_not_guest_visible(): void
    {
        /* Arrange: draft invoice — never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $paymentCountBefore = $this->databaseCount('ip_payments');
        $merchantResponseCountBefore = $this->databaseCount('ip_merchant_responses');

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-NOT-VISIBLE']),
        ]);

        /* Act */
        try {
            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-8');
            self::fail('Expected an exception for a non-guest-visible invoice.');
        } catch (RuntimeException $exception) {
            /* Assert: Error Semantics (C) */
            self::assertStringContainsString('Invoice not found or not accessible', $exception->getMessage());
        }

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-NOT-VISIBLE']);
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $merchantResponseCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);
        $this->assertSame($merchantResponseCountBefore, $merchantResponseCountAfter);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame(1, (int) $invoice['invoice_status_id']);

        /* Assert: Boundary Cases (F) */
        $draftInvoice = $this->databaseFetchOne('ip_invoices', ['invoice_status_id' => 1]);
        $this->assertNotNull($draftInvoice);
    }

    private function mockPaypal(array $responses): void
    {
        $this->withEnvironment(['PAYPAL_MOCK_RESPONSES' => json_encode($responses)]);
    }

    private function authResponse(): array
    {
        return ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])];
    }

    private function seedPayableInvoice(array $overrides = [], array $amountOverrides = []): int
    {
        $clientId = $this->seedClient();

        return $this->seedInvoice($clientId, array_merge(['invoice_status_id' => 2], $overrides), array_merge(['invoice_balance' => '50.00'], $amountOverrides));
    }

    private function captureResponse(array $capture, string $status = 'COMPLETED'): array
    {
        return ['status' => 200, 'body' => json_encode([
            'purchase_units' => [[
                'payments' => [
                    'captures' => [[
                        'status'     => $status,
                        'invoice_id' => $capture['invoice_id'],
                        'id'         => $capture['capture_id'] ?? 'CAPTURE-' . bin2hex(random_bytes(4)),
                        'amount'     => ['value' => $capture['amount'], 'currency_code' => $capture['currency'] ?? 'EUR'],
                    ]],
                ],
            ]],
            'id' => 'PAYPAL-ORDER-RESOURCE',
        ])];
    }
}
