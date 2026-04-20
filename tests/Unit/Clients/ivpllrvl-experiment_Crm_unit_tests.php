<?php

namespace Modules\Crm\Tests\Unit;

use Modules\Crm\Models\Client;
use Modules\Crm\Services\ClientService;
use Modules\Invoices\Models\Invoice;
use Modules\Projects\Models\Project;
use Modules\Quotes\Models\Quote;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

/**
 * ClientService Deletion Validation Tests.
 *
 * Tests business rules for client deletion:
 * - Clients with invoices cannot be deleted
 * - Clients with quotes cannot be deleted
 * - Clients with projects cannot be deleted
 */
#[CoversClass(ClientService::class)]
class ClientDeletionValidationTest extends AbstractServiceTestCase
{
    private ClientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientService();
    }

    /**
     * Test that a client without related records can be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_client_without_related_records(): void
    {
        /** Arrange */
        $client = Client::factory()->create([
            'client_name' => 'Deletable Client',
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($client->client_id);
        $blockers  = $this->service->getDeletionBlockers($client->client_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Client without related records should be deletable');
        $this->assertEquals(0, $blockers['invoices']);
        $this->assertEquals(0, $blockers['quotes']);
        $this->assertEquals(0, $blockers['projects']);
    }

    /**
     * Test that a client with invoices cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_of_client_with_invoices(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Invoice::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($client->client_id);
        $blockers  = $this->service->getDeletionBlockers($client->client_id);

        /* Assert */
        $this->assertFalse($canDelete, 'Client with invoices should NOT be deletable');
        $this->assertGreaterThan(0, $blockers['invoices']);
    }

    /**
     * Test that a client with quotes cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_of_client_with_quotes(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Quote::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($client->client_id);
        $blockers  = $this->service->getDeletionBlockers($client->client_id);

        /* Assert */
        $this->assertFalse($canDelete, 'Client with quotes should NOT be deletable');
        $this->assertGreaterThan(0, $blockers['quotes']);
    }

    /**
     * Test that a client with projects cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_of_client_with_projects(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Project::factory()->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($client->client_id);
        $blockers  = $this->service->getDeletionBlockers($client->client_id);

        /* Assert */
        $this->assertFalse($canDelete, 'Client with projects should NOT be deletable');
        $this->assertGreaterThan(0, $blockers['projects']);
    }

    /**
     * Test that a client with multiple invoices cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_multiple_invoices(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Invoice::factory()->count(3)->create([
            'client_id' => $client->client_id,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($client->client_id);
        $blockers  = $this->service->getDeletionBlockers($client->client_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertEquals(3, $blockers['invoices']);
    }

    /**
     * Test that a client with mixed related records cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_mixed_related_records(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        Invoice::factory()->count(2)->create(['client_id' => $client->client_id]);
        Quote::factory()->count(3)->create(['client_id' => $client->client_id]);
        Project::factory()->create(['client_id' => $client->client_id]);

        /** Act */
        $canDelete = $this->service->canDelete($client->client_id);
        $blockers  = $this->service->getDeletionBlockers($client->client_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertEquals(2, $blockers['invoices']);
        $this->assertEquals(3, $blockers['quotes']);
        $this->assertEquals(1, $blockers['projects']);
    }

    /**
     * Test deletion blockers returns correct structure.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_returns_correct_deletion_blockers_structure(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        /** Act */
        $blockers = $this->service->getDeletionBlockers($client->client_id);

        /* Assert */
        $this->assertIsArray($blockers);
        $this->assertArrayHasKey('invoices', $blockers);
        $this->assertArrayHasKey('quotes', $blockers);
        $this->assertArrayHasKey('projects', $blockers);
    }

    /**
     * Test that client can be deleted after all related records are removed.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_after_related_records_removed(): void
    {
        /** Arrange */
        $client = Client::factory()->create();

        $invoice = Invoice::factory()->create(['client_id' => $client->client_id]);
        $quote   = Quote::factory()->create(['client_id' => $client->client_id]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($client->client_id));

        // Remove related records
        $invoice->delete();
        $quote->delete();

        /** Act */
        $canDelete = $this->service->canDelete($client->client_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Client should be deletable after related records removed');
    }
}

#[CoversClass(ClientService::class)]
class ClientServiceTest extends AbstractServiceTestCase
{
    private ClientService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ClientService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_name', $rules);
        $this->assertArrayHasKey('client_email', $rules);
        $this->assertArrayHasKey('client_phone', $rules);
        $this->assertArrayHasKey('client_active', $rules);
    }
}

