<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Basic smoke tests for Mdl_clients model
 * Tests verify model structure and key functionality
 */
#[CoversClass(Mdl_Clients::class)]
class Mdl_clientsTest extends CiTestCase
{
    protected $model;

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
        $this->assertEquals('ip_clients.client_id', $this->model->primary_key);
    }

    #[Test]
    public function it_has_validation_rules(): void
    {
        $this->assertTrue(method_exists($this->model, 'validation_rules'));
        $rules = $this->model->validation_rules();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_name', $rules);
    }

    #[Test]
    public function it_extends_response_model(): void
    {
        $this->assertInstanceOf('Response_Model', $this->model);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_default_order_by_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_order_by'));
    }
}
