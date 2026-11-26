<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * GHSA-jw57-6692-jh2q: a deactivated account could still sign in. Mdl_sessions::auth() now insists on user_active === 1
 * after the password check. LoginSecurityTest covers the plain inactive and active cases only; the legacy md5 route
 * (accounts that have never logged in since the salted-hash migration, user_psalt empty) and an unexpected user_active
 * value were not exercised.
 */
final class LegacyLoginAndActiveStatusTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsGuest();
    }

    #[Test]
    public function it_denies_a_deactivated_legacy_md5_account_even_with_the_correct_password(): void
    {
        /* Arrange */
        $email = 'legacy-inactive@test.local';
        $this->seedUser($email, ['user_active' => 0, 'user_psalt' => '', 'user_password' => md5('legacy-pass')]);

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'legacy-pass']);

        /* Assert */
        self::assertTrue($response->isRedirect());
        self::assertFalse(str_contains($response->redirectUrl(), 'dashboard'), 'A deactivated legacy account must not reach the dashboard.');
        self::assertArrayNotHasKey('user_id', $response->session());
    }

    #[Test]
    public function it_denies_an_account_whose_active_flag_is_not_exactly_one(): void
    {
        /* Arrange: any value other than 1 is not "active" */
        $email = 'active-two@test.local';
        $this->seedUser($email, ['user_active' => 2]);

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'correct-password']);

        /* Assert */
        self::assertFalse(str_contains($response->redirectUrl(), 'dashboard'));
        self::assertArrayNotHasKey('user_id', $response->session());
    }

    #[Test]
    public function it_logs_in_an_active_legacy_md5_account_and_upgrades_its_stored_hash(): void
    {
        /* Arrange */
        $email  = 'legacy-active@test.local';
        $userId = $this->seedUser($email, ['user_psalt' => '', 'user_password' => md5('legacy-pass')]);

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'legacy-pass']);

        /* Assert: signed in, and the md5 hash was replaced by a salted one */
        $this->assertResponseRedirectsToRoute($response, 'dashboard');
        self::assertSame((string) $userId, (string) $response->sessionValue('user_id'));
        $row = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertNotSame(md5('legacy-pass'), $row['user_password'], 'The legacy md5 hash must be upgraded on first login.');
        self::assertNotSame('', (string) $row['user_psalt']);
    }

    #[Test]
    public function it_rejects_a_wrong_password_on_a_legacy_md5_account_and_leaves_it_untouched(): void
    {
        /* Arrange */
        $email  = 'legacy-wrong@test.local';
        $userId = $this->seedUser($email, ['user_psalt' => '', 'user_password' => md5('legacy-pass')]);

        /* Act */
        $response = $this->post('/sessions/login', ['btn_login' => '1', 'email' => $email, 'password' => 'not-the-password']);

        /* Assert */
        self::assertFalse(str_contains($response->redirectUrl(), 'dashboard'));
        $this->assertDatabaseHas('ip_users', ['user_id' => $userId, 'user_password' => md5('legacy-pass'), 'user_psalt' => '']);
    }

    #[Test]
    public function it_upgrades_only_the_signing_in_users_hash_not_every_legacy_account(): void
    {
        /* Arrange: two legacy md5 accounts; only the first signs in */
        $first  = $this->seedUser('legacy-one@test.local', ['user_psalt' => '', 'user_password' => md5('pass-one')]);
        $second = $this->seedUser('legacy-two@test.local', ['user_psalt' => '', 'user_password' => md5('pass-two')]);

        /* Act */
        $this->post('/sessions/login', ['btn_login' => '1', 'email' => 'legacy-one@test.local', 'password' => 'pass-one']);

        /* Assert: the other account keeps its md5 hash and its empty salt */
        $this->assertDatabaseHas('ip_users', ['user_id' => $second, 'user_password' => md5('pass-two'), 'user_psalt' => '']);
        self::assertNotSame(md5('pass-one'), $this->databaseFetchOne('ip_users', ['user_id' => $first])['user_password']);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function seedUser(string $email, array $overrides = []): int
    {
        return $this->databaseInsert('ip_users', array_merge([
            'user_name'         => 'Legacy Tester', 'user_password' => password_hash('correct-password', PASSWORD_BCRYPT),
            'user_psalt'        => bin2hex(random_bytes(10)), 'user_email' => $email, 'user_type' => 1, 'user_active' => 1,
            'user_date_created' => date('Y-m-d H:i:s'), 'user_date_modified' => date('Y-m-d H:i:s'),
        ], $overrides));
    }
}
