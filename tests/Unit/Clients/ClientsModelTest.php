<?php

namespace Tests\Unit\Clients;

use Mdl_Clients;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;
#[CoversClass(Mdl_Clients::class)]
class ClientsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('clients/mdl_clients');
        $this->model = $this->CI->mdl_clients;
    }

    #[Test]
    public function it_returns_all_active_clients(): void
    {
        /* Arrange */
            'client_name'   => 'Active Client 1',
            'client_active' => 1,
        ]);
            'client_name'   => 'Active Client 2',
            'client_active' => 1,
        ]);
            'client_name'   => 'Inactive Client',
            'client_active' => 0,
        ]);

        /* Act */
        $result = $this->model->getActive();

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
            'client_name'   => 'Inactive Client',
            'client_active' => 0,
        ]);

        /* Act */
        $result = $this->model->getActive();

        /* Assert */
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_returns_query_builder_for_active_clients(): void
    {
        /* Act */
        $builder = $this->model->isActive();

        /* Assert */
    }

    #[Test]
    public function it_returns_query_builder_for_inactive_clients(): void
    {
        /* Act */
        $builder = $this->model->isInactive();

        /* Assert */
    }

    #[Test]
    public function it_returns_active_clients_not_assigned_to_user(): void
    {
        /* Arrange */
        $userId = 1;

            'client_name'   => 'Unassigned Client',
            'client_active' => 1,
        ]);
            'client_name'   => 'Assigned Client',
            'client_active' => 1,
        ]);
            'client_name'   => 'Inactive Client',
            'client_active' => 0,
        ]);

        // Assign client2 to the user
            'user_id'   => $userId,
            'client_id' => $client2->client_id,
        ]);

        /* Act */
        $result = $this->model->getNotAssignedToUser($userId);

        /* Assert */
        $this->assertCount(1, $result);
        $this->assertEquals('Unassigned Client', $result[0]->client_name);
    }

    #[Test]
    public function it_returns_all_active_clients_when_no_assignments_exist(): void
    {
        /* Arrange */
        $userId = 999;

            'client_name'   => 'Client 1',
            'client_active' => 1,
        ]);
            'client_name'   => 'Client 2',
            'client_active' => 1,
        ]);

        /* Act */
        $result = $this->model->getNotAssignedToUser($userId);

        /* Assert */
        $this->assertCount(2, $result);
    }

    public function test_get_latest_limits_results(): void
    {

        $results = $this->model->getLatest(5);
        $this->assertCount(5, $results);
    }

    public function test_fix_avs_formats_correctly(): void
    {
        $formatted = $this->model->fixAvs('123.4567.8901.23');
        $this->assertEquals('12345678901 23', $formatted);

        $unformatted = $this->model->fixAvs('1234567890123');
        $this->assertEquals('1234567890123', $unformatted);

        $empty = $this->model->fixAvs('');
        $this->assertEquals('', $empty);
    }

    public function test_convert_date_returns_mysql_format(): void
    {
        $result = $this->model->convertDate('2023-01-15');
        $this->assertEquals('2023-01-15', $result);
    }

    public function test_convert_date_handles_invalid_input(): void
    {
        Log::shouldReceive('warning')->once();
        $result = $this->model->convertDate('invalid-date');
        $this->assertEquals('', $result);
    }

    public function test_convert_date_handles_null_input(): void
    {
        $result = $this->model->convertDate(null);
        $this->assertEquals('', $result);
    }

    public function test_client_lookup_finds_existing_client(): void
    {

        $result = $this->model->clientLookup('Test Client');
        $this->assertEquals($client->client_id, $result);
    }

    public function test_client_lookup_creates_new_client(): void
    {
        $result = $this->model->clientLookup('New Client');
        $this->assertGreaterThan(0, $result);

        $client = tmpClient::find($result);
        $this->assertNotNull($client);
        $this->assertEquals('New Client', $client->client_name);
    }

    public function test_with_total_includes_invoice_sum(): void
    {
        $builder = $this->model->withTotal();
    }

    public function test_with_total_paid_includes_paid_sum(): void
    {
        $builder = $this->model->withTotalPaid();
    }

    public function test_with_total_balance_includes_balance_sum(): void
    {
        $builder = $this->model->withTotalBalance();
    }

    public function test_is_inactive_filters_inactive_clients(): void
    {
        $builder = $this->model->isInactive();
    }

    public function test_is_active_filters_active_clients(): void
    {
        $builder = $this->model->isActive();
    }

    public function test_get_not_assigned_to_user_excludes_assigned_clients(): void
    {
        $user_id          = 1;


        $results = $this->model->getNotAssignedToUser($user_id);
        $this->assertCount(1, $results);
        $this->assertEquals($unassignedClient->client_id, $results->first()->client_id);
    }

    public function test_get_active_returns_only_active_clients(): void
    {

        $results = $this->model->getActive();
        $this->assertCount(3, $results);
        foreach ($results as $client) {
            $this->assertEquals(1, $client->client_active);
        }
    }

    public function test_delete_logs_orphan_handling(): void
    {
        $this->markTestIncomplete('weak test');

        Log::shouldReceive('info')
            ->once()
            ->with('Orphan handling triggered after client deletion', ['client_id' => $client->client_id]);

        $this->model->delete($client->client_id);
    }
}
