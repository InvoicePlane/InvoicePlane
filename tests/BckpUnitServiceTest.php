<?php

use Modules\Products\Models\Family;
use Modules\Products\Services\FamilyService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * FamilyService Deletion Validation Tests.
 *
 * Tests business rules for family deletion:
 * - Families with products cannot be deleted
 */
#[CoversClass(FamilyService::class)]

class BckpUnitServiceTest extends AbstractTestCase
{
    private \Tests\Feature\Invoices\UnitService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new \Tests\Feature\Invoices\UnitService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
        $this->assertArrayHasKey('unit_name_plrl', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_string_when_unit_id_is_null(): void
    {
        $result = $this->service->getUnitName(null, 1);
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_gets_unit_name(): void
    {
        /* Arrange */
        $this->cleanupTables(['ip_units']);

        $unit = \Modules\Products\Models\Unit::create([
            'unit_name'      => 'Hour',
            'unit_name_plrl' => 'Hours',
        ]);

        /* Act */
        $singularName = $this->service->getUnitName($unit->unit_id, 1);
        $pluralName   = $this->service->getUnitName($unit->unit_id, 2);

        /* Assert */
        $this->assertEquals('Hour', $singularName);
        $this->assertEquals('Hours', $pluralName);
    }
}
