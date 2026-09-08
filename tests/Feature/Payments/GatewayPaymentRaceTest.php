<?php

namespace Tests\Feature\Payments;

use Cryptor;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;

/**
 * TOCTOU race in the guest Stripe/PayPal payment callbacks (CWE-362/367).
 *
 * The callbacks read `invoice_balance`, check it, then insert a payment row as
 * separate non-atomic steps. Two concurrent callbacks carrying *distinct*
 * gateway references (so the payment_external_id dedup does not apply) can both
 * pass the balance check before either commits, recording two payments against
 * one balance and driving the invoice balance negative.
 *
 * The fix must make "check balance + record payment" atomic (a single
 * conditional UPDATE gated on the current balance). These tests fire the two
 * callbacks as genuinely parallel subprocesses and assert the money invariant
 * holds every round: at most one payment per invoice, balance never negative,
 * paid never above total.
 */
class GatewayPaymentRaceTest extends AbstractTestCase
{
    private const ENCRYPTION_KEY = '0123456789abcdef0123456789abcdef';

    /** Rounds to fire the concurrent pair — the race is timing-dependent, so retry. */
    private const RACE_ROUNDS = 8;

    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 3) . '/application/libraries/Cryptor.php';

        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_payment_method', 'setting_value' => '1']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_stripe_apiKey', 'setting_value' => Cryptor::Encrypt('sk_test_fake_key', self::ENCRYPTION_KEY)]);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_currency', 'setting_value' => 'EUR']);
        $this->databaseInsertOrIgnore('ip_settings', ['setting_key' => 'gateway_paypal_payment_method', 'setting_value' => '1']);
    }

    #[Test]
    public function it_does_not_double_record_when_two_distinct_stripe_callbacks_race(): void
    {
        for ($round = 1; $round <= self::RACE_ROUNDS; $round++) {
            /* Arrange: a fresh, fully-payable 100.00 invoice each round */
            $invoiceId = $this->seedPayableInvoice(100.00);
            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
            $intentA   = 'pi_race_' . $round . '_a';
            $intentB   = 'pi_race_' . $round . '_b';

            /* Act: two genuinely concurrent paid callbacks, distinct payment_intents */
            $this->fireConcurrently([
                $this->stripeCallbackRequest($urlKey, $intentA, 10000),
                $this->stripeCallbackRequest($urlKey, $intentB, 10000),
            ]);

            /* Assert: the balance covered exactly one payment */
            $this->assertMoneyInvariant($invoiceId, $round);
        }
    }

    #[Test]
    public function it_does_not_double_record_when_two_distinct_paypal_captures_race(): void
    {
        for ($round = 1; $round <= self::RACE_ROUNDS; $round++) {
            /* Arrange */
            $invoiceId = $this->seedPayableInvoice(100.00);

            /* Act: two concurrent completed captures, distinct capture ids */
            $this->fireConcurrently([
                $this->paypalCaptureRequest($invoiceId, 'CAP_race_' . $round . '_a', '100.00'),
                $this->paypalCaptureRequest($invoiceId, 'CAP_race_' . $round . '_b', '100.00'),
            ]);

            /* Assert */
            $this->assertMoneyInvariant($invoiceId, $round);
        }
    }

    #[Test]
    public function it_keeps_one_row_when_the_same_stripe_intent_is_replayed_concurrently(): void
    {
        for ($round = 1; $round <= self::RACE_ROUNDS; $round++) {
            /* Arrange */
            $invoiceId = $this->seedPayableInvoice(100.00);
            $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];
            $intent    = 'pi_replay_' . $round;

            /* Act: the SAME payment_intent delivered twice at once */
            $this->fireConcurrently([
                $this->stripeCallbackRequest($urlKey, $intent, 10000),
                $this->stripeCallbackRequest($urlKey, $intent, 10000),
            ]);

            /* Assert: exactly one row, no crash, balance settled */
            $this->assertMoneyInvariant($invoiceId, $round);
            $this->assertDatabaseCount('ip_payments', 1, ['payment_external_id' => $intent]);
        }
    }

    #[Test]
    public function it_still_records_a_single_full_balance_payment(): void
    {
        /* Arrange: a gateway callback always pays the exact outstanding balance */
        $invoiceId = $this->seedPayableInvoice(100.00);
        $urlKey    = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoiceId])['invoice_url_key'];

        /* Act: one legitimate paid callback for the full 100.00 */
        $this->fireConcurrently([$this->stripeCallbackRequest($urlKey, 'pi_ok_full', 10000)]);

        /* Assert: recorded once, invoice settled, nothing over-credited */
        $this->resetDatabaseConnection();
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_external_id' => 'pi_ok_full', 'payment_amount' => '100.00']);
        $this->assertDatabaseCount('ip_payments', 1, ['invoice_id' => $invoiceId]);
        $amounts = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertEqualsWithDelta(0.0, (float) $amounts['invoice_balance'], 0.001);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId, 'invoice_status_id' => 4]);
    }

    // -------------------------------------------------------------------------

    private function assertMoneyInvariant(int $invoiceId, int $round): void
    {
        $this->resetDatabaseConnection();

        // At most one payment row per invoice — a raced double-record makes this 2.
        $this->assertDatabaseCount('ip_payments', 1, ['invoice_id' => $invoiceId]);

        $amounts = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertNotNull($amounts);
        $paid    = (float) $amounts['invoice_paid'];
        $balance = (float) $amounts['invoice_balance'];
        $total   = (float) $amounts['invoice_total'];

        self::assertGreaterThanOrEqual(-0.001, $balance, sprintf('Round %d: invoice_balance went negative (%.2f) — the callbacks over-credited.', $round, $balance));
        self::assertLessThanOrEqual($total + 0.001, $paid, sprintf('Round %d: invoice_paid (%.2f) exceeds invoice_total (%.2f).', $round, $paid, $total));
    }

    /**
     * @param array<int, array{method: string, uri: string, env: array<string, string>}> $requests
     */
    private function fireConcurrently(array $requests): void
    {
        $this->resetDatabaseConnection();

        $command = sprintf('php %s', escapeshellarg(dirname(__DIR__, 2) . '/Integration/bin/request.php'));
        $baseEnv = getenv();

        $procs = [];
        foreach ($requests as $i => $request) {
            $payload = [
                'method'  => mb_strtoupper($request['method']),
                'uri'     => $request['uri'],
                'query'   => [],
                'post'    => [],
                'cookies' => [],
                'session' => [],
                'env'     => $request['env'],
                'ajax'    => false,
            ];

            $env = array_merge($baseEnv, [
                'CI_TEST_REQUEST' => base64_encode((string) json_encode($payload, JSON_THROW_ON_ERROR)),
            ]);

            $proc = proc_open(
                $command,
                [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
                $pipes,
                dirname(__DIR__, 2),
                $env,
            );

            if ( ! is_resource($proc)) {
                throw new RuntimeException('Unable to start concurrent request process ' . $i);
            }

            fclose($pipes[0]);
            $procs[] = ['proc' => $proc, 'pipes' => $pipes];
        }

        foreach ($procs as $entry) {
            stream_get_contents($entry['pipes'][1]);
            fclose($entry['pipes'][1]);
            stream_get_contents($entry['pipes'][2]);
            fclose($entry['pipes'][2]);
            proc_close($entry['proc']);
        }

        $this->resetDatabaseConnection();
    }

    /**
     * @return array{method: string, uri: string, env: array<string, string>}
     */
    private function stripeCallbackRequest(string $urlKey, string $paymentIntent, int $amountTotalMinor): array
    {
        $session = ['status' => 200, 'body' => json_encode([
            'id'                  => 'cs_' . bin2hex(random_bytes(6)),
            'object'              => 'checkout.session',
            'status'              => 'complete',
            'mode'                => 'payment',
            'payment_status'      => 'paid',
            'client_reference_id' => $urlKey,
            'payment_intent'      => $paymentIntent,
            'currency'            => 'eur',
            'amount_total'        => $amountTotalMinor,
            'amount_received'     => $amountTotalMinor,
            'livemode'            => false,
        ])];

        return [
            'method' => 'GET',
            'uri'    => '/guest/gateways/stripe/callback/cs_test_' . bin2hex(random_bytes(4)),
            'env'    => ['STRIPE_MOCK_RESPONSES' => json_encode([$session])],
        ];
    }

    /**
     * @return array{method: string, uri: string, env: array<string, string>}
     */
    private function paypalCaptureRequest(int $invoiceId, string $captureId, string $amount): array
    {
        $auth    = ['status' => 200, 'body' => json_encode(['access_token' => 'fake-bearer-token'])];
        $capture = ['status' => 200, 'body' => json_encode([
            'purchase_units' => [[
                'payments' => [
                    'captures' => [[
                        'status'     => 'COMPLETED',
                        'invoice_id' => $invoiceId,
                        'id'         => $captureId,
                        'amount'     => ['value' => $amount, 'currency_code' => 'EUR'],
                    ]],
                ],
            ]],
            'id' => 'PAYPAL-ORDER-' . $captureId,
        ])];

        return [
            'method' => 'POST',
            'uri'    => '/guest/gateways/paypal/paypal_capture_payment/ORDER-' . $captureId,
            'env'    => ['PAYPAL_MOCK_RESPONSES' => json_encode([$auth, $capture])],
        ];
    }

    /**
     * A guest-visible invoice with one line item worth $balance, so that
     * Mdl_invoice_amounts::calculate() (run by the payment save) recomputes
     * invoice_total / invoice_balance to real figures instead of zero.
     */
    private function seedPayableInvoice(float $balance): int
    {
        $money     = number_format($balance, 2, '.', '');
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice(
            $clientId,
            ['invoice_status_id' => 2],
            [
                'invoice_total'         => $money,
                'invoice_balance'       => $money,
                'invoice_item_subtotal' => $money,
            ],
        );

        $itemId = $this->databaseInsert('ip_invoice_items', [
            'invoice_id'       => $invoiceId,
            'item_tax_rate_id' => 0,
            'item_date_added'  => date('Y-m-d'),
            'item_name'        => 'Race test item',
            'item_quantity'    => '1.00',
            'item_price'       => $money,
            'item_order'       => 1,
        ]);

        $this->databaseInsert('ip_invoice_item_amounts', [
            'item_id'        => $itemId,
            'item_subtotal'  => $money,
            'item_tax_total' => '0.00',
            'item_discount'  => '0.00',
            'item_total'     => $money,
        ]);

        return $invoiceId;
    }
}
