<?php

namespace Tests\Feature\Payments;

use PDO;
use RuntimeException;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * TOCTOU race in the admin payment form/ajax endpoints (CWE-362/367).
 *
 * Mdl_Payments::validate_payment_amount() reads invoice_balance, checks it,
 * and returns pass/fail; the controller then calls Mdl_Payments::save(),
 * which inserts the payment row, as a separate non-atomic step. Two
 * concurrent submissions from distinct authenticated sessions can both read
 * the same stale balance and both pass validate_payment_amount() before
 * either commits, recording more payments than the invoice balance allows
 * and driving invoice_balance negative.
 *
 * The fix serializes concurrent writers on the same invoice with
 * PaymentCallbackLock — the same connection-scoped MySQL advisory
 * (GET_LOCK/RELEASE_LOCK) lock already used to close the equivalent
 * gateway-callback race — so a second submission blocks until the first
 * commits, then re-reads the now-current balance.
 *
 * Rather than hoping two independently-scheduled OS processes happen to
 * interleave inside the few-millisecond validate+save window (unreliable:
 * confirmed empirically that even the unpatched code rarely loses that race
 * under this harness's process-spawn overhead), these tests hold the named
 * lock from a second, directly-opened database connection *before* the real
 * HTTP request is dispatched. This makes the ordering deterministic: if the
 * controller calls PaymentCallbackLock::acquire() first (the fix), its
 * request cannot complete before the test releases the lock, which is
 * asserted directly from wall-clock elapsed time — a signal that does not
 * depend on winning a timing race, so it reliably fails on the unprotected
 * (pre-fix) code and reliably passes once the lock is in place.
 */
class AdminPaymentAmountRaceTest extends AbstractTestCase
{
    private const LOCK_HOLD_SECONDS = 0.5;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_blocks_a_concurrent_admin_payment_until_a_racing_submission_releases_the_invoice_lock(): void
    {
        /* Arrange: a 500.00 invoice, and a second connection holding the exact
         * named lock PaymentCallbackLock uses, simulating a concurrent admin
         * session that is mid-save on the same invoice. */
        $invoiceId   = $this->seedPayableInvoice(500.00);
        $lockName    = 'ip:payment:invoice:' . $invoiceId;
        $lockHolder  = $this->openSecondaryConnection();

        $acquired = $lockHolder->prepare('SELECT GET_LOCK(?, ?) AS acquired');
        $acquired->execute([$lockName, 5]);
        self::assertSame(1, (int) $acquired->fetch(PDO::FETCH_ASSOC)['acquired'], 'Test setup could not acquire the named lock.');

        /* Act: dispatch the real admin ajax request without waiting for it */
        $handle = $this->startAsyncAjaxPost('/payments/ajax/add', [
            'invoice_id'     => (string) $invoiceId,
            'payment_date'   => date('Y-m-d'),
            'payment_amount' => '400.00',
        ]);

        // While the request is (if fixed) blocked waiting on the lock, perform the
        // concurrent payment that the real race would have committed first: record
        // its own 400.00 payment and bring the balance down to 100.00, exactly as
        // Mdl_Payments::save() + Mdl_invoice_amounts::calculate() would.
        usleep((int) (self::LOCK_HOLD_SECONDS * 1_000_000));

        /* Assert: Error Semantics (C) — the request must still be waiting on the
         * lock at this point, not have already run unprotected to completion.
         * This is checked with a non-blocking process-status poll, not elapsed
         * wall-clock time (which would also pass a 0.5s sleep performed here
         * regardless of whether the subprocess actually waited for anything) —
         * so it reliably distinguishes the fix from the pre-fix code. */
        $status = proc_get_status($handle['proc']);
        self::assertTrue(
            $status['running'],
            'The admin payment request already completed before the racing submission released the invoice '
            . 'lock — it is not serializing on PaymentCallbackLock and is vulnerable to the balance-overrun race.'
        );

        $lockHolder->exec(sprintf(
            'INSERT INTO ip_payments (invoice_id, payment_date, payment_amount, payment_method_id) VALUES (%d, CURDATE(), 400.00, 1)',
            $invoiceId
        ));
        $lockHolder->exec(sprintf(
            'UPDATE ip_invoice_amounts SET invoice_paid = 400.00, invoice_balance = 100.00 WHERE invoice_id = %d',
            $invoiceId
        ));

        $release = $lockHolder->prepare('SELECT RELEASE_LOCK(?)');
        $release->execute([$lockName]);

        $body = $this->finishAsyncRequest($handle);

        /* Assert: Business Logic (A) — once unblocked, it must re-validate against
         * the now-current balance (100.00) and reject the 400.00 as oversized. */
        $json = json_decode($body, true);
        self::assertSame(0, $json['success'] ?? null, 'Body: ' . $body);

        /* Assert: Data Integrity (D) — only the racing payment landed, balance never went negative */
        $this->resetDatabaseConnection();
        $this->assertDatabaseCount('ip_payments', 1, ['invoice_id' => $invoiceId]);
        $amounts = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertEqualsWithDelta(100.00, (float) $amounts['invoice_balance'], 0.001);
        self::assertGreaterThanOrEqual(-0.001, (float) $amounts['invoice_balance']);
    }

    #[Test]
    public function it_blocks_a_concurrent_admin_form_payment_until_a_racing_submission_releases_the_invoice_lock(): void
    {
        /* Arrange: same setup, but through the non-ajax Payments::form() entry point */
        $invoiceId  = $this->seedPayableInvoice(500.00);
        $lockName   = 'ip:payment:invoice:' . $invoiceId;
        $lockHolder = $this->openSecondaryConnection();

        $acquired = $lockHolder->prepare('SELECT GET_LOCK(?, ?) AS acquired');
        $acquired->execute([$lockName, 5]);
        self::assertSame(1, (int) $acquired->fetch(PDO::FETCH_ASSOC)['acquired'], 'Test setup could not acquire the named lock.');

        /* Act: dispatch the real form submission without waiting for it */
        $handle = $this->startAsyncPost('/payments/form', [
            'invoice_id'     => (string) $invoiceId,
            'payment_date'   => date('Y-m-d'),
            'payment_amount' => '400.00',
            'btn_submit'     => '1',
        ]);

        usleep((int) (self::LOCK_HOLD_SECONDS * 1_000_000));

        /* Assert: the request must still be waiting on the lock at this point —
         * same non-blocking process-status discriminator as the ajax test. */
        $status = proc_get_status($handle['proc']);
        self::assertTrue(
            $status['running'],
            'The admin form payment request already completed before the racing submission released the '
            . 'invoice lock — Payments::form() is not serializing on PaymentCallbackLock.'
        );

        $lockHolder->exec(sprintf(
            'INSERT INTO ip_payments (invoice_id, payment_date, payment_amount, payment_method_id) VALUES (%d, CURDATE(), 400.00, 1)',
            $invoiceId
        ));
        $lockHolder->exec(sprintf(
            'UPDATE ip_invoice_amounts SET invoice_paid = 400.00, invoice_balance = 100.00 WHERE invoice_id = %d',
            $invoiceId
        ));

        $release = $lockHolder->prepare('SELECT RELEASE_LOCK(?)');
        $release->execute([$lockName]);

        $result = $this->finishAsyncRequestFull($handle);

        /* Assert: once unblocked, it must re-validate against the now-current
         * balance (100.00) and reject the 400.00 — i.e. NOT redirect to the
         * payments list the way a successful save does. */
        $redirected = array_filter($result['headers'], static fn ($h) => stripos((string) $h, 'Location:') === 0);
        self::assertSame([], array_values($redirected), 'The form submission redirected as if it had succeeded, despite the balance no longer covering the payment.');

        /* Assert: Data Integrity — only the racing payment landed, balance never went negative */
        $this->resetDatabaseConnection();
        $this->assertDatabaseCount('ip_payments', 1, ['invoice_id' => $invoiceId]);
        $amounts = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertEqualsWithDelta(100.00, (float) $amounts['invoice_balance'], 0.001);
        self::assertGreaterThanOrEqual(-0.001, (float) $amounts['invoice_balance']);
    }

    #[Test]
    public function it_still_allows_a_single_legitimate_payment_to_succeed(): void
    {
        /* Arrange */
        $invoiceId = $this->seedPayableInvoice(500.00);

        /* Act: one legitimate payment within the balance, no contention */
        $response = $this->ajax('POST', '/payments/ajax/add', [
            'invoice_id'     => (string) $invoiceId,
            'payment_date'   => date('Y-m-d'),
            'payment_amount' => '400.00',
        ]);

        /* Assert: Business Logic (A) */
        $json = json_decode($response->body(), true);
        $this->assertSame(1, $json['success'] ?? null, 'Body: ' . $response->body());

        /* Assert: Data Integrity (D) */
        $this->assertDatabaseHas('ip_payments', ['invoice_id' => $invoiceId, 'payment_amount' => '400.00']);
        $amounts = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoiceId]);
        self::assertEqualsWithDelta(100.00, (float) $amounts['invoice_balance'], 0.001);
    }

    /**
     * A real line item behind the balance, so Mdl_invoice_amounts::calculate()
     * (run by every payment save) recomputes invoice_total/invoice_balance to
     * a real figure instead of collapsing to zero.
     */
    protected function seedPayableInvoice(float $balance): int
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

    // -------------------------------------------------------------------------

    /**
     * A brand-new PDO connection, distinct from the cached test connection:
     * MySQL's GET_LOCK/RELEASE_LOCK are scoped per connection, so simulating a
     * concurrent session requires a genuinely separate connection.
     */
    private function openSecondaryConnection(): PDO
    {
        $basePath = dirname(__DIR__, 3);
        require_once $basePath . '/bootstrap/kernel.php';

        $active_group = null;
        $db           = [];
        require $basePath . '/application/config/database.php';

        $cfg = $db[$active_group ?? 'default'] ?? [];

        return new PDO(
            sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=utf8',
                (string) ($cfg['hostname'] ?? '127.0.0.1'),
                (int) ($cfg['port'] ?? 3306),
                (string) ($cfg['database'] ?? '')
            ),
            (string) ($cfg['username'] ?? ''),
            (string) ($cfg['password'] ?? ''),
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{proc: resource, pipes: array<int, resource>}
     */
    private function startAsyncAjaxPost(string $uri, array $data): array
    {
        return $this->startAsyncPost($uri, $data, true);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return array{proc: resource, pipes: array<int, resource>}
     */
    private function startAsyncPost(string $uri, array $data, bool $ajax = false): array
    {
        $this->actingAsAdmin();
        $this->resetDatabaseConnection();

        $payload = [
            'method'  => 'POST',
            'uri'     => $uri,
            'query'   => [],
            'post'    => $data,
            'cookies' => [],
            'session' => $this->sessionData,
            'env'     => [],
            'ajax'    => $ajax,
        ];

        $command      = sprintf('php %s', escapeshellarg(dirname(__DIR__, 2) . '/Integration/bin/request.php'));
        $inheritedEnv = getenv();
        foreach (['DB_HOSTNAME', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD', 'DB_DRIVER'] as $dbEnvKey) {
            unset($inheritedEnv[$dbEnvKey]);
        }
        $env = array_merge($inheritedEnv, [
            'CI_TEST_REQUEST' => base64_encode((string) json_encode($payload, JSON_THROW_ON_ERROR)),
        ]);

        $proc = proc_open(
            $command,
            [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
            $pipes,
            dirname(__DIR__, 3),
            $env,
        );

        if ( ! is_resource($proc)) {
            throw new RuntimeException('Unable to start async request process.');
        }

        fclose($pipes[0]);

        return ['proc' => $proc, 'pipes' => $pipes];
    }

    /**
     * @param array{proc: resource, pipes: array<int, resource>} $handle
     */
    private function finishAsyncRequest(array $handle): string
    {
        return $this->finishAsyncRequestFull($handle)['body'];
    }

    /**
     * @param array{proc: resource, pipes: array<int, resource>} $handle
     *
     * @return array{body: string, status: int, headers: array<int, string>}
     */
    private function finishAsyncRequestFull(array $handle): array
    {
        $stdout = stream_get_contents($handle['pipes'][1]);
        fclose($handle['pipes'][1]);
        stream_get_contents($handle['pipes'][2]);
        fclose($handle['pipes'][2]);
        proc_close($handle['proc']);

        $this->resetDatabaseConnection();

        preg_match('/__CI_TEST_RESULT_START__(.*?)__CI_TEST_RESULT_END__/s', $stdout ?: '', $matches);
        if ( ! isset($matches[1])) {
            throw new RuntimeException('Async request produced no CI result envelope. STDOUT: ' . $stdout);
        }

        $result = json_decode(base64_decode($matches[1], true), true, 512, JSON_THROW_ON_ERROR);

        return [
            'body'    => base64_decode((string) ($result['output'] ?? ''), true) ?: '',
            'status'  => (int) ($result['status'] ?? 200),
            'headers' => $result['headers'] ?? [],
        ];
    }
}
