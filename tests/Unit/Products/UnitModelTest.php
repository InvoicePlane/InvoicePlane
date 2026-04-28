<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Products;

use Mdl_Units;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Units::class)]
class UnitModelTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new UnitService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->getValidationRules();

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
        $this->markTestIncomplete('Requires database setup with unit data');
    }
}
