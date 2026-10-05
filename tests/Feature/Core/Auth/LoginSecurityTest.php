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

        /* Assert: success clears both counters (only the success branch does that) */
        self::assertTrue($response->isRedirect(), 'A successful login must redirect. Got: ' . $response->statusCode());
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email)]);
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->ipKey()]);
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

        /* Act: the CORRECT password must still be refused */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: no success path ran, so neither counter was cleared or advanced */
        self::assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 20]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 1]);
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
            'user_name' => 'Login Tester', 'user_password' => password_hash('correct-password', PASSWORD_BCRYPT),
            'user_psalt' => bin2hex(random_bytes(10)), 'user_email' => $email, 'user_type' => 1, 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
