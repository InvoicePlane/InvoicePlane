<?php

namespace Tests\Unit\Clients;

use Mdl_Clients;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
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
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_clients', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('client_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_name', $rules);
        $this->assertArrayHasKey('client_email', $rules);
        $this->assertArrayHasKey('client_phone', $rules);
        $this->assertArrayHasKey('client_active', $rules);
    }

    #[Test]
    public function it_has_is_active_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'is_active'));
    }

    #[Test]
    public function it_has_is_inactive_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'is_inactive'));
    }

    #[Test]
    public function it_has_get_not_assigned_to_user_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_not_assigned_to_user'));
    }

    #[Test]
    public function it_has_get_latest_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_latest'));
    }

    #[Group('smoke')]
    #[Test]
    public function it_fix_avs_strips_dots_from_valid_avs_number(): void
    {
        $result = $this->model->fix_avs('123.4567.8901.23');

        $this->assertEquals('1234567890123', $result);
    }

    #[Group('smoke')]
    #[Test]
    public function it_fix_avs_returns_empty_string_for_empty_input(): void
    {
        $this->assertEquals('', $this->model->fix_avs(''));
    }

    #[Group('smoke')]
    #[Test]
    public function it_fix_avs_returns_13_digit_number_unchanged(): void
    {
        $this->assertEquals('1234567890123', $this->model->fix_avs('1234567890123'));
    }

    #[Group('smoke')]
    #[Test]
    public function it_fix_avs_returns_empty_string_for_invalid_format(): void
    {
        $this->assertEquals('', $this->model->fix_avs('invalid'));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_and_retrieves_client(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $name      = 'ClientCreate_' . uniqid();
        $client_id = $this->seedModel('Client', ['client_name' => $name])->client_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_clients', ['client_id' => $client_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($name, $row['client_name']);
        $this->assertEquals(1, (int) $row['client_active']);

        /* Cleanup */
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_all_active_clients(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $active_id   = $this->seedModel('Client', ['client_active' => 1])->client_id;
        $inactive_id = $this->seedModel('Client', ['client_active' => 0])->client_id;

        /* Act */
        $this->model->is_active();
        $results = $this->model->get()->result();

        /* Assert */
        $ids = array_column($results, 'client_id');
        $this->assertContains((string) $active_id, $ids);
        $this->assertNotContains((string) $inactive_id, $ids);

        /* Cleanup */
        $this->databaseDelete('ip_clients', ['client_id' => $active_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $inactive_id]);
    }
}
