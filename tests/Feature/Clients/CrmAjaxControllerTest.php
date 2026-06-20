<?php

namespace Tests\Feature\Clients;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ClientsController Feature Tests.
 *
 * Comprehensive test coverage for client management including CRUD operations,
 * status filtering, and relationship handling.
 */
#[CoversClass(ClientsController::class)]
#[CoversClass(Tests\Feature\Clients\CrmAjaxController::class)]

class CrmAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Requires Laravel service layer — not available in CI3');
    }
    use InteractsWithDatabase;

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
        $response = $this->actingAs($user)->get(route('crm.ajax.modal_client_lookup'));

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
        $response = $this->actingAs($user)->get(route('crm.ajax.modal_client_lookup'));

        /* Assert */
        $clients = $response->viewData('clients');
        $names   = $clients->pluck('client_name')->toArray();

        $this->assertEquals('Alpha Inc', $names[0]);
        $this->assertEquals('Beta LLC', $names[1]);
        $this->assertEquals('Zebra Corp', $names[2]);
    }

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
        $response = $this->actingAs($user)->get(route('crm.ajax.get_client_details', ['clientId' => $client->client_id]));

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
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_for_non_existent_client(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('crm.ajax.get_client_details', ['clientId' => 99999]));

        /* Assert */
        $response->assertNotFound();
    }
}
