<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-cjwm-qx8w-hrq2: the per-account login lockout never expired. authenticate() stops
 * counting at 10 failures, so the unlock rule (log_count >= 10 AND older than 12 hours) had
 * to compare with >= 10; with > 10 the counter could never exceed 10, the unlock branch was
 * unreachable, and the account stayed locked forever. LoginSecurityTest covers the locked
 * (10, fresh) and almost-locked (9) cases but never an old lockout.
 */
final class LoginLockoutExpiryTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_unlocks_an_account_whose_lockout_is_older_than_twelve_hours(): void
    {
        /* Arrange: exactly 10 failures, the newest 13 hours ago */
        $email = 'expired-lockout@test.local';
        $this->seedUser($email);
        $this->seedLog($this->accountKey($email), 10, '-13 hours');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: the correct password logs in, and the stale counter is gone */
        $this->assertResponseRedirectsToRoute($response, 'dashboard');
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email)]);
    }

    #[Test]
    public function it_keeps_an_account_locked_while_the_lockout_is_younger_than_twelve_hours(): void
    {
        /* Arrange: exactly 10 failures, the newest 11 hours ago */
        $email = 'recent-lockout@test.local';
        $this->seedUser($email);
        $this->seedLog($this->accountKey($email), 10, '-11 hours');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert: still locked out, counter untouched */
        self::assertTrue($response->isRedirect());
        self::assertFalse(str_contains($response->redirectUrl(), 'dashboard'), 'A lockout younger than 12 hours must still block a correct password.');
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 10]);
    }

    #[Test]
    public function it_does_not_unlock_an_account_below_the_threshold_just_because_the_log_is_old(): void
    {
        /* Arrange: 9 failures from 13 hours ago are not a lockout, so the unlock rule must not reset them.
         * A wrong password is used because a successful login would clear the counter and hide the difference. */
        $email = 'old-nine@test.local';
        $this->seedUser($email);
        $this->seedLog($this->accountKey($email), 9, '-13 hours');

        /* Act */
        $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'wrong-password']);

        /* Assert: the nine old failures survived and this one made ten */
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 10]);
    }

    #[Test]
    public function it_unlocks_just_past_the_twelve_hour_boundary(): void
    {
        /* Arrange: 12.5 hours old, so a 13 hour window (a mutated constant) would still hold it locked */
        $email = 'boundary-lockout@test.local';
        $this->seedUser($email);
        $this->seedLog($this->accountKey($email), 10, '-750 minutes');

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'dashboard');
    }

    #[Test]
    public function it_restarts_the_failure_count_when_an_expired_lockout_meets_a_wrong_password(): void
    {
        /* Arrange */
        $email = 'expired-then-wrong@test.local';
        $this->seedUser($email);
        $this->seedLog($this->accountKey($email), 10, '-13 hours');

        /* Act */
        $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'wrong-password']);

        /* Assert: the stale counter was cleared by the unlock rule, then this one failure counted. Without the reset the row
         * would stay at 10, because counting stops at the threshold. */
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 1]);
    }

    #[Test]
    public function it_applies_the_lockout_to_email_spellings_the_database_treats_as_the_same_account(): void
    {
        /* Arrange: the exact address is locked out; the same address with a trailing space has its own counter row */
        $email = 'variant-lockout@test.local';
        $this->seedUser($email);
        $this->seedLog($this->accountKey($email), 10, 'now');

        /* Act: the users table compares emails with a PAD SPACE collation, so the spaced spelling finds the same account */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email . ' ', 'password' => 'correct-password']);

        /* Assert */
        self::assertTrue($response->isRedirect());
        self::assertFalse(str_contains($response->redirectUrl(), 'dashboard'), 'A locked-out account must not be reachable through another spelling of its address.');
    }

    #[Test]
    public function it_counts_a_failure_made_through_another_spelling_on_the_one_account_counter(): void
    {
        /* Arrange */
        $email = 'variant-count@test.local';
        $this->seedUser($email);

        /* Act: a wrong password through the spaced spelling */
        $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email . ' ', 'password' => 'wrong-password']);

        /* Assert: counted on the stored spelling's row, and no extra row is created for the variant */
        $this->assertDatabaseHas('ip_login_log', ['login_name' => $this->accountKey($email), 'log_count' => 1]);
        $this->assertDatabaseMissing('ip_login_log', ['login_name' => $this->accountKey($email . ' ')]);
    }

    private function accountKey(string $email): string
    {
        return 'login_account:' . hash('sha256', mb_strtolower($email));
    }

    private function seedLog(string $key, int $count, string $when): void
    {
        $this->databaseInsert('ip_login_log', [
            'login_name' => $key, 'log_count' => $count, 'log_create_timestamp' => date('Y-m-d H:i:s', strtotime($when)),
        ]);
    }

    private function seedUser(string $email): void
    {
        $this->databaseInsert('ip_users', [
            'user_name'         => 'Lockout Tester', 'user_password' => password_hash('correct-password', PASSWORD_BCRYPT),
            'user_psalt'        => bin2hex(random_bytes(10)), 'user_email' => $email, 'user_type' => 1, 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
