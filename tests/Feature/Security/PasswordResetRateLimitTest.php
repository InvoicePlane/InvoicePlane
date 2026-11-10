<?php

namespace Tests\Feature\Security;

use DateTimeImmutable;
use DateTimeZone;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-p7w2-hmm5-qw7m / GHSA-hcxg-3465-jc8m: the password-reset request endpoint is rate limited
 * per IP (5 per 60 minutes) and per email (3 per 1 hour) from rows in ip_login_log, not from the
 * session, so a client that sends no session cookie cannot reset the counter. The only existing
 * tests of this limiter ran against StubSessionsSecurity, a re-implementation that never touches
 * production code. These drive the real controller.
 */
final class PasswordResetRateLimitTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_refuses_a_reset_for_an_email_that_reached_the_email_limit(): void
    {
        /* Arrange: 3 requests already counted for this email inside the window */
        $email  = 'limited-email@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->emailKey($email), 3, 'now');

        /* Act */
        $response = $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert: redirected, no token issued, counter not advanced */
        self::assertTrue($response->isRedirect());
        self::assertSame('', $this->tokenOf($userId), 'No reset token may be issued once the email limit is reached.');
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->emailKey($email), 'log_count' => 3]);
    }

    #[Test]
    public function it_still_issues_a_reset_one_below_the_email_limit_and_counts_it(): void
    {
        /* Arrange */
        $email  = 'almost-limited-email@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->emailKey($email), 2, 'now');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertNotSame('', $this->tokenOf($userId), 'A request below the limit must still issue a reset token.');
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->emailKey($email), 'log_count' => 3]);
    }

    #[Test]
    public function it_refuses_a_reset_from_an_ip_that_reached_the_ip_limit(): void
    {
        /* Arrange: 5 requests already counted for this IP inside the window */
        $email  = 'ip-limited@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->ipKey(), 5, 'now');

        /* Act */
        $response = $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert: blocked before the email counter is even created */
        self::assertTrue($response->isRedirect());
        self::assertSame('', $this->tokenOf($userId));
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->emailKey($email)]);
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 5]);
    }

    #[Test]
    public function it_still_issues_a_reset_one_below_the_ip_limit_and_counts_it(): void
    {
        /* Arrange */
        $email  = 'ip-almost-limited@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->ipKey(), 4, 'now');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertNotSame('', $this->tokenOf($userId));
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 5]);
    }

    #[Test]
    public function it_allows_a_reset_again_once_the_email_window_has_elapsed(): void
    {
        /* Arrange: at the limit, but the counter started two hours ago (window is one hour) */
        $email  = 'expired-window@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->emailKey($email), 3, '-2 hours');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert: allowed, and the counter restarted at 1 */
        self::assertNotSame('', $this->tokenOf($userId), 'An elapsed window must lift the limit.');
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->emailKey($email), 'log_count' => 1]);
    }

    #[Test]
    public function it_stores_a_token_expiry_fifteen_minutes_ahead_by_default(): void
    {
        /* Arrange (GHSA-5r28-6rw3-25c2: a reset token must carry an expiry written at request time) */
        $email  = 'expiry-default@test.local';
        $userId = $this->seedActiveUser($email);

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertEqualsWithDelta(15 * 60, $this->expiryInSeconds($userId), 120);
    }

    #[Test]
    public function it_honours_a_configured_expiry_within_the_allowed_range(): void
    {
        /* Arrange */
        $this->withEnvironment(['PASSWORD_RESET_TOKEN_EXPIRY_MINUTES' => '30']);
        $email  = 'expiry-custom@test.local';
        $userId = $this->seedActiveUser($email);

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertEqualsWithDelta(30 * 60, $this->expiryInSeconds($userId), 120);
    }

    #[Test]
    public function it_falls_back_to_fifteen_minutes_for_an_out_of_range_expiry_setting(): void
    {
        /* Arrange: 5000 minutes is beyond the 24 hour maximum */
        $this->withEnvironment(['PASSWORD_RESET_TOKEN_EXPIRY_MINUTES' => '5000']);
        $email  = 'expiry-toolong@test.local';
        $userId = $this->seedActiveUser($email);

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertEqualsWithDelta(15 * 60, $this->expiryInSeconds($userId), 120);
    }

    #[Test]
    public function it_still_refuses_an_email_whose_limit_was_reached_thirty_minutes_ago(): void
    {
        /* Arrange: inside the one hour window */
        $email  = 'half-hour-email@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->emailKey($email), 3, '-30 minutes');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertSame('', $this->tokenOf($userId));
    }

    #[Test]
    public function it_keeps_counting_an_ip_whose_earlier_requests_are_thirty_minutes_old(): void
    {
        /* Arrange: 4 counted requests half an hour ago, still inside the sixty minute window */
        $email  = 'half-hour-count@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->ipKey(), 4, '-30 minutes');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert: the new request adds to the running count instead of restarting it at 1 */
        self::assertNotSame('', $this->tokenOf($userId));
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 5]);
    }

    #[Test]
    public function it_restarts_the_ip_count_once_the_previous_requests_are_just_outside_the_window(): void
    {
        /* Arrange: 4 counted requests 3630 seconds ago, thirty seconds outside the sixty minute window */
        $email  = 'just-outside-count@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->ipKey(), 4, '-3630 seconds');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert: the stale counter was discarded, so this request is the first of a new window */
        self::assertNotSame('', $this->tokenOf($userId));
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->ipKey(), 'log_count' => 1]);
    }

    #[Test]
    public function it_still_refuses_an_ip_whose_limit_was_reached_thirty_minutes_ago(): void
    {
        /* Arrange: inside the sixty minute window */
        $email  = 'half-hour-ip@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->ipKey(), 5, '-30 minutes');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertSame('', $this->tokenOf($userId));
    }

    #[Test]
    public function it_lifts_the_ip_limit_just_after_the_sixty_minute_window(): void
    {
        /* Arrange: 60.5 minutes old, so a 61 minute window (a mutated constant) would still block it */
        $email  = 'just-past-ip@test.local';
        $userId = $this->seedActiveUser($email);
        $this->seedLog($this->ipKey(), 5, '-3630 seconds');

        /* Act */
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);

        /* Assert */
        self::assertNotSame('', $this->tokenOf($userId));
    }

    private function expiryInSeconds(int $userId): int
    {
        $expiry = (string) ($this->databaseFetchOne('ip_users', ['user_id' => $userId])['user_passwordreset_token_expiry'] ?? '');
        self::assertNotSame('', $expiry, 'A reset request must store a token expiry.');

        return (int) ((new DateTimeImmutable($expiry, new DateTimeZone('UTC')))->getTimestamp() - time());
    }

    private function emailKey(string $email): string
    {
        return 'password_reset_email:' . hash('sha256', mb_strtolower($email));
    }

    private function ipKey(): string
    {
        return 'password_reset_ip:' . hash('sha256', '127.0.0.1');
    }

    private function tokenOf(int $userId): string
    {
        return (string) ($this->databaseFetchOne('ip_users', ['user_id' => $userId])['user_passwordreset_token'] ?? '');
    }

    private function seedLog(string $key, int $count, string $when): void
    {
        $this->databaseInsert('ip_login_log', [
            'login_name' => $key, 'log_count' => $count, 'log_create_timestamp' => date('Y-m-d H:i:s', strtotime($when)),
        ]);
    }

    private function seedActiveUser(string $email): int
    {
        return $this->databaseInsert('ip_users', [
            'user_name'         => 'Reset Tester', 'user_password' => password_hash('irrelevant-pw', PASSWORD_BCRYPT),
            'user_psalt'        => bin2hex(random_bytes(10)), 'user_email' => $email, 'user_type' => 1, 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
