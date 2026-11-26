<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Regression coverage for a gap found while porting the CWE-694 namespacing fix
 * (GHSA-r59m-wgg6-h4pv) to upstream: before that fix, ip_login_log rows for the
 * per-account lockout were keyed by the raw email address, and rows for the
 * password-reset token counter were keyed by the raw token. Deploying the fix
 * with no migration path means every row written under those pre-fix keys becomes
 * permanently unreachable the moment the namespaced lookup goes live — an account
 * that was actively locked out gets a free reset purely because the key format
 * changed underneath it, not because the lockout window elapsed.
 *
 * Sessions::authenticate() and the token-link branch of Sessions::passwordreset()
 * now migrate a matching legacy-keyed row onto the new namespaced key (preserving
 * log_count and log_create_timestamp) before checking it, the first time that
 * email/token is used after the fix deploys.
 */
#[Group('security')]
class LoginRateLimitLegacyKeyMigrationTest extends AbstractTestCase
{
    private const TOKEN = 'b2c3d4e5f60718293a4b5c6d7e8f901a2b3c4d5e6f708192a3b4c5d6e7f809a1';

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_preserves_an_active_account_lockout_recorded_under_the_pre_fix_raw_email_key(): void
    {
        /* Arrange: a pre-fix row — raw email as login_name, no prefix, no hash — at the
         * lockout threshold and still within the 12-hour window. The user's real password
         * is correct, so if the legacy lockout is lost this login would succeed. */
        $email = 'legacy-lockout@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($email, 10, '-5 minutes');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: still locked out — the correct password must NOT be enough to log in,
         * because the legacy row's count (10) must carry over under the new key. */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $email]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 10]);
    }

    #[Test]
    public function it_migrates_a_below_threshold_legacy_account_row_and_still_allows_login(): void
    {
        /* Arrange: a legacy row that never reached the lockout threshold. */
        $email = 'legacy-below-threshold@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($email, 3, '-5 minutes');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: login succeeds (below threshold), and the success path clears the
         * counter under its namespaced key — proving the row was migrated, not just
         * ignored, before being reset. */
        $this->assertResponseRedirectsToRoute($response, 'dashboard');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $email]);
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email)]);
    }

    #[Test]
    public function it_does_not_touch_a_legacy_row_for_a_different_email(): void
    {
        /* Arrange: a legacy lockout row for someone else entirely. */
        $this->seedLoginLog('someone-else@test.local', 10, '-5 minutes');
        $email = 'unrelated@test.local';
        $this->seedLoginUser($email);

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: unrelated legacy row is left exactly as it was. */
        $this->assertResponseRedirectsToRoute($response, 'dashboard');
        $this->assertDatabaseHas('ip_login_log', ['login_name' => 'someone-else@test.local', 'log_count' => 10]);
    }

    #[Test]
    public function it_preserves_an_active_token_lockout_recorded_under_the_pre_fix_prefixless_key(): void
    {
        /* Arrange: a pre-fix row — the raw token itself as login_name — at the lockout
         * threshold and still within the 12-hour window. */
        $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));
        $this->seedLoginLog(self::TOKEN, 11, '-5 minutes');

        /* Act */
        $response = $this->get('/sessions/passwordreset/' . self::TOKEN);

        /* Assert: still locked out — the token-link flow must redirect away rather than
         * rendering the new-password form, because the legacy row's count (11, over the
         * >10 threshold) must carry over under the new namespaced key. */
        $this->assertResponseRedirectsToRoute($response, 'sessions/passwordreset');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => self::TOKEN]);
        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'password_reset_token:' . hash('sha256', self::TOKEN),
            'log_count'  => 11,
        ]);
    }

    #[Test]
    public function it_refuses_to_migrate_a_forged_email_shaped_like_another_counters_namespaced_key(): void
    {
        /* Arrange: the real per-IP counter already holds 19 attempts, and the attacker's
         * "email" is crafted to literally equal that counter's own namespaced key — the
         * same forgery the namespacing fix itself defends against. A naive migration step
         * that blindly renames "any row matching this literal string" onto the account key
         * would let the attacker steal the IP counter's row instead of starting fresh. */
        $this->seedLoginLog($this->ipKey(), 19, '-5 minutes');
        $forged_email = $this->ipKey();

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $forged_email, 'password' => 'irrelevant']);

        /* Assert: the real IP counter is untouched (still its own row, now 20 from the
         * genuine per-request IP tracking — not renamed away and not inflated to 21), and
         * the forged value gets its own fresh namespaced row instead of inheriting count 19. */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 20]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($forged_email), 'log_count' => 1]);
    }

    #[Test]
    public function it_preserves_the_stronger_lockout_when_both_a_legacy_and_namespaced_row_already_exist(): void
    {
        /*
         * Regression for a CodeRabbit finding on PR #30: a legacy row and a namespaced row
         * for the same account can coexist (e.g. a case-variant email — the namespaced key
         * is case-insensitive, but the legacy lookup is an exact string match, so an earlier
         * request under a different case created a fresh namespaced row without ever
         * finding this exact-case legacy one). The original reconciliation unconditionally
         * kept the namespaced row and discarded the legacy row's count, so an attacker who
         * was already locked out (count 10) could silently reset their own lockout to
         * whatever weaker count (1) happened to exist under the namespaced key, purely by
         * varying the case of the email on a later attempt.
         */

        /* Arrange: the legacy row already has an active, threshold-reached lockout (10);
         * a namespaced row for the same account exists too, but far below the threshold. */
        $email = 'legacy-strong-lockout@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($email, 10, '-5 minutes');
        $this->seedLoginLog($this->accountKey($email), 1, '-1 minute');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: the stronger lockout (10) must win and still block the correct password —
         * not be discarded in favor of the weaker namespaced row's count (1). */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $email]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 10]);
    }

    #[Test]
    public function it_does_not_let_a_weak_legacy_row_overwrite_a_stronger_coexisting_namespaced_lockout(): void
    {
        /*
         * Mutation-check survivor: the sibling case to
         * it_preserves_the_stronger_lockout_when_both_a_legacy_and_namespaced_row_already_exist
         * above, with the magnitudes reversed. That test only proves the comparison's "legacy
         * wins" direction; a mutant that forces the reconciliation to always overwrite with the
         * legacy row's count regardless of which is actually higher survived because nothing
         * exercised "legacy loses." If the legacy row were always applied unconditionally, an
         * attacker with a real active lockout (10, under the namespaced key from normal use)
         * could reset it to a trivially weak count just by causing a stray weak legacy-shaped
         * row to exist for the same email (e.g. a one-off failed attempt recorded before this
         * fix shipped, or any other weak row happening to sit under the exact raw key).
         */

        /* Arrange: the namespaced row already has an active, threshold-reached lockout (10);
         * a legacy row for the same account exists too, but far below the threshold. */
        $email = 'legacy-weak-lockout@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($email, 1, '-1 minute');
        $this->seedLoginLog($this->accountKey($email), 10, '-5 minutes');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: the stronger lockout (10) must survive untouched — not be overwritten by
         * the weaker legacy row's count (1) — and the legacy row is still cleaned up either
         * way. */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $email]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 10]);
    }

    #[Test]
    public function it_refuses_to_migrate_a_forged_email_shaped_like_the_cron_key_counter(): void
    {
        /*
         * Regression for a CodeRabbit finding on PR #30: the namespace-shape guard listed
         * five of the six namespaces that share ip_login_log.login_name but omitted
         * cron_key: (Cron::recur()'s per-IP throttle). A login submission forged to equal
         * that counter's own namespaced key would not be rejected by the guard and could be
         * migrated or clobbered through this path, letting an attacker reset or hijack
         * another IP's cron throttle - the same class of forgery GHSA-r59m-wgg6-h4pv covers
         * for the other five counters.
         */

        /* Arrange: a real cron_key counter already at 7 attempts, and the attacker's
         * "email" crafted to literally equal that counter's own namespaced key. */
        $this->seedLoginLog($this->cronKey(), 7, '-5 minutes');
        $forged_email = $this->cronKey();

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $forged_email, 'password' => 'irrelevant']);

        /* Assert: the real cron_key counter is untouched (still 7, not renamed away), and
         * the forged value gets its own fresh namespaced row instead of inheriting count 7. */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->cronKey(), 'log_count' => 7]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($forged_email), 'log_count' => 1]);
    }

    #[Test]
    public function it_clears_a_legacy_account_lockout_when_the_password_is_changed_before_the_next_login(): void
    {
        /* Arrange: a pre-fix lockout row under the raw email, still active, and a valid reset token.
         * No login has happened since the upgrade, so the row has not been migrated yet. */
        $userId = $this->seedUserWithResetToken(gmdate('Y-m-d H:i:s', time() + 600));
        $email  = (string) $this->databaseFetchOne('ip_users', ['user_id' => $userId])['user_email'];
        $this->seedLoginLog($email, 10, '-5 minutes');

        /* Act */
        $this->post('/sessions/passwordreset', [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => self::TOKEN,
            'new_password'     => 'BrandNewPass123!',
            'new_passwordv'    => 'BrandNewPass123!',
        ]);

        /* Assert: the reset removed the lockout under both key formats, so the new password logs in */
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $email]);
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email)]);
        $login = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'BrandNewPass123!']);
        $this->assertResponseRedirectsToRoute($login, 'dashboard');
    }

    private function ipKey(): string
    {
        return 'login_ip:' . hash('sha256', '127.0.0.1');
    }

    private function cronKey(): string
    {
        return 'cron_key:' . hash('sha256', '127.0.0.1');
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

    private function seedLoginUser(string $email): void
    {
        $this->databaseInsert('ip_users', [
            'user_name'         => 'Legacy Key Tester', 'user_password' => password_hash('correct-password', PASSWORD_BCRYPT),
            'user_psalt'        => bin2hex(random_bytes(10)), 'user_email' => $email, 'user_type' => 1, 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * @param string|null $expiry UTC 'Y-m-d H:i:s', or null for a legacy token with no expiry
     */
    private function seedUserWithResetToken(?string $expiry): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'                       => 'legacytoken_' . bin2hex(random_bytes(3)),
            'user_email'                      => 'legacytoken+' . bin2hex(random_bytes(3)) . '@example.com',
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
