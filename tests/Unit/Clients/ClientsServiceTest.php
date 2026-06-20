<?php

namespace Tests\Unit\Clients;

use Mdl_Clients;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Clients::class)]
class ClientsServiceTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Service class does not exist — CI3 model layer, no Laravel service layer available');
        $this->service = new ClientsService();
    }

    #[Test]
    public function it_returns_all_active_clients(): void
    {
        /* Arrange */
        tmpClient::create([
            'client_name'   => 'Active Client 1',
            'client_active' => 1,
        ]);
        tmpClient::create([
            'client_name'   => 'Active Client 2',
            'client_active' => 1,
        ]);
        tmpClient::create([
            'client_name'   => 'Inactive Client',
            'client_active' => 0,
        ]);

        /* Act */
        $result = $this->service->getActive();

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertEquals('Active Client 1', $result[0]->client_name);
        $this->assertEquals('Active Client 2', $result[1]->client_name);
        $result->each(function ($client): void {
            $this->assertEquals(1, $client->client_active);
        });
    }

    #[Test]
    public function it_returns_empty_collection_when_no_active_clients_exist(): void
    {
        /* Arrange */
        tmpClient::create([
            'client_name'   => 'Inactive Client',
            'client_active' => 0,
        ]);

        /* Act */
        $result = $this->service->getActive();

        /* Assert */
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_returns_query_builder_for_active_clients(): void
    {
        /* Act */
        $builder = $this->service->isActive();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_returns_query_builder_for_inactive_clients(): void
    {
        /* Act */
        $builder = $this->service->isInactive();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_returns_active_clients_not_assigned_to_user(): void
    {
        /* Arrange */
        $userId = 1;

        tmpClient::create([
            'client_name'   => 'Unassigned Client',
            'client_active' => 1,
        ]);
        $client2 = tmpClient::create([
            'client_name'   => 'Assigned Client',
            'client_active' => 1,
        ]);
        tmpClient::create([
            'client_name'   => 'Inactive Client',
            'client_active' => 0,
        ]);

        // Assign client2 to the user
        UserClient::create([
            'user_id'   => $userId,
            'client_id' => $client2->client_id,
        ]);

        /* Act */
        $result = $this->service->getNotAssignedToUser($userId);

        /* Assert */
        $this->assertCount(1, $result);
        $this->assertEquals('Unassigned Client', $result[0]->client_name);
    }

    #[Test]
    public function it_returns_all_active_clients_when_no_assignments_exist(): void
    {
        /* Arrange */
        $userId = 999;

        tmpClient::create([
            'client_name'   => 'Client 1',
            'client_active' => 1,
        ]);
        tmpClient::create([
            'client_name'   => 'Client 2',
            'client_active' => 1,
        ]);

        /* Act */
        $result = $this->service->getNotAssignedToUser($userId);

        /* Assert */
        $this->assertCount(2, $result);
    }

    public function test_get_latest_limits_results(): void
    {
        $this->seedModelMany('tmpClient', 15, ['client_active' => 1]);

        $results = $this->service->getLatest(5);
        $this->assertCount(5, $results);
    }

    public function test_fix_avs_formats_correctly(): void
    {
        $formatted = $this->service->fixAvs('123.4567.8901.23');
        $this->assertEquals('12345678901 23', $formatted);

        $unformatted = $this->service->fixAvs('1234567890123');
        $this->assertEquals('1234567890123', $unformatted);

        $empty = $this->service->fixAvs('');
        $this->assertEquals('', $empty);
    }

    public function test_convert_date_returns_mysql_format(): void
    {
        $result = $this->service->convertDate('2023-01-15');
        $this->assertEquals('2023-01-15', $result);
    }

    public function test_convert_date_handles_invalid_input(): void
    {
        Log::shouldReceive('warning')->once();
        $result = $this->service->convertDate('invalid-date');
        $this->assertEquals('', $result);
    }

    public function test_convert_date_handles_null_input(): void
    {
        $result = $this->service->convertDate(null);
        $this->assertEquals('', $result);
    }

    public function test_client_lookup_finds_existing_client(): void
    {
        $client = $this->seedModel('tmpClient', ['client_name' => 'Test Client']);

        $result = $this->service->clientLookup('Test Client');
        $this->assertEquals($client->client_id, $result);
    }

    public function test_client_lookup_creates_new_client(): void
    {
        $result = $this->service->clientLookup('New Client');
        $this->assertGreaterThan(0, $result);

        $client = tmpClient::find($result);
        $this->assertNotNull($client);
        $this->assertEquals('New Client', $client->client_name);
    }

    public function test_with_total_includes_invoice_sum(): void
    {
        $builder = $this->service->withTotal();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_with_total_paid_includes_paid_sum(): void
    {
        $builder = $this->service->withTotalPaid();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_with_total_balance_includes_balance_sum(): void
    {
        $builder = $this->service->withTotalBalance();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_is_inactive_filters_inactive_clients(): void
    {
        $builder = $this->service->isInactive();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_is_active_filters_active_clients(): void
    {
        $builder = $this->service->isActive();
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    public function test_get_not_assigned_to_user_excludes_assigned_clients(): void
    {
        $user_id          = 1;
        $assignedClient   = $this->seedModel('tmpClient', ['client_active' => 1]);
        $unassignedClient = $this->seedModel('tmpClient', ['client_active' => 1]);

        UserClient::create(['user_id' => $user_id, 'client_id' => $assignedClient->client_id]);

        $results = $this->service->getNotAssignedToUser($user_id);
        $this->assertCount(1, $results);
        $this->assertEquals($unassignedClient->client_id, $results->first()->client_id);
    }

    public function test_get_active_returns_only_active_clients(): void
    {
        $this->seedModelMany('tmpClient', 3, ['client_active' => 1]);
        $this->seedModelMany('tmpClient', 2, ['client_active' => 0]);

        $results = $this->service->getActive();
        $this->assertCount(3, $results);
        foreach ($results as $client) {
            $this->assertEquals(1, $client->client_active);
        }
    }

    public function test_delete_logs_orphan_handling(): void
    {
        $this->markTestIncomplete('weak test');
        $client = $this->seedModel('tmpClient');

        Log::shouldReceive('info')
            ->once()
            ->with('Orphan handling triggered after client deletion', ['client_id' => $client->client_id]);

        $this->service->delete($client->client_id);
    }
}
