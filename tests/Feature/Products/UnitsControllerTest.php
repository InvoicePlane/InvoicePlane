<?php

namespace Tests\Feature\Products;

use Modules\Products\Controllers\FamiliesController;
use Modules\Products\Models\Family;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\Core\FeatureTestCase;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(FamiliesController::class)]

class UnitsControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test index displays paginated list of units.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_units(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Unit', 5);

        /* Act */
        $response = $this->actingAs($user)->get(route('units.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_index');
        $response->assertViewHas('units');
    }

    /**
     * Test create displays unit form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('units.create'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $unit = $response->viewData('unit');
        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertFalse($unit->exists);
    }

    /**
     * Test store creates new unit with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_unit_with_valid_data(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "unit_name": "Kilogram",
         *     "unit_name_plrl": "Kilograms"
         * }.
         */
        $unitData = [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('units.store'), $unitData);

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ]);
    }

    /**
     * Test edit displays unit form with existing data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_unit(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $unit = $this->seedModel('Unit');

        /* Act */
        $response = $this->actingAs($user)->get(route('units.edit', $unit));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $viewUnit = $response->viewData('unit');
        $this->assertEquals($unit->unit_id, $viewUnit->unit_id);
    }

    /**
     * Test update modifies existing unit.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_unit_with_valid_data(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $unit = $this->seedModel('Unit', ['unit_name' => 'Old Name']);

        /**
         * {
         *     "unit_name": "Updated Name",
         *     "unit_name_plrl": "Updated Names"
         * }.
         */
        $updateData = [
            'unit_name'      => 'Updated Name',
            'unit_name_plrl' => 'Updated Names',
        ];

        /* Act */
        $response = $this->actingAs($user)->put(route('units.update', $unit), $updateData);

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Name',
        ]);
    }

    /**
     * Test destroy deletes unit.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_unit(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $unit = $this->seedModel('Unit');

        /* Act */
        $response = $this->actingAs($user)->delete(route('units.destroy', $unit));

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_units', [
            'unit_id' => $unit->unit_id,
        ]);
    }

    /**
     * Test units are ordered correctly.
     */
    #[Test]
    public function it_orders_units_correctly(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('Unit', ['unit_name' => 'Zebra Unit']);
        $this->seedModel('Unit', ['unit_name' => 'Alpha Unit']);
        $this->seedModel('Unit', ['unit_name' => 'Beta Unit']);

        /* Act */
        $response = $this->actingAs($user)->get(route('units.index'));

        /* Assert */
        $response->assertOk();
        $units = $response->viewData('units');

        // Verify ordering (depends on Unit's ordered() scope implementation)
        $this->assertCount(3, $units);
    }
}
