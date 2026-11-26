<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression coverage for CWE-694 (GHSA-r59m-wgg6-h4pv): ip_login_log.login_name is a single
 * shared string column backing five different rate-limit counters (per-account login lockout,
 * per-IP login throttle, per-IP and per-email password-reset throttles, and the password-reset
 * token counter). Before the fix, the per-account counter was keyed by the raw, unhashed email
 * address with no prefix, so an attacker could choose an email address equal to another
 * counter's literal key and read or mutate that unrelated counter through the shared column.
 *
 * Each counter now uses a distinct '<prefix>:sha256(...)' key shape, so no user-supplied email
 * or token can ever collide with a different counter's key regardless of its content.
 */
#[Group('security')]
class LoginRateLimitNamespacingTest extends AbstractTestCase
{
    private const TOKEN = 'a1b2c3d4e5f60718293a4b5c6d7e8f901a2b3c4d5e6f708192a3b4c5d6e7f809';

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_does_not_let_a_forged_email_collide_with_the_ip_rate_limit_counter(): void
    {
        /* Arrange: the real per-IP counter already holds 19 attempts (one below the
         * 20-attempt threshold), and the attacker's "email" is crafted to literally equal
         * that counter's own key string. */
        $this->seedLoginLog($this->ipKey(), 19, 'now');
        $forged_email = $this->ipKey();

        /* Act: one failed login attempt from the forged email. */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $forged_email, 'password' => 'irrelevant']);

        /* Assert: a real failed login always adds exactly one attempt to the genuine IP
         * counter (127.0.0.1 is the only IP the test harness can use), so it must now read
         * 20 — not 21. A pre-fix, unprefixed account key would equal the IP counter's own
         * key, so the account-lockout bookkeeping would land on the SAME row as the IP
         * counter (which this request also updates), pushing it to 21 instead of 20. The
         * namespaced account key must instead create its OWN separate row. */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 20]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($forged_email), 'log_count' => 1]);
    }

    #[Test]
    public function it_rejects_an_empty_email_without_touching_any_login_rate_limit_counter(): void
    {
        /*
         * An empty identity has no account to lock out: counting it would key a real,
         * namespaced lockout row on 'login_account:' . hash(''), reachable by any
         * submission that simply omits the field. authenticate() must reject it before
         * touching either the per-account or per-IP counter at all.
         */

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => '', 'password' => 'irrelevant']);

        /* Assert */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey('')]);
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->ipKey()]);
    }

    #[Test]
    public function it_rejects_an_array_valued_email_without_crashing_or_touching_any_counter(): void
    {
        /*
         * A crafted `email[]=a&email[]=b` POST field reaches authenticate() as an array, not
         * a string. The empty/null guard (=== '' || === null) does not catch an array, so it
         * fell through to _login_account_log_key(string $email_address), whose type hint
         * raised an uncaught TypeError instead of a graceful login failure. Reject any
         * non-string identity up front, same as an empty one.
         */

        /* Arrange */
        $countBefore = $this->databaseCount('ip_login_log');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => ['a', 'b'], 'password' => 'irrelevant']);

        /* Assert */
        self::assertTrue($response->isRedirect());
        self::assertSame($countBefore, $this->databaseCount('ip_login_log'));
    }

    #[Test]
    public function it_clears_the_namespaced_account_lockout_counter_after_a_successful_password_change(): void
    {
        /* Arrange: the account already has failed login attempts recorded under its
         * namespaced key, plus a valid, unexpired password-reset token. */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));
        $user   = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        $this->seedLoginLog($this->accountKey($user['user_email']), 5, 'now');

        /* Act */
        $response = $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'BrandNewPass123!',
            'new_passwordv'    => 'BrandNewPass123!',
        ]);

        /* Assert: a successful password change must clear the SAME namespaced key that
         * failed logins accumulate under. Regression for a bug where the reset call used
         * the user's raw email as the key instead of the namespaced account key, so the
         * delete silently targeted a different, nonexistent row and the real lockout
         * counter was never actually cleared. */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($user['user_email'])]);
    }

    #[Test]
    public function it_namespaces_the_password_reset_token_counter_under_its_own_digest_key(): void
    {
        /* Arrange: no user holds this token, so the attempt is counted as a failure and the
         * counter row survives for inspection (a valid token's own successful visit clears
         * its counter — see _login_log_reset() in Sessions::passwordreset()). */
        $unknown_token = 'ff00ff00ff00ff00ff00ff00ff00ff00ff00ff00ff00ff00ff00ff00ff00ff00';

        /* Act */
        $this->get('/sessions/passwordreset/' . $unknown_token);

        /* Assert: the counter is keyed by the token's own namespaced digest — never the raw
         * token value, which would otherwise sit in ip_login_log in the clear. */
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $unknown_token]);
        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'password_reset_token:' . hash('sha256', $unknown_token),
            'log_count'  => 1,
        ]);
    }

    #[Test]
    public function it_locks_out_the_password_reset_token_after_more_than_ten_attempts(): void
    {
        /* Arrange: the namespaced token counter already holds 11 attempts — one over the
         * lockout threshold. */
        $token    = 'aa00aa00aa00aa00aa00aa00aa00aa00aa00aa00aa00aa00aa00aa00aa00aa00';
        $tokenKey = 'password_reset_token:' . hash('sha256', $token);
        $this->seedLoginLog($tokenKey, 11, 'now');

        /* Act */
        $this->get('/sessions/passwordreset/' . $token);

        /* Assert: the lockout branch redirects away without touching the counter further —
         * if it were taken as a normal (uncounted) attempt instead, log_count would climb
         * to 12. */
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $tokenKey, 'log_count' => 11]);
    }

    #[Test]
    public function it_still_counts_the_attempt_at_exactly_ten_prior_failures(): void
    {
        /*
         * Boundary for the `> 10` lockout check: exactly 10 prior failures must NOT be
         * locked out yet — the 11th attempt is still counted normally, taking log_count to
         * 11. (A `>= 10` mutant would incorrectly lock this out and leave it at 10.)
         */

        /* Arrange */
        $token    = 'bb00bb00bb00bb00bb00bb00bb00bb00bb00bb00bb00bb00bb00bb00bb00bb00';
        $tokenKey = 'password_reset_token:' . hash('sha256', $token);
        $this->seedLoginLog($tokenKey, 10, 'now');

        /* Act */
        $this->get('/sessions/passwordreset/' . $token);

        /* Assert */
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $tokenKey, 'log_count' => 11]);
    }

    #[Test]
    public function it_resets_the_namespaced_token_counter_after_a_valid_token_link_is_opened(): void
    {
        /*
         * A valid, unexpired token visited via the emailed link must clear its own
         * namespaced counter (_login_log_reset() in the token-link branch), not just the
         * account counter cleared on an actual password change.
         */

        /* Arrange */
        $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));
        $tokenKey = $this->tokenKey(self::TOKEN);
        $this->seedLoginLog($tokenKey, 3, 'now');

        /* Act */
        $this->get('/sessions/passwordreset/' . self::TOKEN);

        /* Assert */
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $tokenKey]);
    }

    private function tokenKey(string $token): string
    {
        return 'password_reset_token:' . hash('sha256', $token);
    }

    private function ipKey(): string
    {
        return 'login_ip:' . hash('sha256', '127.0.0.1');
    }

    private function accountKey(string $email): string
    {
        return 'login_account:' . hash('sha256', mb_strtolower($email));
    }

    private function seedLoginLog(string $key, int $count, string $when): void
    {
        $this->databaseInsert('ip_login_log', [
            'login_name' => $key, 'log_count' => $count, 'log_create_timestamp' => date('Y-m-d H:i:s', strtotime($when)),
        ]);
    }

    /**
     * @param string|null $expiry UTC 'Y-m-d H:i:s', or null for a legacy token with no expiry
     */
    private function seedUserWithResetToken(?string $expiry): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'                       => 'namespacing_' . bin2hex(random_bytes(3)),
            'user_email'                      => 'namespacing+' . bin2hex(random_bytes(3)) . '@example.com',
            'user_password'                   => password_hash('OriginalPass123!', PASSWORD_DEFAULT),
            'user_psalt'                      => bin2hex(random_bytes(10)),
            'user_type'                       => 1,
            'user_active'                     => 1,
            'user_passwordreset_token'        => hash('sha256', self::TOKEN),
            'user_passwordreset_token_expiry' => $expiry,
            'user_date_created'               => date('Y-m-d H:i:s'),
            'user_date_modified'              => date('Y-m-d H:i:s'),
        ]);
    }
}
