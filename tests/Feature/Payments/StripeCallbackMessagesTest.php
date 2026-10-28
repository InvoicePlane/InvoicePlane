<?php

namespace Tests\Feature\Payments;

use Cryptor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Stripe::callback() sets a status-specific message when it refuses a paid session (duplicate payment
 * intent, invoice already paid, currency or amount mismatch). The generic "payment failed" message used to
 * be assigned afterwards unconditionally, so the guest never saw why the payment was refused.
 */
#[Group('feature')]
#[Group('payments')]
final class StripeCallbackMessagesTest extends AbstractTestCase
{
    private const ENCRYPTION_KEY = '0123456789abcdef0123456789abcdef';

    protected function setUp(): void
    {
        parent::setUp();

        require_once ROOT_PATH . '/application/libraries/Cryptor.php';
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_apiKey', 'setting_value' => Cryptor::Encrypt('sk_test_fake_key', self::ENCRYPTION_KEY)]);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_payment_method', 'setting_value' => '1']);
    }

    #[Test]
    public function it_tells_the_guest_the_payment_was_already_processed_for_a_duplicate_payment_intent(): void
    {
        /* Arrange: the same payment intent is already booked on another payment row */
        $invoiceId = $this->seedPayableInvoice();
        $this->seedPayment($invoiceId, ['payment_external_id' => 'pi_duplicate', 'payment_amount' => '1.00']);
        $urlKey = $this->urlKeyOf($invoiceId);
        $this->mockStripe($this->paidSession($urlKey, 'pi_duplicate', 5000));
        $paymentsBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: the specific message, and nothing booked a second time */
        $this->assertResponseRedirectsToRoute($response, 'guest/view/invoice/' . $urlKey);
        self::assertSame('This payment has already been processed and recorded.', $response->sessionValue('alert_info'));
        self::assertSame($paymentsBefore, $this->databaseCount('ip_payments'));
    }

    #[Test]
    public function it_tells_the_guest_the_invoice_was_already_paid_when_nothing_is_left_to_pay(): void
    {
        /* Arrange */
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '0.00']);
        $urlKey    = $this->urlKeyOf($invoiceId);
        $this->mockStripe($this->paidSession($urlKey, 'pi_late', 5000));

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'guest/view/invoice/' . $urlKey);
        self::assertSame('This invoice was already paid.', $response->sessionValue('alert_info'));
        self::assertSame(0, $this->databaseCount('ip_payments', ['payment_external_id' => 'pi_late']));
    }

    #[Test]
    public function it_tells_the_guest_the_payment_failed_when_the_capture_currency_differs_from_the_gateway_currency(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->urlKeyOf($invoiceId);
        $this->mockStripe($this->paidSession($urlKey, 'pi_usd', 5000, 'usd'));

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        self::assertSame('Payment failed. Please try again.', $response->sessionValue('alert_info'));
        self::assertSame(0, $this->databaseCount('ip_payments', ['payment_external_id' => 'pi_usd']));
    }

    #[Test]
    public function it_tells_the_guest_the_payment_failed_when_the_capture_is_below_the_invoice_balance(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->urlKeyOf($invoiceId);
        $this->mockStripe($this->paidSession($urlKey, 'pi_short', 100));

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        self::assertSame('Payment failed. Please try again.', $response->sessionValue('alert_info'));
        self::assertSame(0, $this->databaseCount('ip_payments', ['payment_external_id' => 'pi_short']));
    }

    #[Test]
    public function it_confirms_a_successful_payment_with_the_invoice_number_and_books_it(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->urlKeyOf($invoiceId);
        $number    = (string) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_number'];
        $this->mockStripe($this->paidSession($urlKey, 'pi_ok', 5000));

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: the success message, not a refusal, and exactly one payment row */
        self::assertSame('Payment successful for invoice #' . $number, $response->sessionValue('alert_success'));
        self::assertNull($response->sessionValue('alert_info'));
        self::assertSame(1, $this->databaseCount('ip_payments', ['payment_external_id' => 'pi_ok']));
    }

    #[Test]
    public function it_falls_back_to_the_generic_message_when_a_session_was_not_paid_and_nothing_more_specific_applies(): void
    {
        /* Arrange: a checkout session that is still unpaid */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->urlKeyOf($invoiceId);
        $this->mockStripe($this->paidSession($urlKey, 'pi_unpaid', 5000, 'eur', 'unpaid'));

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        self::assertSame('Payment failed<br>Payment was not completed. Stripe session status: unpaid', $response->sessionValue('alert_info'));
        self::assertSame(0, $this->databaseCount('ip_payments', ['payment_external_id' => 'pi_unpaid']));
    }

    private function seedPayableInvoice(): int
    {
        return $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '50.00']);
    }

    private function urlKeyOf(int $invoiceId): string
    {
        return (string) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
    }

    private function mockStripe(array $responses): void
    {
        $this->withEnvironment(['STRIPE_MOCK_RESPONSES' => json_encode($responses)]);
    }

    private function paidSession(string $urlKey, string $paymentIntent, int $amountMinor, string $currency = 'eur', string $paymentStatus = 'paid'): array
    {
        return [['status' => 200, 'body' => json_encode([
            'id'                     => 'cs_test_callback',
            'object'                 => 'checkout.session',
            'status'                 => 'complete',
            'mode'                   => 'payment',
            'payment_status'         => $paymentStatus,
            'client_reference_id'    => $urlKey,
            'payment_intent'         => $paymentIntent,
            'currency'               => $currency,
            'amount_total'           => $amountMinor,
            'amount_received'        => $amountMinor,
            'application_fee_amount' => 0,
            'livemode'               => false,
            'cancellation_reason'    => null,
            'last_payment_error'     => null,
        ])]];
    }
}
