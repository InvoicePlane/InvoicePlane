<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-w6fv-2f29-rc6g (PR #1724): only a COMPLETED PayPal capture may book a payment.
 *
 * Complements PaypalFlowTest (which pins the exact-case PENDING and DECLINED paths) with the status
 * values and edge branches a mutant of Paypal::paypal_capture_payment() could still pass:
 * case folding, every non-COMPLETED status, a PENDING capture without an invoice reference,
 * and repeated PENDING callbacks.
 */
#[Group('security')]
#[Group('payments')]
final class PaypalCaptureStatusGapTest extends AbstractTestCase
{
    /** @return array<string, array{0: string}> */
    public static function neverSettledStatuses(): array
    {
        return [
            'DECLINED'           => ['DECLINED'],
            'FAILED'             => ['FAILED'],
            'DENIED'             => ['DENIED'],
            'REFUNDED'           => ['REFUNDED'],
            'PARTIALLY_REFUNDED' => ['PARTIALLY_REFUNDED'],
            'PENDING_REVIEW'     => ['PENDING_REVIEW'],
            'COMPLETED prefix'   => ['COMPLETED_PENDING_REVIEW'],
            'empty'              => [''],
        ];
    }

    #[Test]
    #[DataProvider('neverSettledStatuses')]
    public function it_books_no_payment_for_any_status_other_than_completed(string $status): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $before    = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetailsResponse($invoiceId, '50.00'),
            $this->captureResponse($invoiceId, 'CAP-' . $status, $status),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERSTATUS');

        /* Assert */
        self::assertTrue($response->isRedirect() || $response->statusCode() === 200);
        $this->assertDatabaseCount('ip_payments', 0);
        $after = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertSame($before['invoice_paid'], $after['invoice_paid']);
        self::assertSame($before['invoice_balance'], $after['invoice_balance']);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 0]);
        self::assertNull($response->sessionValue('alert_success'));
    }

    #[Test]
    public function it_treats_a_lowercase_pending_status_as_pending_not_as_failed_or_settled(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetailsResponse($invoiceId, '50.00'),
            $this->captureResponse($invoiceId, 'CAP-LOWER-PENDING', 'pending'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERLOWER');

        /* Assert */
        $this->assertDatabaseCount('ip_payments', 0);
        $this->assertDatabaseHas('ip_merchant_responses', [
            'invoice_id'               => $invoiceId,
            'merchant_response'        => 'PENDING - awaiting settlement',
            'merchant_response_driver' => 'paypal',
        ]);
        self::assertSame('Payment Pending! Check PayPal for details.', $response->sessionValue('alert_info'));
        self::assertNull($response->sessionValue('alert_error'), 'A pending capture is not a failed payment.');
    }

    #[Test]
    public function it_still_books_a_completed_capture_reported_in_mixed_case(): void
    {
        /* Arrange: positive control for the case-folding tests above */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetailsResponse($invoiceId, '50.00'),
            $this->captureResponse($invoiceId, 'CAP-MIXED', 'Completed'),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERMIXED');

        /* Assert */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-MIXED', 'payment_amount' => '50.00']);
    }

    #[Test]
    public function it_acknowledges_a_pending_capture_without_an_invoice_reference_but_writes_nothing(): void
    {
        /* Arrange: the capture itself carries no invoice_id (the preflight order lookup still matches) */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetailsResponse($invoiceId, '50.00'),
            ['status' => 200, 'body' => json_encode([
                'purchase_units' => [['payments' => ['captures' => [['status' => 'PENDING', 'id' => 'CAP-NOREF']]]]],
                'id'             => 'PAYPAL-ORDERRESOURCE',
            ])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERNOREF');

        /* Assert */
        $this->assertDatabaseCount('ip_payments', 0);
        $this->assertDatabaseCount('ip_merchant_responses', 0);
        self::assertSame('Payment Pending! Check PayPal for details.', $response->sessionValue('alert_info'));
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
    }

    #[Test]
    public function it_never_books_money_however_often_the_pending_callback_repeats(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();

        /* Act */
        foreach (['ORDERR1', 'ORDERR2', 'ORDERR3'] as $orderId) {
            $this->mockPaypal([
                $this->authResponse(),
                $this->orderDetailsResponse($invoiceId, '50.00'),
                $this->captureResponse($invoiceId, 'CAP-REPEAT', 'PENDING'),
            ]);
            $this->post('/guest/gateways/paypal/paypal_capture_payment/' . $orderId);
        }

        /* Assert */
        $this->assertDatabaseCount('ip_payments', 0);
        $this->assertDatabaseHas('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_paid' => '0.00', 'invoice_balance' => '50.00']);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
    }

    #[Test]
    public function it_books_exactly_one_payment_when_a_pending_capture_later_completes_after_repeated_pending_callbacks(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        foreach (['ORDERS1', 'ORDERS2'] as $orderId) {
            $this->mockPaypal([
                $this->authResponse(),
                $this->orderDetailsResponse($invoiceId, '50.00'),
                $this->captureResponse($invoiceId, 'CAP-SETTLE', 'PENDING'),
            ]);
            $this->post('/guest/gateways/paypal/paypal_capture_payment/' . $orderId);
        }
        $this->assertDatabaseCount('ip_payments', 0);

        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetailsResponse($invoiceId, '50.00'),
            $this->captureResponse($invoiceId, 'CAP-SETTLE', 'COMPLETED'),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERS3');

        /* Assert */
        $this->assertDatabaseCount('ip_payments', 1);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-SETTLE']);
        $this->assertDatabaseHas('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_paid' => '50.00']);
    }

    private function seedPayableInvoice(): int
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_payment_method', 'setting_value' => '1']);
        $clientId = $this->seedClient();

        return $this->seedInvoice($clientId, ['invoice_status_id' => 2], ['invoice_balance' => '50.00']);
    }

    private function mockPaypal(array $responses): void
    {
        $this->withEnvironment(['PAYPAL_MOCK_RESPONSES' => json_encode($responses)]);
    }

    private function authResponse(): array
    {
        return ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])];
    }

    private function captureResponse(int $invoiceId, string $captureId, string $status): array
    {
        return ['status' => 200, 'body' => json_encode([
            'purchase_units' => [[
                'payments' => ['captures' => [[
                    'status'     => $status,
                    'invoice_id' => $invoiceId,
                    'id'         => $captureId,
                    'amount'     => ['value' => '50.00', 'currency_code' => 'EUR'],
                ]]],
            ]],
            'id' => 'PAYPAL-ORDERRESOURCE',
        ])];
    }

    private function orderDetailsResponse(int $invoiceId, string $amount): array
    {
        return ['status' => 200, 'body' => json_encode([
            'purchase_units' => [[
                'invoice_id' => $invoiceId,
                'amount'     => ['value' => $amount, 'currency_code' => 'EUR'],
            ]],
            'id' => 'PAYPAL-ORDERRESOURCE',
        ])];
    }
}
