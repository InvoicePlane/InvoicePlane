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
        /* Arrange */
        $targetId = $this->seedAdmin();
        $clientId = $this->seedClient();
        $this->actingAsAdmin(1);

        /* Act */
        $response = $this->postWithValidCsrfToken('/user_clients/create/' . $targetId, [
            'user_id'          => $targetId,
            'client_id'        => $clientId,
            'user_all_clients' => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'user_clients/user/' . $targetId);
        $this->assertDatabaseHas('ip_users', ['user_id' => $targetId, 'user_all_clients' => 1]);
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
