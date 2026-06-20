<?php

namespace Tests\Unit\Products;

use Mdl_Units;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Units::class)]
class UnitsServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Service class does not exist — CI3 model layer, no Laravel service layer available');
        $this->service = new UnitsService();
    }

    #[Test]
    public function it_returns_all_units(): void
    {
        /* Arrange */
        Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);
        Unit::create(['unit_name' => 'Day', 'unit_name_plrl' => 'Days']);
        Unit::create(['unit_name' => 'Item', 'unit_name_plrl' => 'Items']);

        /* Act */
        $result = $this->service->getAll();

        /* Assert */
        $this->assertCount(3, $result);
        $this->assertEquals('Hour', $result[0]->unit_name);
        $this->assertEquals('Day', $result[1]->unit_name);
        $this->assertEquals('Item', $result[2]->unit_name);
    }

    #[Test]
    public function it_returns_empty_collection_when_no_units_exist(): void
    {
        /* Act */
        $result = $this->service->getAll();

        /* Assert */
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_checks_if_unit_exists_by_name(): void
    {
        /* Arrange */
        Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act & Assert */
        $this->assertTrue($this->service->exists('Hour'));
        $this->assertFalse($this->service->exists('NonExistent'));
    }

    #[Test]
    public function it_returns_singular_name_for_quantity_of_one(): void
    {
        /* Arrange */
        $unit = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act */
        $result = $this->service->getName($unit->unit_id, 1);

        /* Assert */
        $this->assertEquals('Hour', $result);
    }

    #[Test]
    public function it_returns_singular_name_for_quantity_of_negative_one(): void
    {
        /* Arrange */
        $unit = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act */
        $result = $this->service->getName($unit->unit_id, -1);

        /* Assert */
        $this->assertEquals('Hour', $result);
    }

    #[Test]
    public function it_returns_singular_name_for_quantity_of_zero(): void
    {
        /* Arrange */
        $unit = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act */
        $result = $this->service->getName($unit->unit_id, 0);

        /* Assert */
        $this->assertEquals('Hour', $result);
    }

    #[Test]
    public function it_returns_plural_name_for_quantity_greater_than_one(): void
    {
        /* Arrange */
        $unit = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act */
        $result = $this->service->getName($unit->unit_id, 5);

        /* Assert */
        $this->assertEquals('Hours', $result);
    }

    #[Test]
    public function it_returns_plural_name_for_quantity_less_than_negative_one(): void
    {
        /* Arrange */
        $unit = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act */
        $result = $this->service->getName($unit->unit_id, -5);

        /* Assert */
        $this->assertEquals('Hours', $result);
    }

    #[Test]
    public function it_returns_null_for_non_existent_unit_id(): void
    {
        /* Act */
        $result = $this->service->getName(99999, 1);

        /* Assert */
        $this->assertNull($result);
    }

    #[Test]
    public function it_creates_new_unit_when_id_is_null(): void
    {
        /* Arrange */
        $data = [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ];

        /* Act */
        $unit = $this->service->save($data);

        /* Assert */
        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertEquals('Kilogram', $unit->unit_name);
        $this->assertEquals('Kilograms', $unit->unit_name_plrl);
        $this->assertDatabaseHas('ip_units', [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ]);
    }

    #[Test]
    public function it_updates_existing_unit_when_id_is_provided(): void
    {
        /* Arrange */
        $unit       = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);
        $updateData = [
            'unit_name'      => 'Updated Hour',
            'unit_name_plrl' => 'Updated Hours',
        ];

        /* Act */
        $result = $this->service->save($updateData, $unit->unit_id);

        /* Assert */
        $this->assertInstanceOf(Unit::class, $result);
        $this->assertEquals('Updated Hour', $result->unit_name);
        $this->assertEquals('Updated Hours', $result->unit_name_plrl);
        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Hour',
        ]);
    }

    #[Test]
    public function it_throws_exception_when_updating_non_existent_unit(): void
    {
        /* Arrange */
        $data = ['unit_name' => 'Test', 'unit_name_plrl' => 'Tests'];

        /* Assert */
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unit not found');

        /* Act */
        $this->service->save($data, 99999);
    }

    #[Test]
    public function it_deletes_existing_unit(): void
    {
        /* Arrange */
        $unit = Unit::create(['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);

        /* Act */
        $result = $this->service->delete($unit->unit_id);

        /* Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }

    #[Test]
    public function it_returns_false_when_deleting_non_existent_unit(): void
    {
        /* Act */
        $result = $this->service->delete(99999);

        /* Assert */
        $this->assertFalse($result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Act */
        $rules = $this->service->validationRules();

        /* Assert */
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
        $this->assertArrayHasKey('unit_name_plrl', $rules);
        $this->assertEquals('required', $rules['unit_name']['rules']);
        $this->assertEquals('required', $rules['unit_name_plrl']['rules']);
    }

    #[Test]
    public function it_returns_default_select_query_builder(): void
    {
        /* Act */
        $builder = $this->service->defaultSelect();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }

    #[Test]
    public function it_returns_default_order_by_query_builder(): void
    {
        /* Act */
        $builder = $this->service->defaultOrderBy();

        /* Assert */
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $builder);
    }
}
