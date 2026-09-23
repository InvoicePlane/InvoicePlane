<?php

namespace Tests\Feature\Payments;

use Ajax;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Ajax::class)]

class PaymentsAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_adds_a_payment_with_all_required_fields(): void
    {
        /* Arrange */
        $clientId           = $this->seedClient();
        $invoiceId          = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $this->validPayload($invoiceId));

        /* Assert: Business Logic (A) */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '25.00']);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertGreaterThan($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '25.00']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->ajax('POST', '/payments/ajax/add', $this->validPayload($invoiceId));
        $json2     = json_decode($response2->body(), true);
        $this->assertSame(1, $json2['success'] ?? null, 'Body: ' . $response2->body());
    }

    #[Test]
    public function it_fails_to_add_a_payment_without_invoice_id(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);
        $payload   = $this->validPayload($invoiceId);
        unset($payload['invoice_id']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert: Business Logic (A) */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: Boundary Cases (F) */
        $payload2 = $this->validPayload($invoiceId);
        unset($payload2['invoice_id']);
        $response2 = $this->ajax('POST', '/payments/ajax/add', $payload2);
        $json2     = json_decode($response2->body(), true);
        $this->assertSame(0, $json2['success'] ?? null);

        /* Assert: Idempotency (E) */
        $response3 = $this->ajax('POST', '/payments/ajax/add', $payload);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    #[Test]
    public function it_fails_to_add_a_payment_without_payment_date(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);
        $payload   = $this->validPayload($invoiceId);
        unset($payload['payment_date']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert: Business Logic (A) */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame($invoiceId, (int) $invoice['invoice_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->ajax('POST', '/payments/ajax/add', $payload);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    #[Test]
    public function it_fails_to_add_a_payment_without_payment_amount(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);
        $payload   = $this->validPayload($invoiceId);
        unset($payload['payment_amount']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert: Business Logic (A) */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: Data Integrity (D) — invoice_balance lives on ip_invoice_amounts, not ip_invoices */
        $amounts = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        $this->assertSame('100.00', $amounts['invoice_balance']);

        /* Assert: Idempotency (E) */
        $response2 = $this->ajax('POST', '/payments/ajax/add', $payload);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    #[Test]
    public function it_fails_to_add_a_payment_exceeding_the_invoice_balance(): void
    {
        /* Arrange */
        $clientId                  = $this->seedClient();
        $invoiceId                 = $this->seedInvoice($clientId, [], ['invoice_balance' => '10.00']);
        $payload                   = $this->validPayload($invoiceId);
        $payload['payment_amount'] = '999.00';
        $paymentCountBefore        = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert: Business Logic (A) */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: Data Integrity (D) — invoice_balance lives on ip_invoice_amounts, not ip_invoices */
        $amounts = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        $this->assertSame('10.00', $amounts['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $payload2                   = $this->validPayload($invoiceId);
        $payload2['payment_amount'] = '0.00';
        $response2                  = $this->ajax('POST', '/payments/ajax/add', $payload2);
        $json2                      = json_decode($response2->body(), true);
        $this->assertSame(0, $json2['success'] ?? null);

        /* Assert: Idempotency (E) */
        $response3 = $this->ajax('POST', '/payments/ajax/add', $payload);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    #[Test]
    public function it_renders_the_add_payment_modal(): void
    {
        /* Arrange */
        $clientId           = $this->seedClient();
        $invoiceId          = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/modal_add_payment', [
            'invoice_id'      => (string) $invoiceId,
            'invoice_balance' => '100.00',
        ]);

        /* Assert: Error Semantics (C) */
        $this->assertResponseHasNoPhpErrors($response);
        $this->assertResponseStatusCode($response, 200);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyContains($response, 'payment');

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame($invoiceId, (int) $invoice['invoice_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->ajax('POST', '/payments/ajax/modal_add_payment', [
            'invoice_id'      => (string) $invoiceId,
            'invoice_balance' => '100.00',
        ]);
        $this->assertResponseHasNoPhpErrors($response2);
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Arrange */
        $clientId           = $this->seedClient();
        $invoiceId          = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->post('/payments/ajax/add', $this->validPayload($invoiceId));

        /* Assert: Business Logic (A) */
        self::assertSame('', $response->body());
        $this->assertDatabaseCount('ip_payments', 0);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertGreaterThan(0, (int) $invoice['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/payments/ajax/modal_add_payment', ['invoice_id' => (string) $invoiceId]);
        self::assertSame('', $response2->body());

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/payments/ajax/add', $this->validPayload($invoiceId));
        self::assertSame('', $response3->body());
        $this->assertDatabaseCount('ip_payments', 0);
    }

    private function validPayload(int $invoiceId): array
    {
        return [
            'invoice_id'     => (string) $invoiceId,
            'payment_date'   => date('Y-m-d'),
            'payment_amount' => '25.00',
        ];
    }
}
