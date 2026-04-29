<?php

namespace Tests\Unit\Products;

use Mdl_Units;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Units::class)]
class UnitsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('units/mdl_units');
        $this->model = $this->CI->mdl_units;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_units', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('unit_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
        $this->assertArrayHasKey('unit_name_plrl', $rules);
        $this->assertEquals('required', $rules['unit_name']['rules']);
        $this->assertEquals('required', $rules['unit_name_plrl']['rules']);
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

    #[Test]
    public function it_has_get_name_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_name'));
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_unit_id_is_falsy(): void
    {
        $this->assertNull($this->model->get_name(0, 1));
        $this->assertNull($this->model->get_name(null, 5));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_and_retrieves_unit(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $name    = 'TestUnit_' . uniqid();
        $unit_id = $this->seedModel('Unit', ['unit_name' => $name, 'unit_name_plrl' => $name . 's'])->unit_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_units', ['unit_id' => $unit_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($name, $row['unit_name']);

        /* Cleanup */
        $this->databaseDelete('ip_units', ['unit_id' => $unit_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_singular_name_for_existing_unit(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $unit_id = $this->seedModel('Unit', ['unit_name' => 'Kilogram', 'unit_name_plrl' => 'Kilograms'])->unit_id;

        /* Act */
        $name = $this->model->get_name($unit_id, 1);

        /* Assert */
        $this->assertEquals('Kilogram', $name);

        /* Cleanup */
        $this->databaseDelete('ip_units', ['unit_id' => $unit_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_plural_name_for_quantity_greater_than_one(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $unit_id = $this->seedModel('Unit', ['unit_name' => 'Liter', 'unit_name_plrl' => 'Liters'])->unit_id;

        /* Act */
        $name = $this->model->get_name($unit_id, 5);

        /* Assert */
        $this->assertEquals('Liters', $name);

        /* Cleanup */
        $this->databaseDelete('ip_units', ['unit_id' => $unit_id]);
    }
}
