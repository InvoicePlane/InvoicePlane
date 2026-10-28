<?php

namespace Tests\Feature\Users;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

final class UserClientsIdorTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    #[Test]
    public function it_denies_a_secondary_admin_creating_a_client_mapping_for_another_user(): void
    {
        /* Arrange */
        $secondaryId = $this->seedAdmin();
        $victimId    = $this->seedAdmin();
        $clientId    = $this->seedClient();
        $this->actingAsAdmin($secondaryId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $victimId, [
            'user_id'   => $victimId,
            'client_id' => $clientId,
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $victimId]);
    }

    #[Test]
    public function it_does_not_let_a_fractional_user_id_pass_the_self_check_and_land_on_the_next_user(): void
    {
        /* Arrange: user_id "S.5" is authorised as S by the (int) cast, but a "2.5" in the INT column rounds to the next user */
        $secondaryId = $this->seedAdmin();
        $victimId    = $this->seedAdmin();
        $clientId    = $this->seedClient();
        $this->actingAsAdmin($secondaryId);

        /* Act */
        $response = $this->request('POST', '/users/ajax/save_user_client', [], [
            'user_id'   => $secondaryId . '.5',
            'client_id' => (string) $clientId,
            '_ip_csrf'  => $this->csrfToken,
        ], true, ['ip_csrf_cookie' => $this->csrfToken]);

        /* Assert */
        self::assertSame(403, $response->statusCode(), 'A non-integer user id must be refused. Body: ' . $response->body());
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $victimId]);
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $secondaryId, 'client_id' => $clientId]);
    }

    #[Test]
    public function it_denies_a_secondary_admin_saving_a_client_for_another_user_through_the_users_ajax_endpoint(): void
    {
        /* Arrange: GHSA-5rqm-w9p8-gp7w -- Users/Ajax::save_user_client() must apply the same self-or-primary-admin rule */
        $secondaryId = $this->seedAdmin();
        $victimId    = $this->seedAdmin();
        $clientId    = $this->seedClient();
        $this->actingAsAdmin($secondaryId);

        /* Act */
        $response = $this->request('POST', '/users/ajax/save_user_client', [], [
            'user_id'   => (string) $victimId,
            'client_id' => (string) $clientId,
            '_ip_csrf'  => $this->csrfToken,
        ], true, ['ip_csrf_cookie' => $this->csrfToken]);

        /* Assert */
        self::assertSame(403, $response->statusCode(), 'Body: ' . $response->body());
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $victimId]);
    }

    #[Test]
    public function it_allows_an_admin_to_save_a_client_for_their_own_account_through_the_users_ajax_endpoint(): void
    {
        /* Arrange: positive control */
        $secondaryId = $this->seedAdmin();
        $clientId    = $this->seedClient();
        $this->actingAsAdmin($secondaryId);

        /* Act */
        $response = $this->request('POST', '/users/ajax/save_user_client', [], [
            'user_id'   => (string) $secondaryId,
            'client_id' => (string) $clientId,
            '_ip_csrf'  => $this->csrfToken,
        ], true, ['ip_csrf_cookie' => $this->csrfToken]);

        /* Assert */
        self::assertNotSame(403, $response->statusCode(), 'Body: ' . $response->body());
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $secondaryId, 'client_id' => $clientId]);
    }

    #[Test]
    public function it_still_collects_clients_in_the_session_while_a_new_user_has_no_id_yet(): void
    {
        /* Arrange: the new-user form has no user_id, so the integer guard must not apply to it */
        $clientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->request('POST', '/users/ajax/save_user_client', [], [
            'user_id'   => '',
            'client_id' => (string) $clientId,
            '_ip_csrf'  => $this->csrfToken,
        ], true, ['ip_csrf_cookie' => $this->csrfToken]);

        /* Assert */
        self::assertNotSame(403, $response->statusCode(), 'An empty user id is the new-user flow, not an invalid id. Body: ' . $response->body());
        self::assertSame([$clientId => $clientId], array_map('intval', (array) $response->sessionValue('user_clients')));
    }

    #[Test]
    public function it_allows_the_primary_admin_to_create_a_client_mapping_for_another_user(): void
    {
        /* Arrange */
        $targetId = $this->seedAdmin();
        $clientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $targetId, [
            'user_id'   => $targetId,
            'client_id' => $clientId,
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'user_clients/user/' . $targetId);
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $targetId, 'client_id' => $clientId]);
    }

    /** GHSA-h4xh-4jwc-485r: a user-client mapping may only be deleted by its owner or the primary administrator. */
    #[Test]
    public function it_denies_a_secondary_admin_deleting_another_users_client_mapping(): void
    {
        /* Arrange */
        $secondaryId = $this->seedAdmin();
        $victimId    = $this->seedAdmin();
        $clientId    = $this->seedClient();
        $mappingId   = $this->databaseInsert('ip_user_clients', ['user_id' => $victimId, 'client_id' => $clientId]);
        $this->actingAsAdmin($secondaryId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/delete/' . $mappingId);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $mappingId]);
    }

    /** GHSA-h4xh-4jwc-485r: user_all_clients exposes every client, so only the primary administrator may grant it. */
    #[Test]
    public function it_denies_a_secondary_admin_granting_themselves_all_clients(): void
    {
        /* Arrange */
        $secondaryId = $this->seedAdmin();
        $clientId    = $this->seedClient();
        $this->actingAsAdmin($secondaryId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $secondaryId, [
            'user_id'          => $secondaryId,
            'client_id'        => $clientId,
            'user_all_clients' => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseHas('ip_users', ['user_id' => $secondaryId, 'user_all_clients' => 0]);
    }

    #[Test]
    public function it_allows_the_primary_admin_to_grant_all_clients(): void
    {
        /* Arrange: a second, unrelated client exists that is never named in the request —
         * only set_all_clients_user() would assign it, a plain create() call would not. */
        $targetId        = $this->seedAdmin();
        $namedClientId   = $this->seedClient();
        $unnamedClientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $targetId, [
            'user_id'          => $targetId,
            'client_id'        => $namedClientId,
            'user_all_clients' => '1',
        ]);

        /* Assert: the flag is set AND every client was actually assigned, proving
         * set_all_clients_user() ran rather than just the ip_users flag update. */
        $this->assertResponseRedirectsToRoute($response, 'user_clients/user/' . $targetId);
        $this->assertDatabaseHas('ip_users', ['user_id' => $targetId, 'user_all_clients' => 1]);
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $targetId, 'client_id' => $unnamedClientId]);
    }

    #[Test]
    public function it_does_not_grant_all_clients_when_only_a_single_client_is_requested(): void
    {
        /* Arrange: a second, unrelated client exists. A plain single-client assignment must
         * never pull it in — only an explicit user_all_clients=1 request may. */
        $targetId        = $this->seedAdmin();
        $namedClientId   = $this->seedClient();
        $unnamedClientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $targetId, [
            'user_id'   => $targetId,
            'client_id' => $namedClientId,
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'user_clients/user/' . $targetId);
        $this->assertDatabaseHas('ip_users', ['user_id' => $targetId, 'user_all_clients' => 0]);
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $targetId, 'client_id' => $namedClientId]);
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $targetId, 'client_id' => $unnamedClientId]);
    }

    #[Test]
    public function it_does_not_clobber_an_unrelated_users_all_clients_flag(): void
    {
        /*
         * Regression for a missing $this->db->where('user_id', ...) before the ip_users
         * update in create(): without it, CodeIgniter's query builder would run the update
         * against every row in ip_users, silently resetting every other user's
         * user_all_clients flag to 0.
         */

        /* Arrange */
        $bystanderId = $this->seedAdmin();
        $this->databaseInsert('ip_user_clients', ['user_id' => $bystanderId, 'client_id' => $this->seedClient()]);
        $this->databaseUpdate('ip_users', ['user_all_clients' => 1], ['user_id' => $bystanderId]);

        $targetId = $this->seedAdmin();
        $clientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $targetId, [
            'user_id'   => $targetId,
            'client_id' => $clientId,
        ]);

        /* Assert: the target user's own (non-all-clients) update must not leak onto the
         * bystander's unrelated row. */
        $this->assertResponseRedirectsToRoute($response, 'user_clients/user/' . $targetId);
        $this->assertDatabaseHas('ip_users', ['user_id' => $targetId, 'user_all_clients' => 0]);
        $this->assertDatabaseHas('ip_users', ['user_id' => $bystanderId, 'user_all_clients' => 1]);
    }

    #[Test]
    public function it_rejects_a_non_integer_shaped_user_id_in_the_create_url(): void
    {
        /*
         * Regression for a CodeRabbit finding ported from upstream PR #1747's own hardening
         * commit (6c40b66e): a non-integer-shaped id such as "2.9" would authorize against
         * user 2 via the (int) cast in the authorization check, while the raw, uncast value
         * is what actually reaches $this->db->where('user_id', $user_id) and the model's
         * save() — a type-confusion gap between what's authorized and what's written.
         */

        /* Arrange */
        $clientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/2.9', [
            'user_id'   => '2.9',
            'client_id' => $clientId,
        ]);

        /* Assert: rejected outright, not silently truncated to user 2. */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseMissing('ip_user_clients', ['client_id' => $clientId]);
    }

    #[Test]
    public function it_rejects_a_non_integer_shaped_url_id_even_with_no_posted_user_id(): void
    {
        /*
         * The same URL-id guard as above, isolated: omitting the posted user_id field
         * entirely means the defense-in-depth posted-user_id check (which also rejects
         * non-integer shapes) never runs, since it is itself gated on the field being
         * present. The ctype_digit() guard on the URL id must reject this on its own.
         */

        /* Arrange */
        $clientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/2.9', [
            'client_id' => $clientId,
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseMissing('ip_user_clients', ['client_id' => $clientId]);
    }

    #[Test]
    public function it_rejects_a_non_integer_shaped_posted_user_id_in_the_create_url(): void
    {
        /* Arrange: the URL id is a genuine integer the acting admin may manage, but the
         * POSTed user_id is non-integer-shaped. */
        $targetId = $this->seedAdmin();
        $clientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $targetId, [
            'user_id'   => $targetId . '.9',
            'client_id' => $clientId,
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseMissing('ip_user_clients', ['client_id' => $clientId]);
    }

    #[Test]
    public function it_denies_a_secondary_admin_unassigning_another_users_client_mapping_via_delete_user_client(): void
    {
        /*
         * Regression for Users::delete_user_client(), one of the three endpoints the
         * GHSA-5rqm-w9p8-gp7w fix guards. Unlike the above User_clients::delete() cases,
         * this one has no dedicated authorization test anywhere else — the only existing
         * coverage (CsrfMutationParityTest) acts as the primary admin, which trivially
         * satisfies is_primary_administrator() and never exercises this guard at all.
         */

        /* Arrange */
        $secondaryId = $this->seedAdmin();
        $victimId    = $this->seedAdmin();
        $mappingId   = $this->databaseInsert('ip_user_clients', ['user_id' => $victimId, 'client_id' => $this->seedClient()]);
        $this->actingAsAdmin($secondaryId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete_user_client/' . $victimId . '/' . $mappingId);

        /* Assert */
        $this->assertResponseStatusCode($response, 403);
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $mappingId]);
    }

    #[Test]
    public function it_allows_the_primary_admin_to_unassign_another_users_client_mapping_via_delete_user_client(): void
    {
        /* Arrange: positive control for the test above — the same request, run by the
         * primary admin, must still succeed. */
        $victimId  = $this->seedAdmin();
        $mappingId = $this->databaseInsert('ip_user_clients', ['user_id' => $victimId, 'client_id' => $this->seedClient()]);
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete_user_client/' . $victimId . '/' . $mappingId);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users/form/' . $victimId);
        $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $mappingId]);
    }

    #[Test]
    public function it_allows_a_user_to_unassign_their_own_client_mapping_via_delete_user_client(): void
    {
        /* Arrange: the fix's own-account carve-out — target_user_id === current_user_id
         * must be allowed even for a non-primary admin. */
        $ownerId   = $this->seedAdmin();
        $mappingId = $this->databaseInsert('ip_user_clients', ['user_id' => $ownerId, 'client_id' => $this->seedClient()]);
        $this->actingAsAdmin($ownerId);

        /* Act */
        $response = $this->postWithValidCsrfToken('/users/delete_user_client/' . $ownerId . '/' . $mappingId);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'users/form/' . $ownerId);
        $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $mappingId]);
    }

    private function seedAdmin(): int
    {
        return $this->databaseInsert('ip_users', [
            'user_type'          => 1,
            'user_name'          => 'Admin ' . bin2hex(random_bytes(3)),
            'user_email'         => 'admin+' . bin2hex(random_bytes(4)) . '@test.local',
            'user_password'      => password_hash('secret123', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(8)),
            'user_language'      => 'system',
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);
    }
}
