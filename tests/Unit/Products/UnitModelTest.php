<?php

namespace Tests\Unit\Products;

use Mdl_Units;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Units::class)]
class UnitModelTest extends CiTestCase
{
    use InteractsWithDatabase;
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('units/mdl_units');
        $this->model = $this->CI->mdl_units;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
        $this->assertArrayHasKey('unit_name_plrl', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_string_when_unit_id_is_null(): void
    {
        $result = $this->model->getUnitName(null, 1);
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_gets_unit_name(): void
    {
        $this->skipWithoutDatabase();
        $unit = $this->seedModel('Unit', ['unit_name' => 'Hour', 'unit_name_plrl' => 'Hours']);
        $this->assertEquals('Hour', $this->model->getUnitName($unit->unit_id, 1));
        $this->assertEquals('Hours', $this->model->getUnitName($unit->unit_id, 2));
    }
}
