<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Basic smoke tests for Mdl_users model
 * Tests verify model structure and key functionality
 */
#[CoversClass(Mdl_Users::class)]
class Mdl_usersTest extends CiTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->CI->load->model('users/mdl_users');
        $this->model = $this->CI->mdl_users;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_users', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertEquals('ip_users.user_id', $this->model->primary_key);
    }

    #[Test]
    public function it_has_validation_rules(): void
    {
        $this->assertTrue(method_exists($this->model, 'validation_rules'));
        $rules = $this->model->validation_rules();
        $this->assertIsArray($rules);
    }

    #[Test]
    public function it_extends_response_model(): void
    {
        $this->assertInstanceOf('Response_Model', $this->model);
    }
}
