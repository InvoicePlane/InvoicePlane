<?php

namespace Tests\Unit\Clients;

use Modules\Crm\Models\Client;
use Modules\Crm\Services\ClientService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;
use Tests\Concerns\InteractsWithDatabase;

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
    use InteractsWithDatabase;

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
        /* Arrange */
        $client = $this->seedModel('Client', [
            'client_name' => 'Deletable Client',
        ]);

        /* Act */
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModel('Invoice', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModel('Quote', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModel('Project', [
            'client_id' => $client->client_id,
        ]);

        /* Act */
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModelMany('Invoice', 3, [
            'client_id' => $client->client_id,
        ]);

        /* Act */
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $this->seedModelMany('Invoice', 2, ['client_id' => $client->client_id]);
        $this->seedModelMany('Quote', 3, ['client_id' => $client->client_id]);
        $this->seedModel('Project', ['client_id' => $client->client_id]);

        /* Act */
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
        /* Arrange */
        $client = $this->seedModel('Client');

        /* Act */
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
        /* Arrange */
        $client = $this->seedModel('Client');

        $invoice = $this->seedModel('Invoice', ['client_id' => $client->client_id]);
        $quote   = $this->seedModel('Quote', ['client_id' => $client->client_id]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($client->client_id));

        // Remove related records
        $invoice->delete();
        $quote->delete();

        /* Act */
        $canDelete = $this->service->canDelete($client->client_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Client should be deletable after related records removed');
    }
}
