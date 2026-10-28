<?php

namespace Tests\Feature\Core\Auth;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * The password-reset request form (POST sessions/passwordreset, btn_reset) issues a token only when the request passes
 * the bot check and both rate limits. A refused request must leave the account without a token. Each guard is removed
 * in turn by the mutants these tests were written for, and the positive control proves a plain request does get a token.
 */
#[Group('security')]
#[Group('auth')]
final class PasswordResetRequestGuardsTest extends AbstractTestCase
{
    private const EMAIL = 'reset.me@test.local';

    private int $userId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
        $this->userId = $this->databaseInsert('ip_users', [
            'user_type'          => 2,
            'user_name'          => 'Reset Me',
            'user_email'         => self::EMAIL,
            'user_password'      => password_hash('secret123', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(8)),
            'user_language'      => 'system',
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }

    #[Test]
    public function it_issues_a_token_for_an_ordinary_request(): void
    {
        /* Act: positive control */
        $this->requestReset(self::EMAIL);

        /* Assert */
        self::assertNotSame('', $this->storedToken(), 'An ordinary request must be issued a reset token.');
    }

    #[Test]
    public function it_issues_no_token_to_an_automated_client(): void
    {
        /* Arrange */
        $this->withEnvironment(['HTTP_USER_AGENT' => 'curl/8.5.0']);

        /* Act */
        $this->requestReset(self::EMAIL);

        /* Assert */
        self::assertSame('', $this->storedToken());
    }

    #[Test]
    public function it_issues_no_token_once_the_ip_has_used_up_its_attempts(): void
    {
        /* Arrange: the default limit is 5 requests per hour per IP */
        $this->databaseInsert('ip_login_log', [
            'login_name'           => 'password_reset_ip:' . hash('sha256', '127.0.0.1'),
            'log_count'            => 5,
            'log_create_timestamp' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $this->requestReset(self::EMAIL);

        /* Assert */
        self::assertSame('', $this->storedToken());
    }

    #[Test]
    public function it_issues_no_token_once_the_email_has_used_up_its_attempts_whatever_its_case(): void
    {
        /* Arrange: the default limit is 3 requests per hour per address, counted case-insensitively */
        $this->databaseInsert('ip_login_log', [
            'login_name'           => 'password_reset_email:' . hash('sha256', strtolower(self::EMAIL)),
            'log_count'            => 3,
            'log_create_timestamp' => date('Y-m-d H:i:s'),
        ]);

        /* Act: the same address typed in upper case */
        $this->requestReset(strtoupper(self::EMAIL));

        /* Assert */
        self::assertSame('', $this->storedToken());
    }

    #[Test]
    public function it_counts_the_ip_limit_per_address_not_globally(): void
    {
        /* Arrange: the default test address has used up its attempts */
        $this->databaseInsert('ip_login_log', [
            'login_name'           => 'password_reset_ip:' . hash('sha256', '127.0.0.1'),
            'log_count'            => 5,
            'log_create_timestamp' => date('Y-m-d H:i:s'),
        ]);
        $this->withEnvironment(['REMOTE_ADDR' => '203.0.113.9']);

        /* Act: this request comes from a different address */
        $this->requestReset(self::EMAIL);

        /* Assert */
        self::assertNotSame('', $this->storedToken());
    }

    private function requestReset(string $email): void
    {
        $this->post('/sessions/passwordreset', ['btn_reset' => '1', 'email' => $email]);
    }

    private function storedToken(): string
    {
        return (string) ($this->databaseFetchOne('ip_users', ['user_id' => $this->userId])['user_passwordreset_token'] ?? '');
    }
}
