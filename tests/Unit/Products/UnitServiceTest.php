<?php

namespace Tests\Unit\Products;

use Mdl_Units;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Units::class)]
class UnitServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UnitService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
        $this->assertArrayHasKey('unit_name_plrl', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_string_when_unit_id_is_null(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $result = $this->service->getUnitName(null, 1);
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_gets_unit_name(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->markTestIncomplete('Requires database setup with unit data');
    }
}
