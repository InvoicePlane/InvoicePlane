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
#[CoversClass(Tests\Feature\Clients\ClientsAjaxModal::class)]

class ClientsAjaxModalTest extends AbstractTestCase
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
