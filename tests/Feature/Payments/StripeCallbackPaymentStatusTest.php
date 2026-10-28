<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * The Stripe callback books money only for payment_status "paid". Stripe also reports "unpaid" and
 * "no_payment_required" (a free or fully discounted session); neither is a payment, and a check written as
 * "anything but unpaid" would book the second one.
 */
#[Group('security')]
#[Group('payments')]
final class StripeCallbackPaymentStatusTest extends AbstractTestCase
{
    // Matches ipconfig.php's ENCRYPTION_KEY for the test DB, so the request subprocess can decrypt the fake API key;
    // without it the Stripe client fails before the callback logic runs and every "books nothing" test passes vacuously.
    private const ENCRYPTION_KEY = '0123456789abcdef0123456789abcdef';

    protected function setUp(): void
    {
        parent::setUp();

        require_once ROOT_PATH . '/application/libraries/Cryptor.php';
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_apiKey', 'setting_value' => \Cryptor::Encrypt('sk_test_fake_key', self::ENCRYPTION_KEY)]);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function notPaidStatuses(): array
    {
        return [
            'unpaid'              => ['unpaid'],
            'no_payment_required' => ['no_payment_required'],
        ];
    }

    #[Test]
    #[DataProvider('notPaidStatuses')]
    public function it_books_nothing_for_a_session_that_is_not_paid(string $status): void
    {
        /* Arrange */
        [$invoiceId, $urlKey] = $this->seedInvoiceAndGateway();
        $this->mockSession($status, $urlKey, 'pi_not_paid_' . $status);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 2]);
    }

    #[Test]
    public function it_books_the_payment_for_a_paid_session(): void
    {
        /* Arrange: positive control with the same session shape */
        [$invoiceId, $urlKey] = $this->seedInvoiceAndGateway();
        $this->mockSession('paid', $urlKey, 'pi_paid_ok');

        /* Act */
        $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'pi_paid_ok']);
    }

    /**
     * @return array{0: int, 1: string}
     */
    private function seedInvoiceAndGateway(): array
    {
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_payment_method', 'setting_value' => '1']);
        $invoiceId = $this->seedInvoice($this->seedClient(), ['invoice_status_id' => 2], ['invoice_balance' => '50.00']);

        return [$invoiceId, $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key']];
    }

    private function mockSession(string $status, string $urlKey, string $intent): void
    {
        $this->withEnvironment(['STRIPE_MOCK_RESPONSES' => json_encode([['status' => 200, 'body' => json_encode([
            'id'                     => 'cs_test_callback',
            'object'                 => 'checkout.session',
            'status'                 => 'complete',
            'mode'                   => 'payment',
            'payment_status'         => $status,
            'client_reference_id'    => $urlKey,
            'payment_intent'         => $intent,
            'currency'               => 'eur',
            'amount_total'           => 5000,
            'amount_received'        => 0,
            'application_fee_amount' => 0,
            'livemode'               => false,
            'cancellation_reason'    => null,
            'last_payment_error'     => null,
        ])]])]);
    }
}
