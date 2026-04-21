<?php

namespace Tests\Feature\Core;

use Modules\Core\Models\User;
use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class UnitsControllerTest extends TestCase
{
    use InteractsWithDatabase;

    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_displays_units_index(): void
    {
        $response = $this->get(route('units.index'));

        $response->assertSuccessful();
        $response->assertViewHas('units');
    }

    #[Test]
    public function it_creates_new_unit(): void
    {
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
        $unit = $this->seedModel('Unit');

        $response = $this->delete(route('units.delete', ['id' => $unit->unit_id]));

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }
}
