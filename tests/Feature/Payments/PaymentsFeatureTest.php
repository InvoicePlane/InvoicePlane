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
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '99.00']);

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_payments', ['payment_id' => $paymentId, 'payment_amount' => '99.00']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_payment_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/payments/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

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
         * }
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

        /* Act */
        $response = $this->get('/payments/form/' . $paymentId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, '175');
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
         * }
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

        /* Act */
        $response = $this->post('/payments/delete/' . $paymentId, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
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
         * }
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'     => '',
            'payment_amount' => '100.00',
            'payment_date'   => date('Y-m-d'),
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
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
         * }
         */

        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment Fail Client']);
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '',
            'payment_date'   => date('Y-m-d'),
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
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
         * }
         */

        /* Arrange */
        $clientId  = $this->seedClient(['client_name' => 'Payment No Date Client']);
        $invoiceId = $this->seedInvoice($clientId);

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '100.00',
            'payment_date'   => '',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    // -------------------------------------------------------------------------
    // Guest redirect — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_the_payments_list(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
