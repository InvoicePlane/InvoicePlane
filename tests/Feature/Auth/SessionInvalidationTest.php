<?php

namespace Tests\Feature\Auth;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

final class SessionInvalidationTest extends AbstractTestCase
{
    #[Test]
    public function it_accepts_a_session_bound_to_the_current_password(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertNotSame('', $response->body(), 'The protected page must actually render.');
    }

    #[Test]
    public function it_rejects_an_old_session_after_the_password_changes(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $before = $this->get('/supplier_invoices');
        $this->databaseUpdate('ip_users', [
            'user_password'     => password_hash('NewPassword456!', PASSWORD_DEFAULT),
            'user_auth_version' => 1,
        ], ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert: the same session worked until the password changed, then no longer does */
        $this->assertResponseOk($before);
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'The protected page must not be rendered for a stale session.');
    }

    #[Test]
    public function it_rejects_an_old_session_when_only_the_password_hash_changes(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $before = $this->get('/supplier_invoices');
        $this->databaseUpdate('ip_users', [
            'user_password' => password_hash('AnotherPassword789!', PASSWORD_DEFAULT),
        ], ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseOk($before);
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'The protected page must not be rendered for a stale session.');
    }

    #[Test]
    public function it_ends_another_sessions_access_after_changing_a_password_through_the_real_endpoint(): void
    {
        /*
         * The three tests above simulate "the password changed" by UPDATE-ing ip_users
         * directly, so none of them ever call Users::change_password() itself. This
         * exercises the real controller endpoint instead of its downstream effect.
         */

        /* Arrange: two independent sessions for the same account (e.g. two browsers) */
        $userId          = $this->seedAdmin();
        $attackerSession = ['user_id' => (string) $userId, 'user_type' => '1', 'user_email' => 'admin@test.local', 'user_name' => 'Test Admin', 'user_company' => 'Test Co', 'user_language' => 'system'];
        $this->actingAsAdmin($userId);
        $attackerSession['user_auth_version'] = $this->sessionData['user_auth_version'];
        $attackerSession['user_credential']   = $this->sessionData['user_credential'];

        /* Act: the account's own session changes the password through the real endpoint */
        $response = $this->post('/users/change_password/' . $userId, [
            'user_password'  => 'BrandNewPassword123!',
            'user_passwordv' => 'BrandNewPassword123!',
        ]);
        self::assertTrue($response->isRedirect(), 'A valid password change must redirect.');

        /* Assert: the OTHER session of the same account is rejected on its next request */
        $this->sessionData = $attackerSession;
        $rejected          = $this->get('/supplier_invoices');
        $this->assertResponseRedirectsToRoute($rejected, 'sessions/login');
        self::assertSame('', $rejected->body(), 'A session that did not make the change must not survive it.');
    }

    #[Test]
    public function it_preserves_the_acting_users_own_session_after_changing_their_own_password(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);

        /* Act: the acting user changes their own password through the real endpoint */
        $changeResponse = $this->post('/users/change_password/' . $userId, [
            'user_password'  => 'BrandNewPassword123!',
            'user_passwordv' => 'BrandNewPassword123!',
        ]);
        self::assertTrue($changeResponse->isRedirect(), 'A valid password change must redirect.');

        /* Assert: carrying the session forward (as a real browser cookie would), the
         * SAME session that just changed its own password must still be authorized —
         * not caught by the very invalidation it just triggered for every other session. */
        $this->sessionData = $changeResponse->session();
        $response          = $this->get('/supplier_invoices');
        $this->assertResponseStatusCode($response, 200);
        self::assertNotSame('', $response->body(), 'The acting user must not be logged out by their own password change.');
    }

    #[Test]
    public function it_gives_the_session_that_changes_its_own_password_a_new_id_so_a_copied_cookie_dies(): void
    {
        /* Arrange: a stolen cookie is a copy of this very session, so it shares the session id */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);

        /* Act */
        $response = $this->post('/users/change_password/' . $userId, [
            'user_password'  => 'BrandNewPassword123!',
            'user_passwordv' => 'BrandNewPassword123!',
        ]);

        /* Assert: the old id (what the thief holds) no longer names the live session; the owner keeps their login */
        self::assertTrue($response->isRedirect(), 'A valid password change must redirect.');
        self::assertNotSame('', $response->initialSessionId());
        self::assertNotSame($response->initialSessionId(), $response->sessionId(), 'The session id must change after the password change.');
        self::assertSame((string) $userId, $response->sessionValue('user_id'), 'The acting user stays logged in on the new session id.');
        $savePath = rtrim((string) (ini_get('session.save_path') ?: sys_get_temp_dir()), '/');
        self::assertFileDoesNotExist($savePath . '/sess_' . $response->initialSessionId(), 'The old session must be destroyed, not just left behind under its old id.');
    }

    #[Test]
    public function it_keeps_the_session_id_when_the_primary_admin_changes_another_users_password(): void
    {
        /* Arrange: positive control, the acting session is not the one whose credentials changed */
        $otherId = $this->seedAdmin();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->post('/users/change_password/' . $otherId, [
            'user_password'  => 'BrandNewPassword123!',
            'user_passwordv' => 'BrandNewPassword123!',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid password change must redirect.');
        self::assertSame($response->initialSessionId(), $response->sessionId(), 'Changing someone else\'s password must not rotate the acting session.');
    }

    #[Test]
    public function it_rejects_a_session_whose_account_was_deactivated(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $this->assertResponseOk($this->get('/supplier_invoices'));
        $this->databaseUpdate('ip_users', ['user_active' => 0], ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'A deactivated account must not keep access through an old session.');
    }

    #[Test]
    public function it_rejects_a_session_whose_account_was_downgraded_from_administrator(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $this->assertResponseOk($this->get('/supplier_invoices'));
        $this->databaseUpdate('ip_users', ['user_type' => 2], ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'A downgraded account must not keep administrator access through an old session.');
    }

    #[Test]
    public function it_rejects_a_session_whose_account_row_was_deleted(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $this->assertResponseOk($this->get('/supplier_invoices'));
        $this->databaseDelete('ip_users', ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'A deleted account must not keep access through an old session.');
    }

    #[Test]
    public function it_rejects_a_session_that_carries_no_user_id(): void
    {
        /* Arrange */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        unset($this->sessionData['user_id']);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'A session naming no account must not be authorised.');
    }

    #[Test]
    public function it_rejects_a_session_when_only_the_auth_version_changed(): void
    {
        /* Arrange: the password hash (so the credential fingerprint) stays identical;
         * only the auth version is bumped, as a forced "log out everywhere" would do. */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $this->assertResponseOk($this->get('/supplier_invoices'));
        $this->databaseUpdate('ip_users', ['user_auth_version' => 99], ['user_id' => $userId]);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'A stale auth version alone must end the session.');
    }

    #[Test]
    public function it_binds_a_session_created_before_fingerprints_existed_to_the_current_password(): void
    {
        /* Arrange: an older session with no credential fingerprint at all */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        unset($this->sessionData['user_credential']);

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert: still authorised, and now bound, so a later password change can end it */
        $this->assertResponseStatusCode($response, 200);
        self::assertNotSame('', (string) ($response->session()['user_credential'] ?? ''), 'The legacy session must be bound to the current password.');

        $this->sessionData = $response->session();
        $this->databaseUpdate('ip_users', ['user_password' => password_hash('ChangedAfterBinding1!', PASSWORD_DEFAULT)], ['user_id' => $userId]);
        $after = $this->get('/supplier_invoices');
        $this->assertResponseRedirectsToRoute($after, 'sessions/login');
    }

    #[Test]
    public function it_rejects_a_session_claiming_a_role_the_account_does_not_hold_in_the_session_snapshot(): void
    {
        /* Arrange: the stored account is an administrator, but the session snapshot says guest (2) */
        $userId = $this->seedAdmin();
        $this->actingAsAdmin($userId);
        $this->sessionData['user_type'] = '2';

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert: the session snapshot is checked, not just the database */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
        self::assertSame('', $response->body(), 'A session whose own role is not administrator must be rejected.');
    }

    #[Test]
    public function it_clears_a_pending_password_reset_token_when_the_password_is_changed(): void
    {
        /* Arrange: a reset link was requested, then the owner changes the password another way */
        $userId = $this->seedAdmin();
        $this->databaseUpdate('ip_users', [
            'user_passwordreset_token'        => hash('sha256', 'pending-reset-token'),
            'user_passwordreset_token_expiry' => gmdate('Y-m-d H:i:s', time() + 600),
        ], ['user_id' => $userId]);
        $this->actingAsAdmin($userId);

        /* Act */
        $response = $this->post('/users/change_password/' . $userId, [
            'user_password'  => 'BrandNewPassword123!',
            'user_passwordv' => 'BrandNewPassword123!',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid password change must redirect.');
        $row = $this->databaseFetchOne('ip_users', ['user_id' => $userId]);
        self::assertSame('', (string) $row['user_passwordreset_token'], 'The pending reset token must be cleared.');
        self::assertNull($row['user_passwordreset_token_expiry']);
    }

    private function seedAdmin(): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'          => 1,
            'user_name'          => 'Admin ' . bin2hex(random_bytes(3)),
            'user_email'         => 'admin+' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'      => password_hash('OldPassword123!', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(8)),
            'user_language'      => 'system',
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
