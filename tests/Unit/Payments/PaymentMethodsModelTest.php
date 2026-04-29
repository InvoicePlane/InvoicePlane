<?php

namespace Tests\Unit\Payments;

use Mdl_Payment_Methods;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Payment_Methods::class)]
class PaymentMethodsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('payment_methods/mdl_payment_methods');
        $this->model = $this->CI->mdl_payment_methods;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_payment_methods', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('payment_method_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('payment_method_name', $rules);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_order_by_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'order_by'));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_and_retrieves_payment_method(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $name = 'TestMethod_' . uniqid();
        $pm_id = $this->seedModel('PaymentMethod', ['payment_method_name' => $name])->payment_method_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_payment_methods', ['payment_method_id' => $pm_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($name, $row['payment_method_name']);

        /* Cleanup */
        $this->databaseDelete('ip_payment_methods', ['payment_method_id' => $pm_id]);
    }
}
