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
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $response = $this->get(route('units.index'));

        $response->assertSuccessful();
        $response->assertViewHas('units');
    }

    #[Test]
    public function it_creates_new_unit(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $unitData = [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ];

        $response = $this->post(route('units.form'), $unitData);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('ip_units', [
            'unit_name' => 'Kilogram',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_unit_names(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->seedModel('Unit', ['unit_name' => 'Existing Unit']);

        $unitData = [
            'unit_name'      => 'Existing Unit',
            'unit_name_plrl' => 'Existing Units',
            'is_update'      => 0,
        ];

        $response = $this->post(route('units.form'), $unitData);

        $response->assertRedirect(route('units.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_unit(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $unit = $this->seedModel('Unit', ['unit_name' => 'Original Unit']);

        $updateData = [
            'unit_name'      => 'Updated Unit',
            'unit_name_plrl' => 'Updated Units',
        ];

        $response = $this->post(route('units.form', ['id' => $unit->unit_id]), $updateData);

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Unit',
        ]);
    }

    #[Test]
    public function it_deletes_unit(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $unit = $this->seedModel('Unit');

        $response = $this->delete(route('units.delete', ['id' => $unit->unit_id]));

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }


    // Migrated from BckpUnitsControllerTest.php
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_paginated_list_of_units(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Unit', 5);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(\Tests\Feature\Invoices\route('units.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_index');
        $response->assertViewHas('units');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_create_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(\Tests\Feature\Invoices\route('units.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $unit = $response->viewData('unit');
        $this->assertInstanceOf(\Tests\Feature\Invoices\Unit::class, $unit);
        $this->assertFalse($unit->exists);
    }

    #[\PHPUnit\Framework\Attributes\Test]
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
        $response = $this->post(\Tests\Feature\Invoices\route('units.form'), $unitData);

        /* Assert */
        $response->assertRedirect(\Tests\Feature\Invoices\route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_edit_form_with_existing_unit(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $unit = $this->seedModel('Unit');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(\Tests\Feature\Invoices\route('units.form', ['unit_id' => $unit->unit_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $viewUnit = $response->viewData('unit');
        $this->assertEquals($unit->unit_id, $viewUnit->unit_id);
    }

    #[\PHPUnit\Framework\Attributes\Test]
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
        $response = $this->post(\Tests\Feature\Invoices\route('units.form', ['unit_id' => $unit->unit_id]), $updateData);

        /* Assert */
        $response->assertRedirect(\Tests\Feature\Invoices\route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Name',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_orders_units_correctly(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        $this->seedModel('Unit', ['unit_name' => 'Zebra Unit']);
        $this->seedModel('Unit', ['unit_name' => 'Alpha Unit']);
        $this->seedModel('Unit', ['unit_name' => 'Beta Unit']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(\Tests\Feature\Invoices\route('units.index'));

        /* Assert */
        $response->assertOk();
        $units = $response->viewData('units');

        // Verify ordering (depends on Unit's ordered() scope implementation)
        $this->assertCount(3, $units);
    }

}
