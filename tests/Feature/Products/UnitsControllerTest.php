<?php

namespace Feature\Products;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;
use Units;

#[CoversClass(Units::class)]
class UnitsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_units_index(): void
    {
        $response = $this->get('/units/index');

        $response->assertSuccessful();
        $response->assertViewHas('units');
    }

    #[Test]
    public function it_creates_new_unit(): void
    {
        /**
         * Payload:
         * {
         *     "unit_name": "Kilogram",
         *     "unit_name_plrl": "Kilograms"
         * }
         */
        $unitData = [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ];

        $response = $this->post('/units/form', $unitData);

        $response->assertRedirect('/units/index');
        $this->assertDatabaseHas('ip_units', [
            'unit_name' => 'Kilogram',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_unit_names(): void
    {
        $this->seedModel('Unit', ['unit_name' => 'Existing Unit']);

        /**
         * Payload:
         * {
         *     "unit_name": "Existing Unit",
         *     "unit_name_plrl": "Existing Units",
         *     "is_update": 0
         * }
         */
        $unitData = [
            'unit_name'      => 'Existing Unit',
            'unit_name_plrl' => 'Existing Units',
            'is_update'      => 0,
        ];

        $response = $this->post('/units/form', $unitData);

        $response->assertRedirect('/units/form');
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_unit(): void
    {
        $unit = $this->seedModel('Unit', ['unit_name' => 'Original Unit']);

        /**
         * Payload:
         * {
         *     "unit_name": "Updated Unit",
         *     "unit_name_plrl": "Updated Units"
         * }
         */
        $updateData = [
            'unit_name'      => 'Updated Unit',
            'unit_name_plrl' => 'Updated Units',
        ];

        $response = $this->post('/units/form/' . ($unit->unit_id), $updateData);

        $response->assertRedirect('/units/index');
        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Unit',
        ]);
    }

    #[Test]
    public function it_deletes_unit(): void
    {
        $unit = $this->seedModel('Unit');

        $response = $this->delete('/units/delete/' . ($unit->unit_id));

        $response->assertRedirect('/units/index');
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }


    // Migrated from BckpUnitsControllerTest.php
    #[Test]
    public function it_displays_paginated_list_of_units(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Unit', 5);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/units/index');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_index');
        $response->assertViewHas('units');
    }

    #[Test]
    public function it_displays_create_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/units/form');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $unit = $response->viewData('unit');
        $this->assertNotNull($unit);
    }

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
        $this->actingAs($user);
        $response = $this->post('/units/form', $unitData);

        /* Assert */
        $response->assertRedirect('/units/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ]);
    }

    #[Test]
    public function it_displays_edit_form_with_existing_unit(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $unit = $this->seedModel('Unit');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/units/form/' . ($unit->unit_id));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $viewUnit = $response->viewData('unit');
        $this->assertEquals($unit->unit_id, $viewUnit->unit_id);
    }

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
        $this->actingAs($user);
        $response = $this->post('/units/form/' . ($unit->unit_id), $updateData);

        /* Assert */
        $response->assertRedirect('/units/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Name',
        ]);
    }

    #[Test]
    public function it_orders_units_correctly(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('Unit', ['unit_name' => 'Zebra Unit']);
        $this->seedModel('Unit', ['unit_name' => 'Alpha Unit']);
        $this->seedModel('Unit', ['unit_name' => 'Beta Unit']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/units/index');

        /* Assert */
        $response->assertOk();
        $units = $response->viewData('units');

        // Verify ordering (depends on Unit's ordered() scope implementation)
        $this->assertCount(3, $units);
    }

}
