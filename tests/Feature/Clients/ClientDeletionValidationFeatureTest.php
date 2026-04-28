<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
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
#[CoversClass(Tests\Feature\Clients\ClientDeletionValidationFeature::class)]

class ClientDeletionValidationFeatureTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test that client without related records can be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_client_without_related_records(): void
    {
        /* Arrange */
        $client = $this->seedModel('Client', [
            'client_name' => 'Deletable Client',
        ]);

        /* Act */
        $response = $this->post('/clients/delete/' . (int) $client->client_id);

        /* Assert */
        $response->assertRedirect('/clients');
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModel('Invoice', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->post('/clients/delete/' . (int) $client->client_id);

        /* Assert */
        $response->assertRedirect('/clients');
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModel('Quote', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->post('/clients/delete/' . (int) $client->client_id);

        /* Assert */
        $response->assertRedirect('/clients');
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
        $response = $this->post('/clients/delete/' . (int) $client->client_id);

        /* Assert */
        $response->assertRedirect('/clients');
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModelMany('Invoice', 2, ['client_id' => $client->client_id]);
        $this->seedModelMany('Quote', 3, ['client_id' => $client->client_id]);
        $this->seedModel('Project', ['client_id' => $client->client_id]);

        /* Act */
        $response = $this->post('/clients/delete/' . (int) $client->client_id);

        /* Assert */
        $response->assertRedirect('/clients');
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
        /* Arrange */
        $invalidId = -1;

        /* Act */
        $response = $this->post('/clients/delete/' . $invalidId);

        /* Assert */
        $response->assertRedirect('/clients');
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
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $response = $this->post('/clients/delete/' . $nonexistentId);

        /* Assert */
        $response->assertRedirect('/clients');
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $invoice = $this->seedModel('Invoice', ['client_id' => $client->client_id]);
        $quote   = $this->seedModel('Quote', ['client_id' => $client->client_id]);

        // Initially cannot delete
        $response1 = $this->post('/clients/delete/' . (int) $client->client_id);
        $response1->assertSessionHas('alert_error');

        // Remove related records
        $invoice->delete();
        $quote->delete();

        /* Act */
        $response2 = $this->post('/clients/delete/' . (int) $client->client_id);

        /* Assert */
        $response2->assertRedirect('/clients');
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_clients', ['client_id' => $client->client_id]);
    }
}
