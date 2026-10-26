<?php

namespace Tests\Feature\Payments;

use Cryptor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-r382-4chp-xj3p: Stripe::callback() used to dereference unassigned $session / $invoice
 * in its finally block, so any well-formed but unknown checkout session id ended in a 500.
 * StripeFlowTest covers the malformed-id guard only; these tests cover the retrieve() failure,
 * the unresolvable invoice, and the lazily built Stripe client, asserting the redirect
 * DESTINATION (the finally block's redirect(), which a crash would never reach).
 */
#[Group('feature')]
#[Group('security')]
#[Group('payments')]
final class StripeCallbackFailureTest extends AbstractTestCase
{
    private const ENCRYPTION_KEY = '0123456789abcdef0123456789abcdef';

    private const ERROR_MESSAGE = 'Payment system error detected! If this message persists, please contact us.';

    protected function setUp(): void
    {
        parent::setUp();

        require_once ROOT_PATH . '/application/libraries/Cryptor.php';
        $this->databaseInsertOrIgnore('ip_settings', [
            'setting_key'   => 'gateway_stripe_apiKey',
            'setting_value' => Cryptor::Encrypt('sk_test_fake_key', self::ENCRYPTION_KEY),
        ]);
    }

    #[Test]
    public function it_redirects_cleanly_when_stripe_does_not_know_the_checkout_session(): void
    {
        /* Arrange: well-formed id, Stripe answers 404 resource_missing */
        $this->mockStripe([[
            'status' => 404,
            'body'   => json_encode(['error' => ['type' => 'invalid_request_error', 'code' => 'resource_missing', 'message' => 'No such checkout.session']]),
        ]]);
        $merchantBefore = $this->databaseCount('ip_merchant_responses');
        $paymentsBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_doesnotexist');

        /* Assert: no 500, a redirect to the invoice list, an error flash, and no orphan log row */
        $this->assertResponseRedirectsToRoute($response, 'guest/view/invoices');
        self::assertSame(self::ERROR_MESSAGE, $response->sessionValue('alert_error'));
        self::assertSame($merchantBefore, $this->databaseCount('ip_merchant_responses'));
        self::assertSame($paymentsBefore, $this->databaseCount('ip_payments'));
    }

    #[Test]
    public function it_redirects_cleanly_when_the_session_names_no_known_invoice(): void
    {
        /* Arrange: Stripe answers, but client_reference_id matches no invoice */
        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => 'no-such-invoice-key',
            'payment_intent'      => 'pi_orphan',
        ])]);
        $merchantBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'guest/view/invoices');
        self::assertSame(self::ERROR_MESSAGE, $response->sessionValue('alert_error'));
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_orphan']);
        self::assertSame($merchantBefore, $this->databaseCount('ip_merchant_responses'));
    }

    #[Test]
    public function it_redirects_to_the_invoice_and_logs_the_attempt_when_a_paid_session_has_no_payment_intent(): void
    {
        /* Arrange: invoice resolved, then the handler throws (no payment_intent) */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => null,
        ])]);
        $paymentsBefore = $this->databaseCount('ip_payments');

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: error flash, redirect back to THIS invoice, an audit row, and no money booked */
        $this->assertResponseRedirectsToRoute($response, 'guest/view/invoice/' . $urlKey);
        self::assertStringContainsString(self::ERROR_MESSAGE, (string) $response->sessionValue('alert_error'));
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_driver' => 'Stripe']);
        self::assertSame($paymentsBefore, $this->databaseCount('ip_payments'));
        $this->assertDatabaseHas('ip_invoice_amounts', ['invoice_id' => $invoiceId, 'invoice_balance' => '50.00']);
    }

    #[Test]
    public function it_redirects_to_the_invoice_after_a_successful_payment(): void
    {
        /* Arrange: positive control for the redirect destination and the recorded payment */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_payment_method', 'setting_value' => '1']);
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_dest_1',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'guest/view/invoice/' . $urlKey);
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'pi_dest_1']);
        self::assertNull($response->sessionValue('alert_error'));
    }

    #[Test]
    public function it_does_not_need_a_stripe_api_key_to_reject_a_bad_request(): void
    {
        /* Arrange: gateway not configured; the SDK client must only be built when needed */
        $this->databaseDelete('ip_settings', ['setting_key' => 'gateway_stripe_apiKey']);
        $this->mockStripe([]);

        /* Act */
        $malformed = $this->get('/guest/gateways/stripe/callback/cs_evil.payload:1');
        $nonPost   = $this->get('/guest/gateways/stripe/create_checkout_session/whatever');
        $unknown   = $this->post('/guest/gateways/stripe/create_checkout_session/does-not-exist');

        /* Assert: clean guard responses, never a 500 from an eager StripeClient */
        $this->assertResponseRedirectsToRoute($malformed, 'guest/view/invoices');
        $this->assertResponseStatusCode($nonPost, 404);
        $this->assertResponseStatusCode($unknown, 404);
    }

    private function seedPayableInvoice(): int
    {
        return $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '50.00']);
    }

    private function mockStripe(array $responses): void
    {
        $this->withEnvironment(['STRIPE_MOCK_RESPONSES' => json_encode($responses)]);
    }

    private function sessionResponse(array $overrides): array
    {
        return ['status' => 200, 'body' => json_encode(array_merge([
            'id'                     => 'cs_test_callback',
            'object'                 => 'checkout.session',
            'status'                 => 'complete',
            'mode'                   => 'payment',
            'payment_status'         => 'unpaid',
            'client_reference_id'    => null,
            'payment_intent'         => null,
            'currency'               => 'eur',
            'amount_total'           => 5000,
            'amount_received'        => 0,
            'application_fee_amount' => 0,
            'livemode'               => false,
            'cancellation_reason'    => null,
            'last_payment_error'     => null,
        ], $overrides))];
    }
}
