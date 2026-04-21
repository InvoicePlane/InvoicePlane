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
