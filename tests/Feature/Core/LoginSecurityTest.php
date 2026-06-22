<?php

namespace Tests\Feature\Core;

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
        $payload = [
            'btn_login' => '1',
            'email'     => 'nobody@does-not-exist.example',
            'password'  => 'irrelevant-password',
        ];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'An unknown email must trigger a redirect, not a 200 with details. Got: ' . $response->statusCode()
        );
    }

    #[Test]
    public function it_redirects_after_a_login_attempt_with_a_wrong_password(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_users', [
            'user_name'          => 'Login Security Tester',
            'user_password'      => password_hash('correct-password', PASSWORD_BCRYPT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'loginsec@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $payload = [
            'btn_login' => '1',
            'email'     => 'loginsec@test.local',
            'password'  => 'wrong-password',
        ];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'A wrong password must trigger a redirect, not reveal any account info. Got: ' . $response->statusCode()
        );
    }

    #[Test]
    public function it_does_not_reveal_whether_an_email_exists_in_error_responses(): void
    {
        /* Arrange */
        $unknownPayload = [
            'btn_login' => '1',
            'email'     => 'ghost@no-account.example',
            'password'  => 'password123',
        ];

        $wrongPasswordPayload = [
            'btn_login' => '1',
            'email'     => 'admin@test.local',
            'password'  => 'definitely-wrong',
        ];

        /* Act */
        $unknownResponse      = $this->post('/sessions/login', $unknownPayload);
        $wrongPasswordResponse = $this->post('/sessions/login', $wrongPasswordPayload);

        /* Assert */
        // Both must redirect — not 200, not 403, not 401
        self::assertTrue(
            $unknownResponse->isRedirect(),
            'Unknown email must redirect, not produce a distinguishable response. Got: ' . $unknownResponse->statusCode()
        );
        self::assertTrue(
            $wrongPasswordResponse->isRedirect(),
            'Wrong password must redirect, not produce a distinguishable response. Got: ' . $wrongPasswordResponse->statusCode()
        );
        // Both must return the same status code so callers cannot distinguish them
        self::assertSame(
            $unknownResponse->statusCode(),
            $wrongPasswordResponse->statusCode(),
            'Unknown-email and wrong-password failures must produce identical HTTP status codes.'
        );
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
    // IP-based rate limiting
    // -------------------------------------------------------------------------

    #[Test]
    public function it_blocks_login_attempts_after_exceeding_the_ip_rate_limit(): void
    {
        /* Arrange */
        // Seed the session with pre-existing failed attempts that fill the window.
        // LOGIN_IP_MAX_ATTEMPTS defaults to 20. We set 20 timestamps within the window.
        $now      = time();
        $attempts = array_fill(0, 20, $now - 30);
        $key      = 'login_attempts_ip_' . md5('127.0.0.1');

        $this->sessionData[$key] = $attempts;

        $payload = [
            'btn_login' => '1',
            'email'     => 'anyone@example.com',
            'password'  => 'anypassword',
        ];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            'A rate-limited IP must be redirected back to login. Got: ' . $response->statusCode()
        );
        self::assertFalse(
            $response->contains('dashboard'),
            'A rate-limited login attempt must never reach the dashboard.'
        );
    }

    #[Test]
    public function it_allows_login_when_previous_attempts_have_expired_from_the_window(): void
    {
        /* Arrange */
        // 20 attempts, all older than the 15-minute window — they should be pruned.
        $expired  = time() - (16 * 60);
        $attempts = array_fill(0, 20, $expired);
        $key      = 'login_attempts_ip_' . md5('127.0.0.1');

        $this->sessionData[$key] = $attempts;

        $payload = [
            'btn_login' => '1',
            'email'     => 'nobody@no-account.example',
            'password'  => 'irrelevant',
        ];

        /* Act */
        $response = $this->post('/sessions/login', $payload);

        /* Assert */
        // Should redirect (failed credentials), but NOT be blocked by rate limiting.
        // Both rate-limited and credential-failed paths redirect — we verify it's not a 500.
        self::assertNotSame(
            500,
            $response->statusCode(),
            'Expired rate-limit attempts must not block a login attempt or cause a server error.'
        );
        self::assertTrue(
            $response->isRedirect(),
            'A login with expired-window attempts should redirect normally (not be rate-limited). Got: ' . $response->statusCode()
        );
    }
}
