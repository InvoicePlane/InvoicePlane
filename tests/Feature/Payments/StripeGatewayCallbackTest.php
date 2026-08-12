<?php

namespace Tests\Feature\Payments;

use Cryptor;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/gateways/Stripe.php feature tests.
 *
 * The Stripe SDK's HTTP layer is process-global (Stripe\ApiRequestor::setHttpClient()),
 * so the guest Stripe controller swaps it for a canned response queue in the test
 * environment (see Stripe::useTestHttpClientIfConfigured()). Responses are consumed
 * in the order the controller calls the SDK.
 */
class StripeGatewayCallbackTest extends AbstractTestCase
{
    // Matches ipconfig.php's ENCRYPTION_KEY for this test DB, so Crypt::decode()
    // in the request subprocess correctly recovers the plaintext fake API key.
    private const ENCRYPTION_KEY = '0123456789abcdef0123456789abcdef';

    /** @var array<int, string> */
    private array $captureFiles = [];

    protected function setUp(): void
    {
        parent::setUp();

        // StripeClient's constructor validates api_key eagerly, before any guard
        // clause runs, so every test — even the 404 ones — needs a syntactically
        // valid (encrypted-at-rest, like the real setting) fake key.
        require_once dirname(__DIR__, 3) . '/application/libraries/Cryptor.php';
        $ciphertext = Cryptor::Encrypt('sk_test_fake_key', self::ENCRYPTION_KEY);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_apiKey', 'setting_value' => $ciphertext]);
    }

    protected function tearDown(): void
    {
        foreach ($this->captureFiles as $captureFile) {
            if (is_file($captureFile)) {
                unlink($captureFile);
            }
        }

        $this->captureFiles = [];

        parent::tearDown();
    }

    // -------------------------------------------------------------------------
    // create_checkout_session
    // -------------------------------------------------------------------------

    #[Test]
    public function it_returns_404_for_a_non_post_checkout_session_request(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        /* Act */
        $response = $this->get('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_checkout_session_on_an_unknown_invoice_key(): void
    {
        /* Arrange */
        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/does-not-exist');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_404_for_checkout_session_on_a_draft_invoice(): void
    {
        /* Arrange: draft (status 1) invoices are never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_redirects_checkout_session_for_an_already_paid_invoice_without_calling_stripe(): void
    {
        /* Arrange: no STRIPE_MOCK_RESPONSES queued — a live call would error */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert */
        self::assertTrue($response->isRedirect(), sprintf('Expected a redirect, got [%d].', $response->statusCode()));
    }

    #[Test]
    public function it_creates_a_checkout_session_for_a_payable_invoice(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockStripe([
            ['status' => 200, 'body' => json_encode([
                'id'            => 'cs_test_123',
                'object'        => 'checkout.session',
                'client_secret' => 'cs_test_123_secret_abc',
            ])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $json = json_decode($response->body(), true);
        self::assertSame('cs_test_123_secret_abc', $json['clientSecret'] ?? null);
    }

    #[Test]
    public function it_sends_a_jpy_invoice_total_as_100_minor_units_to_stripe_checkout(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'JPY']);
        $invoiceId   = $this->seedPayableInvoice([], ['invoice_balance' => '100.00']);
        $urlKey      = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $captureFile = tempnam(sys_get_temp_dir(), 'stripe-request-');
        self::assertNotFalse($captureFile);
        $this->captureFiles[] = $captureFile;

        $this->withEnvironment([
            'STRIPE_MOCK_RESPONSES' => json_encode([
                ['status' => 200, 'body' => json_encode([
                    'id'            => 'cs_jpy_100',
                    'object'        => 'checkout.session',
                    'client_secret' => 'cs_jpy_100_secret',
                ])],
            ]),
            'STRIPE_MOCK_REQUEST_CAPTURE' => $captureFile,
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $request = json_decode((string) file_get_contents($captureFile), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('JPY', $request['params']['line_items'][0]['price_data']['currency']);
        self::assertSame(100, $request['params']['line_items'][0]['price_data']['unit_amount']);
    }

    #[Test]
    public function it_records_a_paid_callback_and_creates_a_payment(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_payment_method', 'setting_value' => '1']);
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_success_1',
            'currency'            => 'eur',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'pi_success_1', 'payment_amount' => '50.00']);
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 1]);
    }

    #[Test]
    public function it_does_not_duplicate_a_payment_for_an_already_processed_payment_intent(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $this->seedPayment($invoiceId, ['payment_external_id' => 'pi_dup', 'payment_amount' => '50.00']);

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_dup',
            'currency'            => 'eur',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: still exactly one payment row for this intent, not two */
        $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => 'pi_dup']);
    }

    #[Test]
    public function it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_already_paid',
            'currency'            => 'eur',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_already_paid']);
    }

    #[Test]
    public function it_rejects_a_callback_whose_currency_does_not_match_the_gateway_setting(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_bad_ccy',
            'currency'            => 'usd',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_bad_ccy']);
    }

    #[Test]
    public function it_rejects_a_callback_whose_amount_is_short_of_the_invoice_balance(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '50.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_short',
            'currency'            => 'eur',
            'amount_total'        => 1000,
        ])]);

        /* Act */
        $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_short']);
    }

    #[Test]
    public function it_does_not_record_a_payment_for_an_unpaid_callback(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'unpaid',
            'client_reference_id' => $urlKey,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_records_an_error_response_when_the_callback_invoice_is_not_guest_visible(): void
    {
        /* Arrange: draft invoice — never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_not_visible',
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: the controller's own try/catch handles this, no crash, no mutation */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_not_visible']);
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);
    }

    private function mockStripe(array $responses): void
    {
        $this->withEnvironment(['STRIPE_MOCK_RESPONSES' => json_encode($responses)]);
    }

    private function seedPayableInvoice(array $overrides = [], array $amountOverrides = []): int
    {
        $clientId = $this->seedClient();

        return $this->seedInvoice($clientId, array_merge(['invoice_status_id' => 2], $overrides), array_merge(['invoice_balance' => '50.00'], $amountOverrides));
    }

    // -------------------------------------------------------------------------
    // callback
    // -------------------------------------------------------------------------

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
