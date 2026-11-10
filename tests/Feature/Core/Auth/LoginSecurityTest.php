<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Feature tests for login security hardening.
 *
 * Covered:
 *  - Error consolidation: unknown email, wrong password, and inactive account
 *    all produce the same generic redirect response (prevents enumeration)
 *  - IP-based rate limiting: exceeding the threshold blocks further attempts
 *
 * Note on assertion strategy: the test subprocess runs in PHP CLI where
 * headers_list() always returns []. We cannot read flash messages or Location
 * headers directly. We assert on observable HTTP behaviour (redirect vs. 200)
 * and on the absence of privileged content in the response body.
 *
 * GHSA-9452-64v7-9g2p: Username/email enumeration via login timing side-channel
 */
#[Group('security')]
class LoginSecurityTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    // -------------------------------------------------------------------------
    // Error consolidation — all failure paths must look identical to the caller
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_after_a_login_attempt_with_an_unknown_email(): void
    {
        /* Arrange */
        $email = 'nobody@does-not-exist.example';

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'irrelevant-password']);

        /* Assert: redirected, and the failure was counted against both the account key and the IP */
        self::assertTrue($response->isRedirect(), 'An unknown email must redirect. Got: ' . $response->statusCode());
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 1]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 1]);
    }

    #[Test]
    public function it_redirects_after_a_login_attempt_with_a_wrong_password(): void
    {
        /* Arrange */
        $email = 'loginsec@test.local';
        $this->seedLoginUser($email);

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'wrong-password']);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A wrong password must redirect. Got: ' . $response->statusCode());
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 1]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 1]);
    }

    #[Test]
    public function it_does_not_reveal_whether_an_email_exists_in_error_responses(): void
    {
        /* Arrange */
        $this->seedLoginUser('known@test.local');

        /* Act */
        $unknown = $this->post('/sessions/login', ['btn_login' => '1', 'email' => 'ghost@no-account.example', 'password' => 'password123']);
        $wrong   = $this->post('/sessions/login', ['btn_login' => '1', 'email' => 'known@test.local', 'password' => 'definitely-wrong']);

        /* Assert: identical status, identical observable side effects */
        self::assertTrue($unknown->isRedirect());
        self::assertTrue($wrong->isRedirect());
        self::assertSame($unknown->statusCode(), $wrong->statusCode(), 'Unknown-email and wrong-password failures must return identical status codes.');
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey('ghost@no-account.example'), 'log_count' => 1]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey('known@test.local'), 'log_count' => 1]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 2]);
    }

    #[Test]
    public function it_does_not_expose_dashboard_content_after_a_failed_login(): void
    {
        /* Arrange */
        $payload = [
            'btn_login' => '1',
            'email'     => 'nobody@no-account.example',
            'password'  => 'wrong',
        ];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert */
        self::assertFalse(
            $response->contains('dashboard'),
            'A failed login response body must not contain dashboard content.'
        );
        self::assertFalse(
            $response->contains('invoice'),
            'A failed login response body must not contain application content.'
        );
    }

    // -------------------------------------------------------------------------
    // Inactive-account lockout — correct credentials must not authenticate
    // a deactivated user (Mdl_sessions::auth() user_active check).
    // -------------------------------------------------------------------------

    #[Test]
    public function it_denies_login_for_an_inactive_user_even_with_the_correct_password(): void
    {
        /* Arrange */
        $email = 'inactive-' . bin2hex(random_bytes(4)) . '@test.local';
        $this->databaseInsert('ip_users', [
            'user_name'          => 'Inactive User',
            'user_password'      => password_hash('correct-password', PASSWORD_BCRYPT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => $email,
            'user_type'          => 1,
            'user_active'        => 0,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $payload = [
            'btn_login' => '1',
            'email'     => $email,
            'password'  => 'correct-password',
        ];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'A login attempt for an inactive user must redirect, not authenticate. Got: ' . $response->statusCode()
        );
        // Mdl_sessions::auth() must return false for an inactive account, which routes
        // through Sessions::authenticate()'s failure branch and records a login-log
        // failure entry. If the user_active check were removed, auth() would return
        // true on the correct password, the success branch would run instead, and
        // this row would never be written — making this assertion a real regression
        // guard for the auth-bypass fix rather than a redirect-only smoke test.
        $this->assertDatabaseHas('ip_login_log', [
            'login_name' => 'login_account:' . hash('sha256', mb_strtolower($email)),
            'log_count'  => 1,
        ]);
    }

    #[Test]
    public function it_allows_login_for_an_active_user_with_the_correct_password(): void
    {
        /* Arrange: earlier failures exist for both the account and the IP */
        $email = 'activeuser@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($this->accountKey($email), 3, 'now');
        $this->seedLoginLog($this->ipKey(), 3, 'now');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: redirected all the way to the dashboard — not bounced back to the login
         * form, which authenticate() returning false (despite a correct password) would
         * also do while still clearing both counters, since the resets happen before the
         * return statement either way. */
        $this->assertResponseRedirectsToRoute($response, 'dashboard');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email)]);
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->ipKey()]);
    }

    #[Test]
    public function it_denies_login_with_the_correct_password_once_the_account_lockout_threshold_is_reached(): void
    {
        /*
         * Boundary for the account lockout's `< 10` check: at exactly 10 prior failures,
         * the whole credential-check block is skipped — even a correct password must not
         * log the user in, and the skipped block means the counter is not incremented
         * either.
         */

        /* Arrange */
        $email = 'lockedaccount@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($this->accountKey($email), 10, 'now');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A locked-out login attempt must redirect. Got: ' . $response->statusCode());
        self::assertFalse(str_contains($response->redirectUrl(), 'dashboard'), 'A locked-out login must not reach the dashboard.');
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 10]);
    }

    #[Test]
    public function it_still_allows_login_with_the_correct_password_at_nine_prior_failures(): void
    {
        /* Arrange */
        $email = 'almostlockedaccount@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($this->accountKey($email), 9, 'now');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'dashboard');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email)]);
    }

    #[Test]
    public function it_refuses_to_authenticate_a_request_whose_method_is_not_post(): void
    {
        /*
         * authenticate()'s own "even called internally, only ever from a POST login"
         * guard is unreachable through the public routes today — login() already gates
         * on $this->input->post('btn_login') before calling it, so a real browser can
         * never make a GET reach this far. It exists for a future caller that might not
         * re-check the method. The test harness sets $_POST independently of the declared
         * HTTP method, so this sends exactly that mismatch through the real request
         * pipeline (not a reflection call) to prove the guard still holds if that ever
         * changes.
         */

        /* Arrange */
        $email = 'methodguard@test.local';
        $this->seedLoginUser($email);

        /* Act */
        $response = $this->request('GET', '/sessions/login', [], ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: authenticate() returning false takes login()'s failure branch (sets the
         * flash error and redirects to sessions/login). A mutant making it return true
         * would skip straight to the user_type dashboard/guest redirects without ever
         * setting any session data, matching neither branch and falling through to
         * re-render the login form instead (a 200, not a redirect) — so asserting the
         * redirect itself happened is what actually distinguishes the two. */
        self::assertTrue($response->isRedirect(), 'A GET-method request must still take the login-failure branch. Got: ' . $response->statusCode());
        self::assertFalse(
            str_contains($response->redirectUrl(), 'dashboard'),
            'A GET-method request must never authenticate, even with POST-shaped login data present.'
        );
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email)]);
    }

    // -------------------------------------------------------------------------
    // IP-based rate limiting
    // -------------------------------------------------------------------------

    #[Test]
    public function it_blocks_login_attempts_after_exceeding_the_ip_rate_limit(): void
    {
        /* Arrange: a valid user, and an IP that has used up its attempts inside the window */
        $email = 'throttled@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($this->ipKey(), 20, '-30 seconds');
        $this->seedLoginLog($this->accountKey($email), 1, 'now');

        $logFile = APPPATH . 'logs/log-' . date('Y-m-d') . '.php';
        $offset  = is_file($logFile) ? filesize($logFile) : 0;

        /* Act: the CORRECT password must still be refused */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: no success path ran, so neither counter was cleared or advanced */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 20]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 1]);
        clearstatcache(true, $logFile);
        $logged = is_file($logFile) ? (string) file_get_contents($logFile, false, null, $offset) : '';
        self::assertMatchesRegularExpression('/^WARNING - [^\n]*Login IP rate limit exceeded/m', $logged, 'A throttled login is a security event and must reach the log as a WARNING line (application/core/MY_Log.php; stock CI3 drops that level).');
    }

    #[Test]
    public function it_allows_login_when_previous_attempts_have_expired_from_the_window(): void
    {
        /* Arrange: the IP hit the limit, but all of it is older than the 15-minute window */
        $email = 'expired-window@test.local';
        $this->seedLoginUser($email);
        $this->seedLoginLog($this->ipKey(), 20, '-16 minutes');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: login succeeded (the success branch cleared the stale IP counter) */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->ipKey()]);
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

    private function seedLoginUser(string $email): void
    {
        $this->databaseInsert('ip_users', [
            'user_name'         => 'Login Tester', 'user_password' => password_hash('correct-password', PASSWORD_BCRYPT),
            'user_psalt'        => bin2hex(random_bytes(10)), 'user_email' => $email, 'user_type' => 1, 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
