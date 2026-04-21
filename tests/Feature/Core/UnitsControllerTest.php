<?php

namespace Modules\Core\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

class UnitsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
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
        Unit::factory()->create(['unit_name' => 'Existing Unit']);

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
        $unit = Unit::factory()->create(['unit_name' => 'Original Unit']);

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
        $unit = Unit::factory()->create();

        $response = $this->delete(route('units.delete', ['id' => $unit->unit_id]));

        $response->assertRedirect(route('units.index'));
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }
}
