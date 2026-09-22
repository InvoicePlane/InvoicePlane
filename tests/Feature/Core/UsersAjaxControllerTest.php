<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Users AJAX controller — application/modules/users/controllers/Ajax.php.
 * Primarily tests authorization on endpoints that modify user-related data.
 */
#[Group('users')]
#[CoversClass(\Ajax::class)]
class UsersAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // save_user_client — authorization
    // -------------------------------------------------------------------------

    #[Test]
    public function it_prevents_a_non_primary_admin_from_assigning_clients_to_another_user(): void
    {
        /* Arrange */
        $attackerId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Attacker Admin',
            'user_password'      => password_hash('attacker-secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'attacker-admin@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $victimId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Victim Admin',
            'user_password'      => password_hash('victim-secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'victim-admin@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $clientId = $this->seedClient(['client_name' => 'Test Client']);

        $this->actingAsAdmin($attackerId);

        /* Act */
        $response = $this->ajax('POST', '/users/ajax/save_user_client', [
            'user_id'   => (string) $victimId,
            'client_id' => (string) $clientId,
        ]);

        /* Assert */
        self::assertSame(403, $response->statusCode(), 'Only the primary admin may assign clients to other users.');
        $this->assertDatabaseMissing('ip_user_clients', ['user_id' => $victimId, 'client_id' => $clientId]);
    }

    #[Test]
    public function it_allows_a_user_to_assign_clients_to_themselves(): void
    {
        /* Arrange */
        $userId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Self-Managing User',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'self-manage@test.local',
            'user_type'          => 1,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $clientId = $this->seedClient(['client_name' => 'My Client']);

        $this->actingAsAdmin($userId);

        /* Act */
        $response = $this->ajax('POST', '/users/ajax/save_user_client', [
            'user_id'   => (string) $userId,
            'client_id' => (string) $clientId,
        ]);

        /* Assert */
        self::assertTrue($response->isSuccessful(), 'A user should be able to assign clients to their own account.');
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);
    }

    #[Test]
    public function it_allows_the_primary_admin_to_assign_clients_to_any_user(): void
    {
        /* Arrange */
        // Primary admin is user_id 1 (automatically set by actingAsAdmin())
        $userId = $this->databaseInsert('ip_users', [
            'user_name'          => 'Non-Admin User',
            'user_password'      => password_hash('secret', PASSWORD_DEFAULT),
            'user_psalt'         => bin2hex(random_bytes(10)),
            'user_email'         => 'non-admin@test.local',
            'user_type'          => 2,
            'user_active'        => 1,
            'user_date_created'  => date('Y-m-d H:i:s'),
            'user_date_modified' => date('Y-m-d H:i:s'),
        ]);

        $clientId = $this->seedClient(['client_name' => 'Assigned By Admin']);

        /* Act */
        $response = $this->ajax('POST', '/users/ajax/save_user_client', [
            'user_id'   => (string) $userId,
            'client_id' => (string) $clientId,
        ]);

        /* Assert */
        self::assertTrue($response->isSuccessful(), 'The primary admin should be able to assign clients to any user.');
        $this->assertDatabaseHas('ip_user_clients', ['user_id' => $userId, 'client_id' => $clientId]);
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    protected function seedClient(array $overrides = []): int
    {
        return $this->databaseInsert('ip_clients', array_merge([
            'user_id'             => 1,
            'client_name'         => 'Seed Client ' . bin2hex(random_bytes(3)),
            'client_active'       => 1,
            'client_date_created' => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ], $overrides));
    }
}
