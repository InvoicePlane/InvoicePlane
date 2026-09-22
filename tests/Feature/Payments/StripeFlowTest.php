<?php

namespace Tests\Feature\Payments;

use Cryptor;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * guest/gateways/Stripe.php end-to-end payment flow tests.
 *
 * The Stripe SDK's HTTP layer is process-global (Stripe\ApiRequestor::setHttpClient()),
 * so the guest Stripe controller swaps it for a canned response queue in the test
 * environment (see Stripe::useTestHttpClientIfConfigured()). Responses are consumed
 * in the order the controller calls the SDK.
 */
class StripeFlowTest extends AbstractTestCase
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
        $countBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->get('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $countAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($countBefore, $countAfter);
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->get('/guest/gateways/stripe/create_checkout_session/invalid-key-123');
        $this->assertResponseStatusCode($response2, 404);

        $response3 = $this->get('/guest/gateways/stripe/create_checkout_session/');
        $this->assertResponseStatusCode($response3, 404);

        /* Assert: Idempotency (E) */
        $response4 = $this->get('/guest/gateways/stripe/create_checkout_session/' . $urlKey);
        $this->assertResponseStatusCode($response4, 404);
    }

    #[Test]
    public function it_returns_404_for_checkout_session_on_an_unknown_invoice_key(): void
    {
        /* Arrange */
        $countBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/does-not-exist');

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $countAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($countBefore, $countAfter);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/guest/gateways/stripe/create_checkout_session/');
        $this->assertResponseStatusCode($response2, 404);

        $response3 = $this->post('/guest/gateways/stripe/create_checkout_session/null');
        $this->assertResponseStatusCode($response3, 404);

        /* Assert: Idempotency (E) */
        $response4 = $this->post('/guest/gateways/stripe/create_checkout_session/does-not-exist');
        $this->assertResponseStatusCode($response4, 404);
    }

    #[Test]
    public function it_returns_404_for_checkout_session_on_a_draft_invoice(): void
    {
        /* Arrange: draft (status 1) invoices are never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $countBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 404);

        /* Assert: State Isolation (B) */
        $countAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($countBefore, $countAfter);
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame(1, (int) $invoice['invoice_status_id']);

        /* Assert: Boundary Cases (F) */
        $nonExistentKey = 'draft-' . uniqid();
        $response2 = $this->post('/guest/gateways/stripe/create_checkout_session/' . $nonExistentKey);
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);
        $this->assertResponseStatusCode($response3, 404);
    }

    #[Test]
    public function it_redirects_checkout_session_for_an_already_paid_invoice_without_calling_stripe(): void
    {
        /* Arrange: no STRIPE_MOCK_RESPONSES queued — a live call would error */
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $countBefore = $this->databaseCount('ip_merchant_responses');

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect(), sprintf('Expected a redirect, got [%d].', $response->statusCode()));

        /* Assert: State Isolation (B) */
        $countAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($countBefore, $countAfter);
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame('0.00', $invoice['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $paidInvoice = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $paidUrlKey = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $paidInvoice])['invoice_url_key'];
        $response2 = $this->post('/guest/gateways/stripe/create_checkout_session/' . $paidUrlKey);
        $this->assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);
        self::assertTrue($response3->isRedirect());
    }

    #[Test]
    public function it_creates_a_checkout_session_for_a_payable_invoice(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $countBefore = $this->databaseCount('ip_merchant_responses');

        $this->mockStripe([
            ['status' => 200, 'body' => json_encode([
                'id'            => 'cs_test_123',
                'object'        => 'checkout.session',
                'client_secret' => 'cs_test_123_secret_abc',
            ])],
        ]);

        /* Act */
        $response = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);
        $json = json_decode($response->body(), true);
        self::assertSame('cs_test_123_secret_abc', $json['clientSecret'] ?? null);

        /* Assert: Business Logic (A) */
        $this->assertResponseBodyContains($response, 'clientSecret');

        /* Assert: State Isolation (B) */
        $countAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($countBefore, $countAfter);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertGreaterThan(0, (int) $invoice['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $response2 = $this->post('/guest/gateways/stripe/create_checkout_session/nonexistent');
        $this->assertResponseStatusCode($response2, 404);

        /* Assert: Idempotency (E) */
        $response3 = $this->post('/guest/gateways/stripe/create_checkout_session/' . $urlKey);
        $this->assertResponseStatusCode($response3, 200);
        $json3 = json_decode($response3->body(), true);
        self::assertSame('cs_test_123_secret_abc', $json3['clientSecret'] ?? null);
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

        /* Assert: Business Logic (A) */
        $request = json_decode((string) file_get_contents($captureFile), true, 512, JSON_THROW_ON_ERROR);
        self::assertSame('JPY', $request['params']['line_items'][0]['price_data']['currency']);
        self::assertSame(100, $request['params']['line_items'][0]['price_data']['unit_amount']);

        /* Assert: Error Semantics (C) */
        $this->assertResponseStatusCode($response, 200);

        /* Assert: State Isolation (B) */
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame('100.00', $invoice['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $testSettings = $this->databaseFetchOne('ip_settings', ['setting_key' => 'gateway_stripe_currency']);
        $this->assertSame('JPY', $testSettings['setting_value']);
    }

    #[Test]
    public function it_records_a_paid_callback_and_creates_a_payment(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_payment_method', 'setting_value' => '1']);
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_success_1',
            'currency'            => 'eur',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());

        /* Assert: Business Logic (A) */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'pi_success_1', 'payment_amount' => '50.00']);
        $this->assertDatabaseHas('ip_merchant_responses', ['invoice_id' => $invoiceId, 'merchant_response_successful' => 1]);

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore + 1, $paymentCountAfter);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_external_id' => 'pi_success_1']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);
        $merchant = $this->databaseFetchOne('ip_merchant_responses', ['invoice_id' => $invoiceId]);
        $this->assertSame(1, (int) $merchant['merchant_response_successful']);

        /* Assert: Boundary Cases (F) */
        $testMerchant = $this->databaseFetchOne('ip_merchant_responses', ['invoice_id' => $invoiceId]);
        $this->assertGreaterThan(0, (int) $testMerchant['merchant_response_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        self::assertTrue($response2->isRedirect());
        $this->assertDatabaseCount('ip_payments', $paymentCountAfter, ['payment_external_id' => 'pi_success_1']);
    }

    #[Test]
    public function it_does_not_duplicate_a_payment_for_an_already_processed_payment_intent(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $this->seedPayment($invoiceId, ['payment_external_id' => 'pi_dup', 'payment_amount' => '50.00']);
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_dup',
            'currency'            => 'eur',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: Error Semantics (C) */
        $this->assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => 'pi_dup']);

        /* Assert: Data Integrity (D) */
        $payment = $this->databaseFetchOne('ip_payments', ['payment_external_id' => 'pi_dup']);
        $this->assertSame($invoiceId, (int) $payment['invoice_id']);
        $this->assertSame('50.00', $payment['payment_amount']);

        /* Assert: Boundary Cases (F) */
        $this->databaseInsertOrIgnore('ip_payments', ['invoice_id' => 99999, 'payment_external_id' => 'pi_boundary', 'payment_amount' => '1.00']);
        $boundaryPayment = $this->databaseFetchOne('ip_payments', ['payment_external_id' => 'pi_boundary']);
        $this->assertGreaterThan(0, (int) $boundaryPayment['payment_id']);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        $this->assertTrue($response2->isRedirect());
        $this->assertDatabaseCount('ip_payments', $paymentCountAfter, ['payment_external_id' => 'pi_dup']);
    }

    #[Test]
    public function it_does_not_record_a_payment_when_the_invoice_is_already_fully_paid(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_already_paid',
            'currency'            => 'eur',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: Error Semantics (C) */
        $this->assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_already_paid']);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame('0.00', $invoice['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $paidInvoice2 = $this->seedPayableInvoice([], ['invoice_balance' => '0.00']);
        $paidUrlKey2 = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $paidInvoice2])['invoice_url_key'];
        $response2 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        $this->assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        $this->assertTrue($response3->isRedirect());
        $this->assertDatabaseCount('ip_payments', $paymentCountAfter, ['payment_external_id' => 'pi_already_paid']);
    }

    #[Test]
    public function it_rejects_a_callback_whose_currency_does_not_match_the_gateway_setting(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_bad_ccy',
            'currency'            => 'usd',
            'amount_total'        => 5000,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: Error Semantics (C) */
        $this->assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_bad_ccy']);

        /* Assert: Data Integrity (D) */
        $setting = $this->databaseFetchOne('ip_settings', ['setting_key' => 'gateway_stripe_currency']);
        $this->assertSame('EUR', $setting['setting_value']);

        /* Assert: Boundary Cases (F) */
        $testCurrency = $this->databaseFetchOne('ip_settings', ['setting_key' => 'gateway_stripe_currency']);
        $this->assertNotEmpty($testCurrency['setting_value']);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        $this->assertTrue($response2->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_bad_ccy']);
    }

    #[Test]
    public function it_rejects_a_callback_whose_amount_is_short_of_the_invoice_balance(): void
    {
        /* Arrange */
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $invoiceId = $this->seedPayableInvoice([], ['invoice_balance' => '50.00']);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_short',
            'currency'            => 'eur',
            'amount_total'        => 1000,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: Error Semantics (C) */
        $this->assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_short']);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame('50.00', $invoice['invoice_balance']);

        /* Assert: Boundary Cases (F) */
        $testInvoice = $this->seedPayableInvoice([], ['invoice_balance' => '100.00']);
        $testBalance = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $testInvoice]);
        $this->assertSame('100.00', $testBalance['invoice_balance']);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        $this->assertTrue($response2->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_short']);
    }

    #[Test]
    public function it_does_not_record_a_payment_for_an_unpaid_callback(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice();
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $paymentCountBefore = $this->databaseCount('ip_payments');

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'unpaid',
            'client_reference_id' => $urlKey,
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertGreaterThan(0, (int) $invoice['invoice_id']);

        /* Assert: Boundary Cases (F) */
        $testInvoice = $this->seedPayableInvoice();
        $testUrlKey = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $testInvoice])['invoice_url_key'];
        $this->assertNotEmpty($testUrlKey);

        /* Assert: Idempotency (E) */
        $response2 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        self::assertTrue($response2->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_records_an_error_response_when_the_callback_invoice_is_not_guest_visible(): void
    {
        /* Arrange: draft invoice — never guest_visible() */
        $invoiceId = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
        $paymentCountBefore = $this->databaseCount('ip_payments');
        $merchantCountBefore = $this->databaseCount('ip_merchant_responses');

        $this->mockStripe([$this->sessionResponse([
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => 'pi_not_visible',
        ])]);

        /* Act */
        $response = $this->get('/guest/gateways/stripe/callback/cs_test_callback');

        /* Assert: Error Semantics (C) */
        self::assertTrue($response->isRedirect());

        /* Assert: State Isolation (B) */
        $paymentCountAfter = $this->databaseCount('ip_payments');
        $merchantCountAfter = $this->databaseCount('ip_merchant_responses');
        $this->assertSame($paymentCountBefore, $paymentCountAfter);
        $this->assertSame($merchantCountBefore, $merchantCountAfter);

        /* Assert: Business Logic (A) */
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_not_visible']);
        $this->assertDatabaseMissing('ip_merchant_responses', ['invoice_id' => $invoiceId]);

        /* Assert: Data Integrity (D) */
        $invoice = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId]);
        $this->assertSame(1, (int) $invoice['invoice_status_id']);

        /* Assert: Boundary Cases (F) */
        $draftInvoice = $this->seedPayableInvoice(['invoice_status_id' => 1]);
        $draftUrlKey = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $draftInvoice])['invoice_url_key'];
        $response2 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        $this->assertTrue($response2->isRedirect());

        /* Assert: Idempotency (E) */
        $response3 = $this->get('/guest/gateways/stripe/callback/cs_test_callback');
        self::assertTrue($response3->isRedirect());
        $this->assertDatabaseMissing('ip_payments', ['payment_external_id' => 'pi_not_visible']);
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
