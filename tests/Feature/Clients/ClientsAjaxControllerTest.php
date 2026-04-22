<?php

namespace Tests\Feature\Clients;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(ClientsController::class)]

class ClientsAjaxControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    // ==================== ROUTE: GET /clients/ajax/modal_client_lookup ====================

    /**
     * Test modalClientLookup displays active clients.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_modal_with_active_clients(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        $activeClient   = $this->seedModel('Client', ['client_active' => 1, 'client_name' => 'Active Client']);
        $inactiveClient = $this->seedModel('Client', ['client_active' => 0, 'client_name' => 'Inactive Client']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::modal_client_lookup');
        $response->assertViewHas('clients');

        $clients   = $response->viewData('clients');
        $clientIds = $clients->pluck('client_id')->toArray();

        $this->assertContains($activeClient->client_id, $clientIds);
        $this->assertNotContains($inactiveClient->client_id, $clientIds);
    }

    /**
     * Test clients are ordered alphabetically by name.
     */
    #[Test]
    public function it_orders_clients_alphabetically_in_modal(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('Client', ['client_active' => 1, 'client_name' => 'Zebra Corp']);
        $this->seedModel('Client', ['client_active' => 1, 'client_name' => 'Alpha Inc']);
        $this->seedModel('Client', ['client_active' => 1, 'client_name' => 'Beta LLC']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /* Assert */
        $clients = $response->viewData('clients');
        $names   = $clients->pluck('client_name')->toArray();

        $this->assertEquals('Alpha Inc', $names[0]);
        $this->assertEquals('Beta LLC', $names[1]);
        $this->assertEquals('Zebra Corp', $names[2]);
    }

    /**
     * Test modal client lookup requires authentication.
     */
    #[Group('auth')]
    #[Test]
    public function it_requires_authentication_for_modal_client_lookup(): void
    {
        /* Act */
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test modal displays empty state when no active clients.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_displays_empty_modal_when_no_active_clients(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        // All clients are inactive
        $this->seedModelMany('Client', 3, ['client_active' => 0]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /* Assert */
        $response->assertOk();
        $clients = $response->viewData('clients');
        $this->assertCount(0, $clients);
    }

    /**
     * Test modal handles special characters in client names.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_special_characters_in_client_names(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModel('Client', [
            'client_active' => 1,
            'client_name'   => "O'Brien & Associates <script>alert('xss')</script>",
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /* Assert */
        $response->assertOk();
        $clients = $response->viewData('clients');
        $this->assertGreaterThan(0, $clients->count());
        // Client name should be in results (will be escaped on output)
    }

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
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => $client->client_id]));

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
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => 99999]));

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
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => $client->client_id]));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test getClientDetails with invalid ID type.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_invalid_client_id_type(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => 'invalid']));

        /* Assert */
        // Should either return 404 or handle gracefully
        $this->assertTrue(
            $response->isNotFound()
            || $response->getStatusCode() >= 400
        );
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
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => $client->client_id]));

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
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => $inactiveClient->client_id]));

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
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => $client->client_id]));

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals('Minimal Client', $data['client_name']);
        // Null fields should be handled gracefully
    }

    /**
     * Test getClientDetails with negative ID.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_negative_client_id(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => -1]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test getClientDetails with zero ID.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_zero_client_id(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => 0]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test modal pagination with many clients.
     */
    #[Group('edge-cases')]
    #[Test]
    public function it_handles_pagination_with_many_active_clients(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        // Create 100 active clients
        $this->seedModelMany('Client', 100, ['client_active' => 1]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /* Assert */
        $response->assertOk();
        $clients = $response->viewData('clients');
        // Should return all clients or handle pagination
        $this->assertGreaterThan(0, $clients->count());
    }
}
