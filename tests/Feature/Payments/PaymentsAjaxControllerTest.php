<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

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
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $this->validPayload($invoiceId));

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '25.00']);
    }

    #[Test]
    public function it_fails_to_add_a_payment_without_invoice_id(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);
        $payload   = $this->validPayload($invoiceId);
        unset($payload['invoice_id']);

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
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

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
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

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
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

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/add', $payload);

        /* Assert */
        $json = json_decode($response->body(), true);
        self::assertSame(0, $json['success'] ?? null);
        $this->assertDatabaseCount('ip_payments', 0);
    }

    #[Test]
    public function it_renders_the_add_payment_modal(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);

        /* Act */
        $response = $this->ajax('POST', '/payments/ajax/modal_add_payment', [
            'invoice_id'      => (string) $invoiceId,
            'invoice_balance' => '100.00',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_requires_an_ajax_request(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], ['invoice_balance' => '100.00']);

        /* Act */
        $response = $this->post('/payments/ajax/add', $this->validPayload($invoiceId));

        /* Assert */
        self::assertSame('', $response->body());
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
