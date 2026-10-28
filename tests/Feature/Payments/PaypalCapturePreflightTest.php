<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-m2c5-pmxf-qfh5: paypal_capture_payment() must verify the order against the invoice
 * BEFORE captureOrder() moves any money (Paypal::_capture_preflight_error()).
 *
 * PaypalFlowTest proves "no payment row" for a few rejections but never proves that the
 * capture call itself was not made, and several rejections are only asserted as
 * "redirect or 200". Here the transport fake's request recorder (PAYPAL_MOCK_REQUEST_CAPTURE,
 * which keeps the LAST outbound request) is the oracle: after a refused preflight the last
 * request must be the order-details GET, never a POST to .../capture. Every refusal also pins
 * the exact message so the distinct preflight branches cannot be swapped for one another.
 */
#[Group('feature')]
#[Group('security')]
#[Group('payments')]
final class PaypalCapturePreflightTest extends AbstractTestCase
{
    private const FAILED = 'Payment failed. Please try again.';

    /** @var array<int, string> */
    private array $captureFiles = [];

    private string $captureFile = '';

    private int $paymentsAtStart = 0;

    protected function setUp(): void
    {
        parent::setUp();

        $captureFile = tempnam(sys_get_temp_dir(), 'paypal-request-');
        self::assertNotFalse($captureFile);
        $this->captureFile    = $captureFile;
        $this->captureFiles[] = $captureFile;

        $this->paymentsAtStart = $this->databaseCount('ip_payments');
        $this->setSetting('gateway_paypal_currency', 'EUR');
        $this->setSetting('gateway_paypal_payment_method', '1');
    }

    protected function tearDown(): void
    {
        foreach ($this->captureFiles as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    #[Test]
    public function it_books_nothing_when_the_capture_names_a_different_invoice_than_the_verified_order(): void
    {
        /* Arrange: the order verifies against invoice A, but the capture PayPal returns names invoice B */
        $invoiceA = $this->seedPayableInvoice();
        $invoiceB = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceA, '50.00'),
            $this->captureResult($invoiceB, '50.00', 'CAP-SWAPPED'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERSWAP');

        /* Assert: neither invoice is credited and the user is told the payment failed */
        $this->assertDatabaseCount('ip_payments', $this->paymentsAtStart);
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'CAP-SWAPPED']);
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
    }

    #[Test]
    public function it_captures_a_matching_order_and_the_capture_is_the_last_paypal_request(): void
    {
        /* Arrange: positive control, also proves the recorder can see a capture */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.00'),
            $this->captureResult($invoiceId, '50.00', 'CAP-PRE-OK'),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDEROK');

        /* Assert */
        $last = $this->lastPaypalRequest();
        self::assertSame('POST', $last['method']);
        self::assertStringEndsWith('/v2/checkout/orders/ORDEROK/capture', $last['url']);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-PRE-OK']);
    }

    #[Test]
    public function it_does_not_capture_when_paypal_cannot_return_the_order_details(): void
    {
        /* Arrange: PayPal answers the pre-capture order lookup with a 404 */
        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 404, 'body' => json_encode(['name' => 'RESOURCE_NOT_FOUND'])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERGONE');

        /* Assert */
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERGONE');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_when_the_order_has_no_purchase_unit(): void
    {
        /* Arrange */
        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 200, 'body' => json_encode(['id' => 'X', 'purchase_units' => []])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDEREMPTY');

        /* Assert */
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDEREMPTY');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_when_the_order_names_an_invoice_but_no_amount(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            ['status' => 200, 'body' => json_encode(['id' => 'X', 'purchase_units' => [['invoice_id' => $invoiceId]]])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERNOAMT');

        /* Assert */
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERNOAMT');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_when_the_order_names_an_invoice_that_does_not_exist(): void
    {
        /* Arrange */
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails(987654, '50.00'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERNOINV');

        /* Assert */
        self::assertSame('Invoice Not Found', $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERNOINV');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_when_the_invoice_has_left_the_guest_visible_statuses(): void
    {
        /* Arrange: the invoice was moved out of sent/viewed/paid after the order was created */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 5]);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.00'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERHIDDEN');

        /* Assert */
        self::assertSame('Invoice Not Found', $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERHIDDEN');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_a_fully_paid_invoice_and_says_it_was_already_paid(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.00'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERPAID');

        /* Assert: the dedicated "already paid" message, not the generic amount-mismatch one */
        self::assertSame('This invoice was already paid.', $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERPAID');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_a_zero_amount_order_against_a_zero_balance(): void
    {
        /* Arrange: balance 0 and an order of 0.00 would satisfy the amount comparison, so only
         * the balance guard stands between this request and a capture */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '0.00'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERZERO');

        /* Assert */
        self::assertSame('This invoice was already paid.', $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERZERO');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_against_an_overpaid_invoice(): void
    {
        /* Arrange: a negative balance (credit) is "nothing left to pay" too */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '-5.00']);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.00'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERCREDIT');

        /* Assert */
        self::assertSame('This invoice was already paid.', $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERCREDIT');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_an_order_larger_than_the_balance(): void
    {
        /* Arrange: the balance was reduced (e.g. an offline payment) after the order was created */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '50.00']);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '60.00'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERBIG');

        /* Assert */
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERBIG');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_capture_an_order_one_cent_away_from_the_balance(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '50.00']);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.01'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERCENT');

        /* Assert */
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERCENT');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_captures_an_order_within_half_a_cent_of_the_balance(): void
    {
        /* Arrange: float noise below the documented 0.005 tolerance must not block a real payment */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '50.00']);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.004'),
            $this->captureResult($invoiceId, '50.00', 'CAP-HALF-CENT'),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERHALF');

        /* Assert */
        self::assertStringEndsWith('/v2/checkout/orders/ORDERHALF/capture', $this->lastPaypalRequest()['url']);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-HALF-CENT']);
    }

    #[Test]
    public function it_still_captures_an_invoice_with_exactly_one_currency_unit_outstanding(): void
    {
        /* Arrange: the "already paid" guard is balance <= 0, so a balance of 1.00 is a live debt */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '1.00']);
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '1.00'),
            $this->captureResult($invoiceId, '1.00', 'CAP-ONE-UNIT'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERONE');

        /* Assert */
        self::assertNotSame('This invoice was already paid.', $response->sessionValue('alert_error'));
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-ONE-UNIT', 'payment_amount' => '1.00']);
    }

    #[Test]
    public function it_compares_the_order_currency_case_insensitively(): void
    {
        /* Arrange: gateway setting and PayPal both use lower case */
        $this->setSetting('gateway_paypal_currency', 'eur');
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.00', 'eur'),
            $this->captureResult($invoiceId, '50.00', 'CAP-LOWER', 'eur'),
        ]);

        /* Act */
        $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERLOWER');

        /* Assert */
        self::assertStringEndsWith('/capture', $this->lastPaypalRequest()['url']);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'CAP-LOWER']);
    }

    #[Test]
    public function it_does_not_capture_an_order_in_another_currency_even_when_the_amount_matches(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $this->mockPaypal([
            $this->authResponse(),
            $this->orderDetails($invoiceId, '50.00', 'USD'),
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDERUSD');

        /* Assert */
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
        $this->assertNoCaptureWasSent('ORDERUSD');
        $this->assertNothingRecorded();
    }

    #[Test]
    public function it_does_not_send_anything_to_paypal_for_a_malformed_order_id(): void
    {
        /* Arrange: an EMPTY queue; "ORDER.1" fails PaypalLib's local order-id validation */
        $this->mockPaypal([]);

        /* Act */
        $response = $this->post('/guest/gateways/paypal/paypal_capture_payment/ORDER.1');

        /* Assert: refused locally, no OAuth, no lookup, no capture */
        self::assertSame(self::FAILED, $response->sessionValue('alert_error'));
        self::assertSame('', (string) file_get_contents($this->captureFile), 'A malformed order id must not cause any PayPal request.');
        $this->assertNothingRecorded();
    }

    private function assertNoCaptureWasSent(string $orderId): void
    {
        $last = $this->lastPaypalRequest();
        self::assertSame('GET', $last['method'], 'After a refused preflight the last PayPal request must be the order lookup.');
        self::assertStringEndsWith('/v2/checkout/orders/' . $orderId, $last['url']);
        self::assertStringNotContainsString('/capture', $last['url']);
    }

    private function assertNothingRecorded(): void
    {
        $this->assertDatabaseCount('ip_payments', $this->paymentsAtStart);
        $this->assertDatabaseCount('ip_merchant_responses', 0);
    }

    /** @return array{method: string, url: string} */
    private function lastPaypalRequest(): array
    {
        $raw = (string) file_get_contents($this->captureFile);
        self::assertNotSame('', $raw, 'Expected at least one outbound PayPal request to have been recorded.');

        return json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
    }

    private function setSetting(string $key, string $value): void
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => $key, 'setting_value' => $value]);
        $this->databaseUpdate('ip_settings', ['setting_value' => $value], ['setting_key' => $key]);
    }

    private function seedPayableInvoice(array $overrides = [], array $amountOverrides = []): int
    {
        $clientId = $this->seedClient();

        return $this->seedInvoice($clientId, array_merge(['invoice_status_id' => 2], $overrides), array_merge(['invoice_balance' => '50.00'], $amountOverrides));
    }

    private function mockPaypal(array $responses): void
    {
        $this->withEnvironment([
            'PAYPAL_MOCK_RESPONSES'       => json_encode($responses),
            'PAYPAL_MOCK_REQUEST_CAPTURE' => $this->captureFile,
        ]);
    }

    private function authResponse(): array
    {
        return ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])];
    }

    private function orderDetails(int $invoiceId, string $amount, string $currency = 'EUR'): array
    {
        return ['status' => 200, 'body' => json_encode([
            'purchase_units' => [[
                'invoice_id' => $invoiceId,
                'amount'     => ['value' => $amount, 'currency_code' => $currency],
            ]],
            'id' => 'PAYPAL-ORDERRESOURCE',
        ])];
    }

    private function captureResult(int $invoiceId, string $amount, string $captureId, string $currency = 'EUR'): array
    {
        return ['status' => 200, 'body' => json_encode([
            'purchase_units' => [[
                'payments' => ['captures' => [[
                    'status'     => 'COMPLETED',
                    'invoice_id' => $invoiceId,
                    'id'         => $captureId,
                    'amount'     => ['value' => $amount, 'currency_code' => $currency],
                ]]],
            ]],
            'id' => 'PAYPAL-ORDERRESOURCE',
        ])];
    }
}
