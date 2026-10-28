<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Funds PayPal has captured but the application did not book must be flagged in the error log, once, so the
 * operator can reconcile them by hand. Without that line the guest sees a failure and the money is simply gone
 * from every record the application keeps.
 */
#[Group('feature')]
#[Group('payments')]
final class PaypalCaptureNotRecordedLogTest extends AbstractTestCase
{
    private const MARKER = 'succeeded but was not recorded against invoice';

    protected function setUp(): void
    {
        parent::setUp();

        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_payment_method', 'setting_value' => '1']);
    }

    #[Test]
    public function it_logs_one_error_naming_the_capture_when_a_completed_capture_is_rejected_after_the_money_moved(): void
    {
        /* Arrange: the order verifies in EUR, but the capture PayPal returns is in USD, so it is refused */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetailsResponse($invoiceId, '50.00'),
            $this->captureResponse($invoiceId, 'CAP-NOT-BOOKED', '50.00', 'USD'),
        ]);
        $logFile = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        $offset  = is_file($logFile) ? filesize($logFile) : 0;

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERLOG1');

        /* Assert: nothing booked, and exactly one reconciliation line that names the capture */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-NOT-BOOKED']);
        $logged = $this->logSince($logFile, $offset);
        self::assertSame(1, substr_count($logged, self::MARKER), 'The reconciliation line must be written exactly once.');
        self::assertStringContainsString('PayPal capture CAP-NOT-BOOKED ' . self::MARKER, $logged);
    }

    #[Test]
    public function it_does_not_log_a_reconciliation_error_for_a_capture_that_was_booked(): void
    {
        /* Arrange: positive control, a matching capture is recorded */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetailsResponse($invoiceId, '50.00'),
            $this->captureResponse($invoiceId, 'CAP-BOOKED', '50.00', 'EUR'),
        ]);
        $logFile = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        $offset  = is_file($logFile) ? filesize($logFile) : 0;

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERLOG2');

        /* Assert */
        $this->assertDatabaseHas('ip_payments', ['payment_external_id' => 'CAP-BOOKED']);
        self::assertStringNotContainsString(self::MARKER, $this->logSince($logFile, $offset));
    }

    private function logSince(string $logFile, int $offset): string
    {
        clearstatcache(true, $logFile);

        return is_file($logFile) ? (string) file_get_contents($logFile, false, null, $offset) : '';
    }

    private function seedPayableInvoice(): int
    {
        return $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '50.00']);
    }

    private function mockPaypal(array $responses): void
    {
        $this->withEnvironment(['PAYPAL_MOCK_RESPONSES' => json_encode($responses)]);
    }

    private function authResponse(): array
    {
        return ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])];
    }

    private function orderDetailsResponse(int $invoiceId, string $amount): array
    {
        return ['status' => 200, 'body' => json_encode([
            'purchase_units' => [['invoice_id' => $invoiceId, 'amount' => ['value' => $amount, 'currency_code' => 'EUR']]],
            'id'             => 'PAYPAL-ORDERRESOURCE',
        ])];
    }

    private function captureResponse(int $invoiceId, string $captureId, string $amount, string $currency): array
    {
        return ['status' => 200, 'body' => json_encode([
            'purchase_units' => [['payments' => ['captures' => [[
                'status'     => 'COMPLETED',
                'invoice_id' => $invoiceId,
                'id'         => $captureId,
                'amount'     => ['value' => $amount, 'currency_code' => $currency],
            ]]]]],
            'id' => 'PAYPAL-ORDERRESOURCE',
        ])];
    }
}
