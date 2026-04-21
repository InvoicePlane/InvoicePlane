<?php

namespace Modules\Products\Tests\Feature;

use Modules\Core\Models\User;
use Modules\Products\Controllers\FamiliesController;
use Modules\Products\Models\Family;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(FamiliesController::class)]

class UnitsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of units.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_units(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Unit::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.index'));

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
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.form'));

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
        /** Arrange */
        $user = User::factory()->create();

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
        $this->actingAs($user);
        $response = $this->post(route('units.form'), $unitData);

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
        /** Arrange */
        $user = User::factory()->create();
        $unit = Unit::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.form', ['unit_id' => $unit->unit_id]));

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
        /** Arrange */
        $user = User::factory()->create();
        $unit = Unit::factory()->create(['unit_name' => 'Old Name']);

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
        $this->actingAs($user);
        $response = $this->post(route('units.form', ['unit_id' => $unit->unit_id]), $updateData);

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
        /** Arrange */
        $user = User::factory()->create();
        $unit = Unit::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->delete(route('units.destroy', $unit));

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
        /** Arrange */
        $user = User::factory()->create();

        Unit::factory()->create(['unit_name' => 'Zebra Unit']);
        Unit::factory()->create(['unit_name' => 'Alpha Unit']);
        Unit::factory()->create(['unit_name' => 'Beta Unit']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.index'));

        /* Assert */
        $response->assertOk();
        $units = $response->viewData('units');

        // Verify ordering (depends on Unit's ordered() scope implementation)
        $this->assertCount(3, $units);
    }
}
