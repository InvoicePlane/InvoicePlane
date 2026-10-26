<?php

namespace Tests\Feature\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * GHSA-hg6g-m4fp-7cj3 (CWE-613): a session must not outlive a password change. SessionInvalidationTest
 * covers Users::change_password and direct row updates; the password RESET endpoint
 * (Sessions::passwordreset, btn_new_password) is a different writer and was never driven end to end.
 */
final class PasswordResetSessionRevocationTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    private const TOKEN = 'a3f1c2d4e5b60718293a4b5c6d7e8f90a1b2c3d4e5f60718293a4b5c6d7e8f90';

    private const OLD_VERSION = 5;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_ends_a_pre_existing_session_after_the_password_is_reset_through_the_real_endpoint(): void
    {
        /* Arrange: an admin holds a live session (e.g. a stolen cookie), then the owner resets the password */
        $userId = $this->seedAdminWithResetToken();
        $this->actingAsAdmin($userId);
        $oldSession = $this->sessionData;
        $before     = $this->get('/supplier_invoices');
        $this->assertResponseStatusCode($before, 200);

        /* Act: the reset form is submitted by a logged-out browser */
        $this->actingAsGuest();
        $reset = $this->postWithValidCsrfToken('/sessions/passwordreset', $this->resetPayload($userId, self::TOKEN));
        $this->assertResponseRedirectsToRoute($reset, 'sessions/login');

        /* Assert: the pre-reset session is rejected and renders nothing */
        $this->sessionData = $oldSession;
        $stale             = $this->get('/supplier_invoices');
        $this->assertResponseRedirectsToRoute($stale, 'sessions/login');
        self::assertSame('', $stale->body(), 'A session created before a password reset must not render protected pages.');

        /* Positive control: a session bound to the post-reset credentials is accepted */
        $this->actingAsAdmin($userId);
        $fresh = $this->get('/supplier_invoices');
        $this->assertResponseStatusCode($fresh, 200);
        self::assertNotSame('', $fresh->body());
    }

    #[Test]
    public function it_increments_the_auth_version_once_and_replaces_the_credential_fingerprint_on_reset(): void
    {
        /* Arrange */
        if ( ! function_exists('session_credential_fingerprint')) {
            require_once dirname(__DIR__, 3) . '/application/helpers/ip_security_helper.php';
        }
        $userId = $this->seedAdminWithResetToken();
        $before = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);

        /* Act */
        $this->actingAsGuest();
        $reset = $this->postWithValidCsrfToken('/sessions/passwordreset', $this->resetPayload($userId, self::TOKEN));
        $this->assertResponseRedirectsToRoute($reset, 'sessions/login');

        /* Assert */
        $after = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame(self::OLD_VERSION + 1, (int) $after['user_auth_version'], 'A reset must bump user_auth_version by exactly one.');
        self::assertTrue(password_verify('BrandNewPassword123!', (string) $after['user_password']), 'The new password must be the stored one.');
        self::assertFalse(password_verify('OldPassword123!', (string) $after['user_password']), 'The old password must no longer verify.');
        self::assertNotSame(
            session_credential_fingerprint((string) $before['user_password']),
            session_credential_fingerprint((string) $after['user_password']),
            'The credential fingerprint a session carries must change when the password does.'
        );
        self::assertEmpty($after['user_passwordreset_token'], 'The used reset token must be cleared.');
    }

    #[Test]
    public function it_changes_only_the_resetting_users_credentials_and_leaves_every_other_user_alone(): void
    {
        /* Arrange */
        $userId      = $this->seedAdminWithResetToken();
        $bystanderId = $this->seedAdminWithResetToken();
        $this->databaseUpdate('ip_users', ['user_passwordreset_token' => hash('sha256', 'unrelated-token')], ['user_id' => $bystanderId]);
        $bystanderBefore = $this->databaseFetchOne('ip_users', ['user_id' => $bystanderId]);

        /* Act */
        $this->actingAsGuest();
        $reset = $this->postWithValidCsrfToken('/sessions/passwordreset', $this->resetPayload($userId, self::TOKEN));
        $this->assertResponseRedirectsToRoute($reset, 'sessions/login');

        /* Assert: the update is scoped to the one user_id (positive control: that user did change) */
        $bystanderAfter = $this->databaseFetchOne('ip_users', ['user_id' => $bystanderId]);
        self::assertSame($bystanderBefore['user_password'], $bystanderAfter['user_password']);
        self::assertSame($bystanderBefore['user_psalt'], $bystanderAfter['user_psalt']);
        self::assertSame(self::OLD_VERSION, (int) $bystanderAfter['user_auth_version']);
        $changed = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame(self::OLD_VERSION + 1, (int) $changed['user_auth_version']);
    }

    #[Test]
    public function it_keeps_an_existing_session_alive_when_the_reset_is_rejected_for_a_wrong_token(): void
    {
        /* Arrange */
        $userId = $this->seedAdminWithResetToken();
        $this->actingAsAdmin($userId);
        $session = $this->sessionData;

        /* Act: wrong token, so nothing may change */
        $this->actingAsGuest();
        $this->postWithValidCsrfToken('/sessions/passwordreset', $this->resetPayload($userId, str_repeat('b', 64)));

        /* Assert: no revocation side effect, and the session is only dead because of a real reset (control for the test above) */
        $row = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame(self::OLD_VERSION, (int) $row['user_auth_version']);
        self::assertTrue(password_verify('OldPassword123!', (string) $row['user_password']));
        $this->sessionData = $session;
        $this->assertResponseStatusCode($this->get('/supplier_invoices'), 200);
    }

    /** @return array<string, mixed> */
    private function resetPayload(int $userId, string $token): array
    {
        return [
            'btn_new_password' => '1',
            'user_id'          => $userId,
            'token'            => $token,
            'new_password'     => 'BrandNewPassword123!',
            'new_passwordv'    => 'BrandNewPassword123!',
        ];
    }

    private function seedAdminWithResetToken(): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'                       => 1,
            'user_name'                       => 'Admin ' . bin2hex(random_bytes(3)),
            'user_email'                      => 'reset+' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'                   => password_hash('OldPassword123!', PASSWORD_DEFAULT),
            'user_psalt'                      => bin2hex(random_bytes(8)),
            'user_language'                   => 'system',
            'user_active'                     => 1,
            'user_auth_version'               => self::OLD_VERSION,
            'user_passwordreset_token'        => hash('sha256', self::TOKEN),
            'user_passwordreset_token_expiry' => gmdate('Y-m-d H:i:s', time() + 900),
            'user_date_created'               => date('Y-m-d H:i:s'),
            'user_date_modified'              => date('Y-m-d H:i:s'),
        ]);
    }
}
