<?php

namespace Tests\Feature\Users;

use Tests\AbstractTestCase;

class UserClientsIdorTest extends AbstractTestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function secondary_admin_cannot_create_user_client_for_other_users(): void
    {
        // Setup: create primary admin, secondary admin, and victim admin
        $primary_id = $this->create_user(['user_type' => '1', 'user_email' => 'primary@test.local']);
        $secondary_id = $this->create_user(['user_type' => '1', 'user_email' => 'secondary@test.local']);
        $victim_id = $this->create_user(['user_type' => '1', 'user_email' => 'victim@test.local']);

        $client = $this->create_client(['client_name' => 'Test Client']);

        // Act: secondary admin tries to create a user_client mapping for victim
        $this->acting_as_user($secondary_id);
        $response = $this->request('POST', '/user_clients/create/' . $victim_id, [
            'user_id' => $victim_id,
            'client_id' => $client->client_id,
        ]);

        // Assert: should be denied
        $this->assertResponseStatus(403, $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function secondary_admin_cannot_delete_other_users_client_mapping(): void
    {
        $primary_id = $this->create_user(['user_type' => '1', 'user_email' => 'primary@test.local']);
        $secondary_id = $this->create_user(['user_type' => '1', 'user_email' => 'secondary@test.local']);
        $victim_id = $this->create_user(['user_type' => '1', 'user_email' => 'victim@test.local']);

        $client = $this->create_client(['client_name' => 'Test Client']);

        // Create a client mapping for the victim (as primary admin)
        $this->acting_as_user($primary_id);
        $this->mdl_user_clients->insert(['user_id' => $victim_id, 'client_id' => $client->client_id]);
        $user_client_id = $this->db->insert_id();

        // Act: secondary admin tries to delete victim's mapping
        $this->acting_as_user($secondary_id);
        $response = $this->request('POST', '/user_clients/delete/' . $user_client_id);

        // Assert: should be denied
        $this->assertResponseStatus(403, $response);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function secondary_admin_cannot_escalate_via_user_all_clients(): void
    {
        $primary_id = $this->create_user(['user_type' => '1', 'user_email' => 'primary@test.local']);
        $secondary_id = $this->create_user(['user_type' => '1', 'user_email' => 'secondary@test.local']);

        // Create multiple clients
        $client1 = $this->create_client(['client_name' => 'Client 1']);
        $client2 = $this->create_client(['client_name' => 'Client 2']);

        // Act: secondary admin tries to set user_all_clients flag on themselves
        $this->acting_as_user($secondary_id);
        $response = $this->request('POST', '/user_clients/create/' . $secondary_id, [
            'user_id' => $secondary_id,
            'user_all_clients' => '1',
        ]);

        // Assert: should be denied (only primary admin can set this flag)
        $this->assertResponseStatus(403, $response);

        // Verify flag was not set
        $user = $this->mdl_users->get_by_id($secondary_id)->row();
        $this->assertEquals('0', $user->user_all_clients);
    }
}
