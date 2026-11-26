<?php

namespace Tests\Unit\Libraries;

use LogicException;
use PaymentCallbackLock;
use PDO;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;
use Tests\Concerns\UsesCodeIgniterModels;

/**
 * GHSA-c2qp-26qj-6gf6 / GHSA-m2c5-pmxf-qfh5: PaymentCallbackLock serializes concurrent payment
 * writers with a connection-scoped MySQL advisory lock. The existing feature-level race tests
 * (AdminPaymentAmountRaceTest, GatewayPaymentRaceTest) prove the application-level behavior —
 * that a second request blocks until the first one finishes — but every one of them dispatches
 * each request as its own subprocess, so the lock is also released implicitly the instant that
 * process exits (MySQL frees a session's advisory locks when its connection closes), whether or
 * not release() itself actually runs. A mutation-check of this class found exactly that gap:
 * deleting the RELEASE_LOCK() call, or short-circuiting release() to always/never return early,
 * survived every feature test unscathed.
 *
 * In a real deployment with a persistent/pooled DB connection (persistent connections, or a
 * long-lived worker reusing one), a release() that silently never calls RELEASE_LOCK() would
 * leave that connection holding the lock for its entire lifetime, starving every future payment
 * on that connection — a real, latent robustness bug no subprocess-per-request test can see.
 * These tests call acquire()/release() directly against one real CI database connection and
 * verify the lock's true state from a second, independent connection, closing that gap.
 */
final class PaymentCallbackLockTest extends AbstractTestCase
{
    use UsesCodeIgniterModels;

    protected function setUp(): void
    {
        parent::setUp();
        require_once APPPATH . 'libraries/PaymentCallbackLock.php';
    }

    protected function tearDown(): void
    {
        $this->tearDownCodeIgniter();
        parent::tearDown();
    }

    #[Test]
    public function it_actually_releases_the_mysql_lock_so_a_second_connection_can_acquire_it_immediately(): void
    {
        /* Arrange */
        $ci         = $this->bootCodeIgniter();
        $invoiceId  = random_int(100000, 999999);
        $lock       = new PaymentCallbackLock($ci->db);
        $lockHolder = $this->openSecondaryConnection();

        /* Act */
        self::assertTrue($lock->acquire($invoiceId, 5), 'Setup: the lock must be acquirable before release() is even tested.');
        $lock->release();

        /* Assert: a second, independent connection must be able to take the exact same named
         * lock without waiting at all (timeout 0) -- if release() were a no-op (the surviving
         * mutant), GET_LOCK would still be held by $ci->db's own connection and this would
         * come back 0, not 1. */
        $probe = $lockHolder->prepare('SELECT GET_LOCK(?, 0) AS acquired');
        $probe->execute(['ip:payment:invoice:' . $invoiceId]);
        self::assertSame(1, (int) $probe->fetch(PDO::FETCH_ASSOC)['acquired'], 'The lock was still held after release() returned -- release() did not actually run RELEASE_LOCK().');

        $release = $lockHolder->prepare('SELECT RELEASE_LOCK(?)');
        $release->execute(['ip:payment:invoice:' . $invoiceId]);
    }

    #[Test]
    public function it_is_a_safe_no_op_to_release_a_lock_that_was_never_acquired(): void
    {
        /* Arrange: a fresh instance that never called acquire() */
        $ci   = $this->bootCodeIgniter();
        $lock = new PaymentCallbackLock($ci->db);

        /* Act */
        $lock->release();

        /* Assert: no exception, and a second call is equally safe (idempotent) */
        $lock->release();
        $fake = $this->recordingDatabase([]);
        (new PaymentCallbackLock($fake))->release();
        self::assertSame([], $fake->queries, 'Releasing a lock that was never acquired must not talk to the database.');
    }

    #[Test]
    public function it_asks_for_a_named_per_invoice_lock_with_a_ten_second_default_wait(): void
    {
        /* Arrange */
        $fake = $this->recordingDatabase([['acquired' => 1], ['acquired' => 1]]);

        /* Act */
        $default  = new PaymentCallbackLock($fake);
        $explicit = new PaymentCallbackLock($fake);
        $default->acquire(42);
        $explicit->acquire(43, 3);

        /* Assert */
        self::assertSame(['ip:payment:invoice:42', 10], $fake->queries[0][1]);
        self::assertSame(['ip:payment:invoice:43', 3], $fake->queries[1][1]);
    }

    #[Test]
    public function it_returns_false_and_holds_nothing_when_another_connection_owns_the_lock(): void
    {
        /* Arrange: GET_LOCK() answers 0 for a timeout */
        $fake = $this->recordingDatabase([['acquired' => 0]]);
        $lock = new PaymentCallbackLock($fake);

        /* Act */
        $acquired = $lock->acquire(7, 0);
        $lock->release();

        /* Assert: no RELEASE_LOCK for a lock this instance never owned */
        self::assertFalse($acquired);
        self::assertCount(1, $fake->queries);
        self::assertStringContainsString('GET_LOCK', $fake->queries[0][0]);
    }

    #[Test]
    public function it_treats_a_null_get_lock_answer_as_a_server_error_not_a_timeout(): void
    {
        /* Arrange */
        $fake = $this->recordingDatabase([['acquired' => null]]);
        $lock = new PaymentCallbackLock($fake);

        /* Act + Assert */
        try {
            $lock->acquire(7);
            self::fail('A NULL from GET_LOCK() must raise, not return false.');
        } catch (RuntimeException $e) {
            self::assertStringContainsString('ip:payment:invoice:7', $e->getMessage());
        }
        $lock->release();
        self::assertCount(1, $fake->queries, 'A failed acquisition owns no lock, so there is nothing to release.');
    }

    #[Test]
    public function it_releases_the_lock_when_the_instance_is_destroyed_without_an_explicit_release(): void
    {
        /* Arrange */
        $fake = $this->recordingDatabase([['acquired' => 1]]);
        $lock = new PaymentCallbackLock($fake);
        $lock->acquire(9);

        /* Act */
        unset($lock);

        /* Assert */
        self::assertCount(2, $fake->queries);
        self::assertSame('SELECT RELEASE_LOCK(?)', $fake->queries[1][0]);
        self::assertSame(['ip:payment:invoice:9'], $fake->queries[1][1]);
    }

    #[Test]
    public function it_can_acquire_again_after_release_and_releases_each_lock_exactly_once(): void
    {
        /* Arrange */
        $fake = $this->recordingDatabase([['acquired' => 1], ['acquired' => 1]]);
        $lock = new PaymentCallbackLock($fake);

        /* Act */
        $lock->acquire(1);
        $lock->release();
        $lock->release();
        $again = $lock->acquire(2);

        /* Assert: the second release() was a no-op, and the instance was reusable (name cleared) */
        self::assertTrue($again);
        $sql = array_column($fake->queries, 0);
        self::assertSame(1, count(array_filter($sql, static fn (string $q): bool => str_contains($q, 'RELEASE_LOCK'))));
    }

    #[Test]
    public function it_swallows_a_database_error_while_releasing_and_still_forgets_the_lock(): void
    {
        /* Arrange: the connection dies between acquire and release */
        $fake = $this->recordingDatabase([['acquired' => 1], ['acquired' => 1]], throwOnRelease: true);
        $lock = new PaymentCallbackLock($fake);
        $lock->acquire(1);

        /* Act */
        $lock->release();
        $again = $lock->acquire(2);

        /* Assert: no exception escaped, and the instance is not stuck "already held" */
        self::assertTrue($again);
    }

    #[Test]
    public function it_refuses_to_acquire_the_same_lock_instance_twice_in_a_row(): void
    {
        /* Arrange */
        $ci        = $this->bootCodeIgniter();
        $invoiceId = random_int(100000, 999999);
        $lock      = new PaymentCallbackLock($ci->db);
        self::assertTrue($lock->acquire($invoiceId, 5));

        /* Act + Assert: acquiring again on the same instance before releasing is a programmer
         * error (LogicException), not a silent no-op -- this one instance tracks exactly one
         * held lock name. */
        $this->expectException(LogicException::class);
        try {
            $lock->acquire($invoiceId, 5);
        } finally {
            $lock->release();
        }
    }

    /** @param list<array<string, mixed>> $answers one row per GET_LOCK() call */
    private function recordingDatabase(array $answers, bool $throwOnRelease = false): object
    {
        return new class ($answers, $throwOnRelease) {
            /** @var list<array{0: string, 1: array}> */
            public array $queries = [];

            public function __construct(private array $answers, private bool $throwOnRelease) {}

            public function query(string $sql, array $binds = []): object
            {
                $this->queries[] = [$sql, $binds];
                if (str_contains($sql, 'RELEASE_LOCK')) {
                    if ($this->throwOnRelease) {
                        throw new RuntimeException('connection lost');
                    }

                    return new class () {
                        public function row_array(): array
                        {
                            return [];
                        }
                    };
                }
                $row = array_shift($this->answers);

                return new class ($row) {
                    public function __construct(private ?array $row) {}

                    public function row_array(): array
                    {
                        return $this->row ?? [];
                    }
                };
            }
        };
    }

    private function openSecondaryConnection(): PDO
    {
        $basePath = dirname(__DIR__, 3);

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
}
