<?php

namespace Tests\Feature\Clients;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * User_Clients controller — application/modules/user_clients/controllers/User_clients.php.
 *
 * A user-client row assigns a client to a (non-admin) user. Required fields
 * (Mdl_User_Clients::validation_rules): user_id, client_id. There is no edit
 * action — the row is created or deleted. Routes:
 *   list   GET  /user_clients/user/{user_id}
 *   create POST /user_clients/create/{user_id}      -> /user_clients/user/{user_id}
 *   delete POST /user_clients/delete/{user_client_id} -> /user_clients/user/{user_id}
 * delete is admin-only (Mdl_User_Clients::can_user_manage). Absorbs
 * UserClientsServiceTest and Issue1694UserClientsDeleteCsrfTest.
 */
#[Group('user_clients')]
#[CoversClass(\User_Clients::class)]
class UserClientsControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_client_assigned_to_a_user(): void
    {
        /* Arrange */
        $userId  = $this->seedSecondaryUser();
        $clientA = $this->seedClient(['client_name' => 'Assigned Client One']);
        $clientB = $this->seedClient(['client_name' => 'Assigned Client Two']);
        $this->seedAssignment($userId, $clientA);
        $this->seedAssignment($userId, $clientB);

        /* Act */
        $response = $this->get('/user_clients/user/' . $userId);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Assigned Client One');
        $this->assertResponseBodyContains($response, 'Assigned Client Two');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_assigns_a_client_to_a_user(): void
    {
        /* Arrange */
        $userId   = $this->seedSecondaryUser();
        $clientId = $this->seedClient(['client_name' => 'Freshly Assigned Client']);

        /* Act */
        $response = $this->post('/user_clients/create/' . $userId, [
            'user_id'    => (string) $userId,
            'client_id'  => (string) $clientId,
            'btn_submit' => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful assignment redirects to the user client list.');
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_assign_without_client_id(): void
    {
        /* Arrange */
        $userId = $this->seedSecondaryUser();

        /* Act */
        $response = $this->post('/user_clients/create/' . $userId, [
            'user_id'    => (string) $userId,
            'client_id'  => '',
            'btn_submit' => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid assignment must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $userId]);
    }

    #[Test]
    public function it_fails_to_assign_without_user_id(): void
    {
        /* Arrange */
        $userId   = $this->seedSecondaryUser();
        $clientId = $this->seedClient(['client_name' => 'Orphan Assignment Client']);

        /* Act */
        $response = $this->post('/user_clients/create/' . $userId, [
            'user_id'    => '',
            'client_id'  => (string) $clientId,
            'btn_submit' => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid assignment must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_user_clients', ['client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_unassigns_a_client_from_a_user(): void
    {
        /* Arrange */
        $userId   = $this->seedSecondaryUser();
        $clientId = $this->seedClient();
        $keptId   = $this->seedClient();
        $id       = $this->seedAssignment($userId, $clientId);
        $keep     = $this->seedAssignment($userId, $keptId);

        /* Act */
        $response = $this->post('/user_clients/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A successful unassign redirects to the user client list.');
        $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $id]);
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_unassigns_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $userId = $this->seedSecondaryUser();
        $id     = $this->seedAssignment($userId, $this->seedClient());

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/delete/' . $id);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A valid-token unassign redirects.');
        $this->assertDatabaseMissing('ip_user_clients', ['user_client_id' => $id]);
    }

    #[Test]
    public function it_does_not_unassign_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $userId = $this->seedSecondaryUser();
        $id     = $this->seedAssignment($userId, $this->seedClient());

        /* Act */
        $response = $this->postWithoutCsrfToken('/user_clients/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less unassign must not reach the controller.');
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_blocks_a_non_admin_from_unassigning_a_client(): void
    {
        /* Arrange */
        $ownerId = $this->seedSecondaryUser();
        $id      = $this->seedAssignment($ownerId, $this->seedClient());
        $this->actingAs(['user_id' => $ownerId, 'user_type' => 2, 'user_email' => 'assignee-nonadmin@test.local']);

        /* Act */
        $response = $this->post('/user_clients/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'A non-admin is bounced off the admin controller entirely.');
        $this->assertDatabaseHas('ip_user_clients', ['user_client_id' => $id]);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_assignment(): void
    {
        /* Arrange */
        $userId   = $this->seedSecondaryUser();
        $clientId = $this->seedClient(['client_name' => 'Secret Assigned Client']);
        $this->seedAssignment($userId, $clientId);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/user_clients/user/' . $userId);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Assigned Client');
    }

    private function seedSecondaryUser(): int
    {
        // Delegate to the shared seedModel() row-builder instead of
        // duplicating its ip_users defaults here; only the type differs.
        return (int) $this->seedModel('User', ['user_type' => 2])->user_id;
    }

    private function seedAssignment(int $userId, int $clientId): int
    {
        return $this->databaseInsert('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);
    }
}
