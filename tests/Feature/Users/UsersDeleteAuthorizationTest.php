<?php

namespace Tests\Feature\Users;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;
use Users;

/**
 * GHSA-6r23-8rf2-78h3 (PR #1713): Users::delete() is limited to the primary administrator.
 *
 * The existing UsersControllerTest attack test runs with CSRF off, only with an admin victim, and
 * only checks one denial shape. These tests run under the production CSRF configuration, cover
 * every victim class (guest-type user, peer admin, the primary admin, the attacker itself, an id
 * that does not exist), pin the positive control "primary may delete a peer admin", and prove
 * that a deleted administrator's still-open session stops working.
 *
 * Mutation targets (application/modules/users/controllers/Users.php, Users::delete()):
 *   - removing / inverting `! Mdl_Users::is_primary_administrator($current_user_id)`
 *   - testing `$id` instead of the acting user in that guard
 *   - letting a self-delete or a guest-type victim through
 *   - moving the guard behind the existence/primary-target logic (id 999999 must still get 403)
 */
#[Group('users')]
#[Group('security')]
#[CoversClass(Users::class)]
final class UsersDeleteAuthorizationTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_denies_a_peer_admin_deleting_a_guest_user_and_keeps_the_guests_client_assignments(): void
    {
        /* Arrange */
        $peerId   = $this->seedUser(1, 'peer-del-guest');
        $guestId  = $this->seedUser(2, 'victim-guest');
        $clientId = $this->seedClient();
        $this->databaseInsert('ip_user_clients', ['user_id' => $guestId, 'client_id' => $clientId]);
        $this->actingAsAdmin($peerId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/' . $guestId);

        /* Assert */
        self::assertSame(403, $response->statusCode());
        $this->assertDatabaseHas('ip_users', ['user_id' => $guestId, 'user_name' => 'victim-guest']);
        $this->assertDatabaseCount('ip_user_clients', 1, ['user_id' => $guestId]);
    }

    #[Test]
    public function it_denies_a_peer_admin_deleting_the_primary_admin_with_403_rather_than_a_silent_redirect(): void
    {
        /* Arrange */
        $peerId = $this->seedUser(1, 'peer-del-primary');
        $this->actingAsAdmin($peerId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/1');

        /* Assert */
        self::assertSame(403, $response->statusCode(), 'The primary admin is protected by the role check, not only by the target check.');
        self::assertFalse($response->isRedirect());
        $this->assertDatabaseHas('ip_users', ['user_id' => 1]);
        $this->assertDatabaseHas('ip_users', ['user_id' => $peerId]);
    }

    #[Test]
    public function it_denies_a_peer_admin_deleting_its_own_account_through_this_endpoint(): void
    {
        /* Arrange */
        $peerId = $this->seedUser(1, 'peer-self-delete');
        $this->actingAsAdmin($peerId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/' . $peerId);

        /* Assert */
        self::assertSame(403, $response->statusCode());
        $this->assertDatabaseHas('ip_users', ['user_id' => $peerId, 'user_name' => 'peer-self-delete']);
    }

    #[Test]
    public function it_answers_403_for_a_peer_admin_even_when_the_target_id_does_not_exist(): void
    {
        /* Arrange */
        $peerId = $this->seedUser(1, 'peer-ghost');
        $this->actingAsAdmin($peerId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/999999');

        /* Assert: authorization comes first, so existence of ids is not probeable */
        self::assertSame(403, $response->statusCode());
        $this->assertDatabaseHas('ip_users', ['user_id' => $peerId]);
    }

    #[Test]
    public function it_lets_the_primary_admin_delete_a_peer_admin_and_only_that_peer(): void
    {
        /* Arrange: positive control for every denial above */
        $peerId  = $this->seedUser(1, 'peer-to-delete');
        $otherId = $this->seedUser(1, 'peer-to-keep');
        $guestId = $this->seedUser(2, 'guest-to-keep');
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/' . $peerId);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseMissing('ip_users', ['user_id' => $peerId]);
        $this->assertDatabaseHas('ip_users', ['user_id' => $otherId]);
        $this->assertDatabaseHas('ip_users', ['user_id' => $guestId]);
        $this->assertDatabaseHas('ip_users', ['user_id' => 1]);
    }

    #[Test]
    public function it_refuses_to_let_even_the_primary_admin_delete_the_primary_admin(): void
    {
        /* Arrange */
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete/1');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users');
        $this->assertDatabaseHas('ip_users', ['user_id' => 1]);
    }

    #[Test]
    public function it_ends_the_open_session_of_an_admin_who_was_deleted(): void
    {
        /* Arrange: the peer has a live, valid session captured before the delete */
        $peerId = $this->seedUser(1, 'peer-live-session');
        $this->actingAsAdmin($peerId);
        $peerSession = $this->sessionData;
        $before      = $this->get('/dashboard');
        self::assertSame(200, $before->statusCode(), 'Positive control: the peer session works before the delete.');

        $this->actingAsAdmin(1);
        $deleted = $this->postWithValidCsrfToken('/users/delete/' . $peerId);
        $this->assertResponseRedirectsToRoute($deleted, 'users');

        /* Act: replay the stale session */
        $this->sessionData = $peerSession;
        $after             = $this->get('/dashboard');

        /* Assert */
        $this->assertResponseRedirectsToRoute($after, 'sessions/login');
    }

    private function seedUser(int $type, string $name): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'          => $type,
            'user_name'          => $name,
            'user_email'         => $name . '@test.local',
            'user_password'      => password_hash('secret-' . $name, PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(8)),
            'user_language'      => 'system',
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
