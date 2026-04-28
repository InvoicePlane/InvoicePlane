<?php

namespace Tests\Feature\Clients;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(ClientsController::class)]
#[CoversClass(Tests\Feature\Clients\ClientsAjaxDetails::class)]

class ClientsAjaxDetailsTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    // ==================== ROUTE: GET /crm/ajax/get_client_details/{clientId} ====================

    /**
     * Test getClientDetails returns client as JSON.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_client_details_as_json(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client', [
            'client_name'  => 'Test Client',
            'client_email' => 'test@client.com',
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/crm/ajax/get_client_details/' . (int) $client->client_id);

        /* Assert */
        $response->assertOk();
        $response->assertJson([
            'client_id'    => $client->client_id,
            'client_name'  => 'Test Client',
            'client_email' => 'test@client.com',
        ]);
    }

    /**
     * Test getClientDetails returns 404 for non-existent client.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_returns_404_for_non_existent_client(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/crm/ajax/get_client_details/' . 99999);

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test getClientDetails requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_get_client_details(): void
    {
        /* Arrange */
        $client = $this->seedModel('Client');

        /* Act */
        $response = $this->get('/crm/ajax/get_client_details/' . (int) $client->client_id);

        /* Assert */
        $response->assertRedirect('/sessions/login');
    }

    /**
     * Test getClientDetails returns all expected fields.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_all_client_fields(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client', [
            'client_name'      => 'Complete Client',
            'client_email'     => 'complete@test.com',
            'client_phone'     => '123-456-7890',
            'client_address_1' => '123 Main St',
            'client_city'      => 'Test City',
            'client_state'     => 'TS',
            'client_zip'       => '12345',
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/crm/ajax/get_client_details/' . (int) $client->client_id);

        /* Assert */
        $response->assertOk();
        $response->assertJsonStructure([
            'client_id',
            'client_name',
            'client_email',
            'client_phone',
            'client_address_1',
            'client_city',
            'client_state',
            'client_zip',
        ]);
    }

    /**
     * Test getClientDetails with inactive client.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_returns_details_for_inactive_client(): void
    {
        /* Arrange */
        $user           = $this->seedModel('User');
        $inactiveClient = $this->seedModel('Client', [
            'client_active' => 0,
            'client_name'   => 'Inactive Client',
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/crm/ajax/get_client_details/' . (int) $inactiveClient->client_id);

        /* Assert */
        // Should still return details even for inactive clients
        $response->assertOk();
        $response->assertJson([
            'client_id'     => $inactiveClient->client_id,
            'client_active' => 0,
        ]);
    }

    /**
     * Test getClientDetails handles null/empty fields.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_null_fields_in_client_details(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client', [
            'client_name'      => 'Minimal Client',
            'client_email'     => null,
            'client_phone'     => null,
            'client_address_1' => null,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/crm/ajax/get_client_details/' . (int) $client->client_id);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals('Minimal Client', $data['client_name']);
        // Null fields should be handled gracefully
    }
}
