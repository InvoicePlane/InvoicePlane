<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-w6fv-2f29-rc6g: a PENDING PayPal capture books nothing, but its merchant-response row must carry
 * the capture id. PayPal reports the later settlement or denial under that id, so without it the pending
 * row cannot be matched to the outcome.
 */
#[Group('security')]
#[Group('payments')]
final class PaypalPendingCaptureReferenceTest extends AbstractTestCase
{
    #[Test]
    public function it_records_the_capture_id_of_a_pending_capture_in_the_merchant_response_reference(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal($invoiceId, 'CAP-PENDING-7731', 'PENDING');

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERREF');

        /* Assert: no money booked, but the row identifies both the order resource and the capture */
        $this->assertDatabaseCount('ip_payments', 0);
        $row = $this->databaseFetchOne('ip_merchant_responses', ['invoice_id' => $invoiceId]);
        self::assertSame('PENDING - awaiting settlement', $row['merchant_response']);
        self::assertSame(1, (int) $row['merchant_response_successful'], 'The audit row records that PayPal acknowledged the capture; it is not a failure.');
        self::assertSame('Resource ID:PAYPAL-ORDERRESOURCE, Capture ID:CAP-PENDING-7731', $row['merchant_response_reference']);
    }

    #[Test]
    public function it_keeps_the_order_resource_reference_when_the_pending_capture_has_no_id(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal($invoiceId, null, 'PENDING');

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERNOID');

        /* Assert: no dangling ", Capture ID:" suffix */
        $row = $this->databaseFetchOne('ip_merchant_responses', ['invoice_id' => $invoiceId]);
        self::assertSame('Resource ID:PAYPAL-ORDERRESOURCE', $row['merchant_response_reference']);
    }

    private function seedPayableInvoice(): int
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_payment_method', 'setting_value' => '1']);

        return $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '50.00']);
    }

    private function mockPaypal(int $invoiceId, ?string $captureId, string $status): void
    {
        $capture = ['status' => $status, 'invoice_id' => $invoiceId, 'amount' => ['value' => '50.00', 'currency_code' => 'EUR']];
        if ($captureId !== null) {
            $capture['id'] = $captureId;
        }

        $this->withEnvironment(['PAYPAL_MOCK_RESPONSES' => json_encode([
            ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])],
            ['status' => 200, 'body' => json_encode([
                'purchase_units' => [['invoice_id' => $invoiceId, 'amount' => ['value' => '50.00', 'currency_code' => 'EUR']]],
                'id'             => 'PAYPAL-ORDERRESOURCE',
            ])],
            ['status' => 200, 'body' => json_encode([
                'purchase_units' => [['payments' => ['captures' => [$capture]]]],
                'id'             => 'PAYPAL-ORDERRESOURCE',
            ])],
        ])]);
    }
}
