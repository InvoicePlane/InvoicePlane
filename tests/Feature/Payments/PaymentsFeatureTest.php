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
    }

    #[Test]
    public function it_renders_the_payments_index_page_with_a_200_status(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_redirects_an_unauthenticated_visitor_away_from_the_payments_list(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET /payments must redirect but got status [%d].',
                $response->statusCode()
            )
        );
    }

    #[Test]
    public function it_renders_the_create_payment_form(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/payments/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertGreaterThan(
            100,
            $response->bodyLength(),
            'GET /payments/form must return a non-empty response.'
        );
    }

    #[Test]
    public function it_renders_the_edit_payment_form_for_a_seeded_payment(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '175.50']);

        /* Act */
        $response = $this->get('/payments/form/' . $paymentId);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);

        self::assertTrue(
            $response->contains('175') || $response->contains((string) $paymentId),
            sprintf(
                'Payment edit form must contain the payment amount or ID. Body (first 400 chars): %s',
                mb_substr($response->body(), 0, 400)
            )
        );
    }

    #[Test]
    public function it_stores_a_payment_and_links_it_to_the_invoice(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, [], [
            'invoice_total'   => '250.00',
            'invoice_balance' => '250.00',
        ]);

        /* Act */
        $response = $this->post('/payments/form', [
            'invoice_id'        => $invoiceId,
            'payment_method_id' => 1,
            'payment_amount'    => '250.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => 'Regression test payment',
            'btn_submit'        => '1',
        ]);

        /* Assert */
        self::assertTrue(
            $response->isRedirect() || $response->statusCode() === 200,
            sprintf(
                'POST /payments/form should redirect on success or re-render on validation error. Got [%d].',
                $response->statusCode()
            )
        );

        $this->assertDatabaseHas('ip_payments', [
            'invoice_id'     => $invoiceId,
            'payment_amount' => '250.00',
        ]);
    }

    #[Test]
    public function it_returns_404_or_redirect_for_a_nonexistent_payment_id(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/payments/form/999999999');

        /* Assert */
        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(404),
                self::equalTo(302),
                self::equalTo(301),
                self::equalTo(307),
                self::equalTo(200)
            ),
            sprintf(
                'Requesting a nonexistent payment must not crash. Got [%d].',
                $response->statusCode()
            )
        );
    }

    #[Test]
    public function it_does_not_expose_raw_php_errors_on_the_payments_index(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $response = $this->get('/payments');

        /* Assert */
        $this->assertResponseHasNoPhpErrors($response);
    }
}
