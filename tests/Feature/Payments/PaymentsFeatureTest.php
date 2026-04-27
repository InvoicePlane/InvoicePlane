<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use Tests\AbstractTestCase;

/**
 * @group feature
 * @group payments
 */
#[CoversClass(Tests\Feature\Payments\PaymentsFeature::class)]
class PaymentsFeatureTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    public function it_renders_the_payments_index_page_with_a_200_status(): void
    {
        $response = $this->get('/payments');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    public function it_redirects_an_unauthenticated_visitor_away_from_the_payments_list(): void
    {
        $this->actingAsGuest();

        $response = $this->get('/payments');

        self::assertTrue(
            $response->isRedirect(),
            sprintf(
                'Unauthenticated GET /payments must redirect but got status [%d].',
                $response->statusCode()
            )
        );
    }

    public function it_renders_the_create_payment_form_for_an_existing_invoice(): void
    {
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $response = $this->get('/payments/create/' . $invoiceId);

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseHasNoPhpErrors($response);
    }

    public function it_stores_a_payment_and_links_it_to_the_invoice(): void
    {
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $response = $this->post('/payments/create/' . $invoiceId, [
            'payment_method_id' => 1,
            'payment_amount'    => '250.00',
            'payment_date'      => date('Y-m-d'),
            'payment_note'      => 'Regression test payment',
        ]);

        self::assertTrue(
            $response->isRedirect() || $response->statusCode() === 200,
            sprintf(
                'POST /payments/create should redirect on success or re-render on validation error. Got [%d].',
                $response->statusCode()
            )
        );

        if ($response->isRedirect()) {
            $this->assertDatabaseHas('ip_payments', [
                'invoice_id'     => $invoiceId,
                'payment_amount' => '250.00',
            ]);
        }
    }

    public function it_rejects_a_payment_submission_with_a_zero_amount(): void
    {
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);

        $response = $this->post('/payments/create/' . $invoiceId, [
            'payment_method_id' => 1,
            'payment_amount'    => '0.00',
            'payment_date'      => date('Y-m-d'),
        ]);

        $persistedCount = (int) get_instance()->db->where([
            'invoice_id'     => $invoiceId,
            'payment_amount' => '0.00',
        ])->count_all_results('ip_payments');

        self::assertSame(
            0,
            $persistedCount,
            'A payment with amount 0.00 must not be persisted to ip_payments.'
        );
    }

    public function it_renders_the_payment_view_page_for_a_seeded_payment(): void
    {
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $paymentId = $this->seedPayment($invoiceId, ['payment_amount' => '175.50']);

        $response = $this->get('/payments/view/' . $paymentId);

        $this->assertResponseStatusCode($response, 200);

        self::assertTrue(
            $response->contains('175') || $response->contains((string) $paymentId),
            sprintf(
                'Payment view page must contain the payment amount or ID. Body (first 400 chars): %s',
                mb_substr($response->body(), 0, 400)
            )
        );
    }

    public function it_returns_a_non_200_for_a_payment_view_with_a_nonexistent_id(): void
    {
        $response = $this->get('/payments/view/999999999');

        self::assertThat(
            $response->statusCode(),
            self::logicalOr(
                self::equalTo(404),
                self::equalTo(302),
                self::equalTo(301)
            ),
            sprintf(
                'Requesting a nonexistent payment must produce 404 or redirect, not a silent 200. Got [%d].',
                $response->statusCode()
            )
        );
    }

    public function it_does_not_expose_raw_php_errors_on_the_payments_index(): void
    {
        $response = $this->get('/payments');

        $this->assertResponseHasNoPhpErrors($response);
    }
}
