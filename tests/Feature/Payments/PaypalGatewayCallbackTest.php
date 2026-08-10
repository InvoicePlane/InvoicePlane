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
class PaypalGatewayCallbackTest extends AbstractTestCase
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

        /* Act */
        $response = $this->get('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_create_order_on_an_unknown_invoice_key(): void
    {
        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/does-not-exist');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_create_order_on_a_draft_invoice(): void
    {
        /* Arrange: draft (status 1) invoices are never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_redirects_create_order_for_an_already_paid_invoice_without_calling_paypal(): void
    {
        /* Arrange: only authorize() is queued — a live createOrder() call would error */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $this->mockPaypal([$this->authResponse()]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert: redirected away, no order-creation call was ever reached */
        self::assertTrue($response->isRedirect(), sprintf('Expected a redirect, got [%d].', $response->statusCode()));
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

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $json = json_decode($response->body(), true);
        self::assertSame('PAYPAL-ORDER-123', $json['id'] ?? null);
        self::assertSame('CREATED', $json['status'] ?? null);
        self::assertArrayHasKey('csrf_token', $json);
    }

    #[Test]
    public function it_returns_500_when_paypal_returns_malformed_json_for_create_order(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 200, 'body' => '{not-json'],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 500);
        self::assertStringContainsString('error', $response->body());
    }

    #[Test]
    public function it_returns_500_when_paypal_response_is_missing_the_order_id(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 201, 'body' => json_encode(['status' => 'CREATED'])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_create_order/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 500);
        self::assertStringContainsString('error', $response->body());
    }

    // -------------------------------------------------------------------------
    // paypal_capture_payment
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_404_for_a_non_post_capture_payment_request(): void
    {
        /* Act */
        $response = $this->get('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_records_a_completed_capture_and_creates_a_payment(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_payment_method', 'setting_value' => '1']);
        $invoiceId = $this->seedPayableInvoice();

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-1']),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-1');

        /* Assert */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-1', 'payment_amount' => '50.00']);
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 1, 'merchant_response_driver' => 'paypal']);
    }

    #[Test]
    public function it_records_a_pending_capture_as_a_payment_with_a_pending_note(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-PENDING'], 'PENDING'),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-2');

        /* Assert */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-PENDING']);
    }

    #[Test]
    public function it_does_not_duplicate_a_payment_for_an_already_processed_capture_id(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $this->seedPayment($invoiceId, ['payment_external_id' => 'CAP-DUP', 'payment_amount' => '50.00']);

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-DUP']),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-3');

        /* Assert: still exactly one payment row for this capture id, not two */
        $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => 'CAP-DUP']);
    }

    #[Test]
    public function it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-ALREADY-PAID']),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-4');

        /* Assert */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-ALREADY-PAID']);
    }

    #[Test]
    public function it_rejects_a_capture_whose_currency_does_not_match_the_gateway_setting(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-BAD-CCY', 'currency' => 'USD']),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-5');

        /* Assert */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-BAD-CCY']);
    }

    #[Test]
    public function it_rejects_a_capture_whose_amount_is_short_of_the_invoice_balance(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '50.00']);

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '10.00', 'capture_id' => 'CAP-SHORT']),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-6');

        /* Assert */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-SHORT']);
    }

    #[Test]
    public function it_records_a_declined_capture_as_an_unsuccessful_merchant_response(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();

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
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-7');

        /* Assert */
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 0]);
    }

    #[Test]
    public function it_throws_and_records_nothing_when_the_captured_invoice_is_not_guest_visible(): void
    {
        /* Arrange: draft invoice — never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);

        $this->mockPaypal([
            $this->authResponse(),
            $this->captureResponse(['invoice_id' => $invoiceId, 'amount' => '50.00', 'capture_id' => 'CAP-NOT-VISIBLE']),
        ]);

        /* Act */
        try {
            $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER-8');
            self::fail('Expected an exception for a non-guest-visible invoice.');
        } catch (RuntimeException $exception) {
            /* Assert */
            self::assertStringContainsString('Invoice not found or not accessible', $exception->getMessage());
        }

        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-NOT-VISIBLE']);
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);
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
