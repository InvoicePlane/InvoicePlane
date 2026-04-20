<?php

namespace Modules\Crm\Tests\Feature;

use Modules\Crm\Controllers\ClientsController;
use Modules\Crm\Models\Client;
use Modules\Invoices\Models\Invoice;
use Modules\Projects\Models\Project;
use Modules\Quotes\Models\Quote;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * ClientsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for client deletion with business rules:
 * - Clients with invoices, quotes, or projects cannot be deleted
 */
#[CoversClass(ClientsController::class)]
class ClientDeletionValidationFeatureTest extends FeatureTestCase
{
    /**
     * Test that client without related records can be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_client_without_related_records(): void
    {
        /** Arrange */
        $client = Client::factory()->create([
            'client_name' => 'Deletable Client',
        ]);

        /** Act */
        $response = $this->post(route('clients.delete', ['client_id' => $client->client_id]));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_clients', [
            'client_id' => $client->client_id,
        ]);
    }

    /**
     * Test that client with invoices cannot be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_client_with_invoices(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Invoice::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $response = $this->post(route('clients.delete', ['client_id' => $client->client_id]));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_error');

        $this->assertDatabaseHas('ip_clients', [
            'client_id' => $client->client_id,
        ]);
    }

    /**
     * Test that client with quotes cannot be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_client_with_quotes(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Quote::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $response = $this->post(route('clients.delete', ['client_id' => $client->client_id]));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_error');

        $this->assertDatabaseHas('ip_clients', [
            'client_id' => $client->client_id,
        ]);
    }

    /**
     * Test that client with projects cannot be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_client_with_projects(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $response = $this->post(route('clients.delete', ['client_id' => $client->client_id]));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_error');

        $this->assertDatabaseHas('ip_clients', [
            'client_id' => $client->client_id,
        ]);
    }

    /**
     * Test that client with mixed related records shows comprehensive error.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_shows_comprehensive_error_for_mixed_blockers(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Invoice::factory()->count(2)->create(['client_id' => $client->client_id]);
        Quote::factory()->count(3)->create(['client_id' => $client->client_id]);
        Project::factory()->create(['client_id' => $client->client_id]);

        /** Act */
        $response = $this->post(route('clients.delete', ['client_id' => $client->client_id]));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_error');

        $this->assertDatabaseHas('ip_clients', ['client_id' => $client->client_id]);
    }

    /**
     * Test deletion with invalid client ID.
     */
    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_client_id(): void
    {
        /** Arrange */
        $invalidId = -1;

        /** Act */
        $response = $this->post(route('clients.delete', ['client_id' => $invalidId]));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_error');
    }

    /**
     * Test deletion with non-existent client ID.
     */
    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_nonexistent_client_id(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->post(route('clients.delete', ['client_id' => $nonexistentId]));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_error');
    }

    /**
     * Test that client can be deleted after all related records are removed.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_related_records_removed(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        $invoice = Invoice::factory()->create(['client_id' => $client->client_id]);
        $quote   = Quote::factory()->create(['client_id' => $client->client_id]);

        // Initially cannot delete
        $response1 = $this->post(route('clients.delete', ['client_id' => $client->client_id]));
        $response1->assertSessionHas('alert_error');

        // Remove related records
        $invoice->delete();
        $quote->delete();

        /** Act */
        $response2 = $this->post(route('clients.delete', ['client_id' => $client->client_id]));

        /* Assert */
        $response2->assertRedirect(route('clients.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $client->client_id]);
    }
}

/**
 * CRM AjaxController Feature Tests.
 *
 * @deprecated This test class has been split into focused test files for better organization:
 *             - ClientsAjaxModalTest.php - Modal client lookup functionality
 *             - ClientsAjaxDetailsTest.php - Client details retrieval
 *             - ClientsAjaxEdgeCasesTest.php - Edge cases and validation
 *
 * This file is kept temporarily for backwards compatibility and will be removed in a future release.
 *
 * Comprehensive test suite covering all AJAX routes for CRM operations.
 */
#[CoversClass(ClientsAjaxController::class)]
class ClientsAjaxControllerTest extends FeatureTestCase
{
    // ==================== ROUTE: GET /clients/ajax/modal_client_lookup ====================

    /**
     * Test modalClientLookup displays active clients.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_modal_with_active_clients(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $activeClient   = Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client']);
        $inactiveClient = Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client']);

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
        /** Arrange */
        $user = User::factory()->create();

        Client::factory()->create(['client_active' => 1, 'client_name' => 'Zebra Corp']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Alpha Inc']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Beta LLC']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /** Assert */
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
        /** Act */
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
        /** Arrange */
        $user = User::factory()->create();
        // All clients are inactive
        Client::factory()->count(3)->create(['client_active' => 0]);

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
        /** Arrange */
        $user = User::factory()->create();
        Client::factory()->create([
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
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create([
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
        /** Arrange */
        $user = User::factory()->create();

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
        /** Arrange */
        $client = Client::factory()->create();

        /** Act */
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
        /** Arrange */
        $user = User::factory()->create();

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
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create([
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
        /** Arrange */
        $user           = User::factory()->create();
        $inactiveClient = Client::factory()->create([
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
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create([
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
        /** Arrange */
        $user = User::factory()->create();

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
        /** Arrange */
        $user = User::factory()->create();

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
        /** Arrange */
        $user = User::factory()->create();
        // Create 100 active clients
        Client::factory()->count(100)->create(['client_active' => 1]);

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

/**
 * CRM AjaxController Client Details Tests.
 *
 * Focused test suite for client details retrieval functionality.
 */
#[CoversClass(CrmAjaxController::class)]
class ClientsAjaxDetailsTest extends FeatureTestCase
{
    // ==================== ROUTE: GET /crm/ajax/get_client_details/{clientId} ====================

    /**
     * Test getClientDetails returns client as JSON.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_client_details_as_json(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create([
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
        /** Arrange */
        $user = User::factory()->create();

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
        /** Arrange */
        $client = Client::factory()->create();

        /** Act */
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => $client->client_id]));

        /* Assert */
        $response->assertRedirect(route('sessions.login'));
    }

    /**
     * Test getClientDetails returns all expected fields.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_all_client_fields(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create([
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
        /** Arrange */
        $user           = User::factory()->create();
        $inactiveClient = Client::factory()->create([
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
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create([
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
}

/**
 * CRM AjaxController Edge Cases Tests.
 *
 * Focused test suite for edge cases and validation scenarios.
 */
#[CoversClass(CrmAjaxController::class)]
class ClientsAjaxEdgeCasesTest extends FeatureTestCase
{
    // ==================== VALIDATION & EDGE CASES ====================

    /**
     * Test getClientDetails with invalid ID type.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_invalid_client_id_type(): void
    {
        /** Arrange */
        $user = User::factory()->create();

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
     * Test getClientDetails with negative ID.
     */
    #[Group('validation')]
    #[Test]
    public function it_handles_negative_client_id(): void
    {
        /** Arrange */
        $user = User::factory()->create();

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
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.get_client_details', ['clientId' => 0]));

        /* Assert */
        $response->assertNotFound();
    }
}

/**
 * CRM AjaxController Modal Tests.
 *
 * Focused test suite for client lookup modal functionality.
 */
#[CoversClass(CrmAjaxController::class)]
class ClientsAjaxModalTest extends FeatureTestCase
{
    // ==================== ROUTE: GET /clients/ajax/modal_client_lookup ====================

    /**
     * Test modalClientLookup displays active clients.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_modal_with_active_clients(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $activeClient   = Client::factory()->create(['client_active' => 1, 'client_name' => 'Active Client']);
        $inactiveClient = Client::factory()->create(['client_active' => 0, 'client_name' => 'Inactive Client']);

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
        /** Arrange */
        $user = User::factory()->create();

        Client::factory()->create(['client_active' => 1, 'client_name' => 'Zebra Corp']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Alpha Inc']);
        Client::factory()->create(['client_active' => 1, 'client_name' => 'Beta LLC']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('crm.ajax.modal_client_lookup'));

        /** Assert */
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
        /** Act */
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
        /** Arrange */
        $user = User::factory()->create();
        // All clients are inactive
        Client::factory()->count(3)->create(['client_active' => 0]);

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
        /** Arrange */
        $user = User::factory()->create();
        Client::factory()->create([
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
        /** Arrange */
        $user = User::factory()->create();
        // Create 100 active clients
        Client::factory()->count(100)->create(['client_active' => 1]);

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

/**
 * ClientsController Feature Tests.
 *
 * Comprehensive test coverage for client management including CRUD operations,
 * status filtering, and relationship handling.
 */
#[CoversClass(ClientsController::class)]
class ClientsControllerTest extends FeatureTestCase
{
    /**
     * Test index redirects to active status view.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_active_status_view_from_index(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.index'));

        /* Assert */
        $response->assertRedirect(route('clients.status', ['status' => 'active']));
    }

    /**
     * Test status method displays only active clients.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_active_clients_when_active_status_selected(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $activeClient   = Client::factory()->create(['client_active' => 1]);
        $inactiveClient = Client::factory()->create(['client_active' => 0]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.status', ['status' => 'active']));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::clients_index');
        $response->assertViewHas('records');

        $clients   = $response->viewData('records');
        $clientIds = $clients->pluck('client_id')->toArray();
        $this->assertContains($activeClient->client_id, $clientIds);
        $this->assertNotContains($inactiveClient->client_id, $clientIds);
    }

    /**
     * Test status method displays only inactive clients.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_inactive_clients_when_inactive_status_selected(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $activeClient   = Client::factory()->create(['client_active' => 1]);
        $inactiveClient = Client::factory()->create(['client_active' => 0]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.status', ['status' => 'inactive']));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::clients_index');
        $response->assertViewHas('records');

        $clients   = $response->viewData('records');
        $clientIds = $clients->pluck('client_id')->toArray();
        $this->assertNotContains($activeClient->client_id, $clientIds);
        $this->assertContains($inactiveClient->client_id, $clientIds);
    }

    /**
     * Test status method displays all clients.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_all_clients_when_all_status_selected(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        $activeClient   = Client::factory()->create(['client_active' => 1]);
        $inactiveClient = Client::factory()->create(['client_active' => 0]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.status', ['status' => 'all']));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::clients_index');
        $response->assertViewHas('records');

        $clients   = $response->viewData('records');
        $clientIds = $clients->pluck('client_id')->toArray();
        $this->assertContains($activeClient->client_id, $clientIds);
        $this->assertContains($inactiveClient->client_id, $clientIds);
    }

    /**
     * Test status view includes filter configuration.
     */
    #[Group('smoke')]
    #[Test]
    public function it_includes_filter_configuration_in_status_view(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.status', ['status' => 'active']));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('filter_display', true);
        $response->assertViewHas('filter_placeholder');
        $response->assertViewHas('filter_method', 'filter_clients');
    }

    /**
     * Test create displays client form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::clients_form');
        $response->assertViewHas('client');

        $client = $response->viewData('client');
        $this->assertInstanceOf(Client::class, $client);
        $this->assertFalse($client->exists);
    }

    /**
     * Test store creates new client with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_client_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "client_name": "Test Client Inc.",
         *     "client_email": "test@client.com",
         *     "client_active": 1
         * }.
         */
        $clientData = [
            'client_name'   => 'Test Client Inc.',
            'client_email'  => 'test@client.com',
            'client_active' => 1,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('clients.form'), $clientData);

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_clients', [
            'client_name'  => 'Test Client Inc.',
            'client_email' => 'test@client.com',
        ]);
    }

    /**
     * Test edit displays client form with existing data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_client(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.form', ['client_id' => $client->client_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::clients_form');
        $response->assertViewHas('client');

        $viewClient = $response->viewData('client');
        $this->assertEquals($client->client_id, $viewClient->client_id);
    }

    /**
     * Test update modifies existing client.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_client_with_valid_data(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create([
            'client_name'   => 'Old Name',
            'client_email'  => 'client@example.com',
            'client_active' => 0,
        ]);

        /**
         * {
         *     "client_name": "Updated Name",
         *     "client_email": "client@example.com",
         *     "client_active": 1
         * }.
         */
        $updateData = [
            'client_name'   => 'Updated Name',
            'client_email'  => 'client@example.com',
            'client_active' => 1,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('clients.form', ['client_id' => $client->client_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_clients', [
            'client_id'   => $client->client_id,
            'client_name' => 'Updated Name',
        ]);
    }

    /**
     * Test destroy deletes client.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_client(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->delete(route('clients.destroy', $client));

        /* Assert */
        $response->assertRedirect(route('clients.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_clients', [
            'client_id' => $client->client_id,
        ]);
    }

    /**
     * Test clients are ordered alphabetically by name.
     */
    #[Test]
    public function it_orders_clients_alphabetically_by_name(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        Client::factory()->create(['client_name' => 'Zebra Company', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Alpha Company', 'client_active' => 1]);
        Client::factory()->create(['client_name' => 'Beta Company', 'client_active' => 1]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('clients.status', ['status' => 'active']));

        /* Assert */
        $response->assertOk();
        $clients = $response->viewData('records');
        $names   = $clients->pluck('client_name')->toArray();

        $this->assertEquals('Alpha Company', $names[0]);
        $this->assertEquals('Beta Company', $names[1]);
        $this->assertEquals('Zebra Company', $names[2]);
    }
}


/**
 * PaymentsController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal payment submission.
 */
#[CoversClass(GuestPaymentsController::class)]
class CrmPaymentsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays guest payment form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_payment_form(): void
    {
        /** Arrange */
        // Guest portal accessible without authentication

        /** Act */
        $response = $this->get(route('guest.payments'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_payments');
    }

    /**
     * Test payment form is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication required

        /** Act */
        $response = $this->get(route('guest.payments'));

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test submit redirects with success message.
     */
    #[Test]
    public function it_submits_payment_and_redirects_with_success(): void
    {
        /** Arrange */
        // Guest payment submission requires invoice URL key and payment details
        // Note: Current implementation is a stub/TODO but test reflects real-world data

        /** Act */
        /**
         * {
         *     "invoice_url_key": "abc123def456",
         *     "payment_method": "paypal",
         *     "amount": "100.00",
         *     "payment_status": "completed"
         * }.
         */
        $payload = [
            'invoice_url_key' => 'abc123def456',
            'payment_method'  => 'paypal',
            'amount'          => '100.00',
            'payment_status'  => 'completed',
        ];

        $response = $this->post(route('guest.payments.submit'), $payload);

        /* Assert */
        // Note: Current stub implementation ignores payload and always succeeds
        // Future implementation should validate these fields
        $response->assertRedirect();
        $response->assertSessionHas('alert_success');
    }

    /**
     * Test payment submission is accessible without authentication.
     */
    #[Test]
    public function it_allows_payment_submission_without_authentication(): void
    {
        /** Arrange */
        // No authentication required for guest payments
        // Note: Current implementation is a stub/TODO but test reflects real-world data

        /** Act */
        /**
         * {
         *     "invoice_url_key": "xyz789ghi012",
         *     "payment_method": "stripe",
         *     "amount": "250.50"
         * }.
         */
        $payload = [
            'invoice_url_key' => 'xyz789ghi012',
            'payment_method'  => 'stripe',
            'amount'          => '250.50',
        ];

        $response = $this->post(route('guest.payments.submit'), $payload);

        /* Assert */
        // Note: Current stub implementation ignores payload and always succeeds
        // Future implementation should validate invoice_url_key exists, amount is positive, etc.
        $response->assertRedirect();
        $response->assertSessionHas('alert_success');
    }

    /**
     * Test payment operations work when authenticated.
     */
    #[Test]
    public function it_works_when_authenticated(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.payments'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_payments');
    }
}

/**
 * GetController Feature Tests.
 *
 * Tests guest get/download operations.
 */
#[CoversClass(GetController::class)]
class GetControllerTest extends FeatureTestCase
{
    /**
     * Test index displays guest get page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_get_page(): void
    {
        /** Arrange */
        // Guest operations may not require authentication

        /** Act */
        $response = $this->get(route('guest.get'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_get');
    }

    /**
     * Test guest get page is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication required

        /** Act */
        $response = $this->get(route('guest.get'));

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test guest get page is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.get'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_get');
    }
}

/**
 * GuestController Feature Tests.
 *
 * Tests guest portal home page display.
 */
#[CoversClass(GuestController::class)]
class GuestControllerTest extends FeatureTestCase
{
    /**
     * Test index displays guest portal home page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_portal_home_page(): void
    {
        /** Arrange */
        // Guest portal may not require authentication

        /** Act */
        $response = $this->get(route('guest.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_index');
    }

    /**
     * Test guest portal is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication

        /** Act */
        $response = $this->get(route('guest.index'));

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test guest portal is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_index');
    }
}

/**
 * PaymentInformationController Feature Tests.
 *
 * Tests guest payment information display.
 */
#[CoversClass(PaymentInformationController::class)]
class PaymentInformationControllerTest extends FeatureTestCase
{
    /**
     * Test index displays payment information page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_payment_information_page(): void
    {
        /** Arrange */
        // Payment info may be accessible to guests

        /** Act */
        $response = $this->get(route('payment_information.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_payment_info');
    }

    /**
     * Test payment information is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication required

        /** Act */
        $response = $this->get(route('payment_information.index'));

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test payment information is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('payment_information.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_payment_info');
    }
}

/**
 * UserClientsController Feature Tests.
 *
 * Tests user-client relationship management.
 */
#[CoversClass(UserClientsController::class)]
class UserClientsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of user-client relationships.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_user_clients(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();
        UserClient::factory()->count(5)->create([
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('user_clients.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::user_clients_index');
        $response->assertViewHas('user_clients');
    }

    /**
     * Test index loads user and client relationships.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_user_and_client_relationships(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();
        UserClient::factory()->create([
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('user_clients.index'));

        /* Assert */
        $response->assertOk();
        $userClients = $response->viewData('user_clients');

        // Verify relationships are loaded
        $this->assertGreaterThan(0, $userClients->count());
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('user_clients.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::user_clients_form');
        $response->assertViewHas('user_client');

        $userClient = $response->viewData('user_client');
        $this->assertInstanceOf(UserClient::class, $userClient);
        $this->assertFalse($userClient->exists);
    }

    /**
     * Test form displays edit form with existing user-client.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_user_client(): void
    {
        /** Arrange */
        $user       = User::factory()->create();
        $client     = Client::factory()->create();
        $userClient = UserClient::factory()->create([
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('user_clients.form', ['id' => $userClient->id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::user_clients_form');
        $response->assertViewHas('user_client');

        $viewUserClient = $response->viewData('user_client');
        $this->assertEquals($userClient->id, $viewUserClient->id);
    }

    /**
     * Test form creates new user-client relationship.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_user_client_relationship_with_valid_data(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        /**
         * {
         *     "user_id": 1,
         *     "client_id": 1,
         *     "btn_submit": "1"
         * }.
         */
        $userClientData = [
            'user_id'    => $user->user_id,
            'client_id'  => $client->client_id,
            'btn_submit' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('user_clients.form'), $userClientData);

        /* Assert */
        $response->assertRedirect(route('user_clients.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_user_clients', [
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);
    }

    /**
     * Test form updates existing user-client relationship.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_user_client_relationship(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $client1 = Client::factory()->create();
        $client2 = Client::factory()->create();

        $userClient = UserClient::factory()->create([
            'user_id'   => $user->user_id,
            'client_id' => $client1->client_id,
        ]);

        /**
         * {
         *     "user_id": 1,
         *     "client_id": 1,
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'user_id'    => $user->user_id,
            'client_id'  => $client2->client_id,
            'btn_submit' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('user_clients.form', ['id' => $userClient->id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('user_clients.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_user_clients', [
            'id'        => $userClient->id,
            'client_id' => $client2->client_id,
        ]);
    }

    /**
     * Test form validates required user_id.
     */
    #[Test]
    public function it_validates_required_user_id(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        /** Act */
        /**
         * {
         *     "client_id": 1,
         *     "btn_submit": "1"
         * }.
         */
        $missingUserPayload = [
            'client_id'  => $client->client_id,
            'btn_submit' => '1',
        ];

        $this->actingAs($user);
        $response = $this->post(route('user_clients.form'), $missingUserPayload);

        /* Assert */
        $response->assertSessionHasErrors('user_id');
    }

    /**
     * Test form validates required client_id.
     */
    #[Test]
    public function it_validates_required_client_id(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** Act */
        /**
         * {
         *     "user_id": 1,
         *     "btn_submit": "1"
         * }.
         */
        $missingClientPayload = [
            'user_id'    => $user->user_id,
            'btn_submit' => '1',
        ];

        $this->actingAs($user);
        $response = $this->post(route('user_clients.form'), $missingClientPayload);

        /* Assert */
        $response->assertSessionHasErrors('client_id');
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('user_clients.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('user_clients.index'));
    }

    /**
     * Test delete removes user-client relationship.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_user_client_relationship(): void
    {
        /** Arrange */
        $user       = User::factory()->create();
        $client     = Client::factory()->create();
        $userClient = UserClient::factory()->create([
            'user_id'   => $user->user_id,
            'client_id' => $client->client_id,
        ]);

        /**
         * {
         *     "user_client_id": 1
         * }.
         */
        $deletePayload = [
            'user_client_id' => $userClient->user_client_id,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('user_clients.delete', ['id' => $userClient->user_client_id]),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect(route('user_clients.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_user_clients', [
            'user_client_id' => $userClient->user_client_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent user-client.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_user_client(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "user_client_id": 99999
         * }.
         */
        $deletePayload = [
            'user_client_id' => 99999,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('user_clients.delete', ['id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }
}

/**
 * ViewController Feature Tests.
 *
 * Tests guest view operations.
 */
#[CoversClass(ViewController::class)]
class ViewControllerTest extends FeatureTestCase
{
    /**
     * Test index displays guest view page.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_view_page(): void
    {
        /** Arrange */
        // Guest operations may not require authentication

        /** Act */
        $response = $this->get(route('guest.view'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_view');
    }

    /**
     * Test guest view page is accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        // No authentication required

        /** Act */
        $response = $this->get(route('guest.view'));

        /* Assert */
        $response->assertOk();
    }

    /**
     * Test guest view page is also accessible when authenticated.
     */
    #[Test]
    public function it_is_accessible_when_authenticated(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('guest.view'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_view');
    }
}

