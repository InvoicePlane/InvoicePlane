<?php

namespace Tests\Feature\Core;

use Modules\Core\Models\User;
use Tests\Concerns\InteractsWithDatabase;
use Tests\TestCase;

class FamiliesControllerTest extends TestCase
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
    public function it_displays_families_index(): void
    {
        $response = $this->get(route('families.index'));

        $response->assertSuccessful();
        $response->assertViewHas('families');
    }

    #[Test]
    public function it_creates_new_family(): void
    {
        $familyData = [
            'family_name' => 'Test Family',
            'is_update'   => 0,
        ];

        $response = $this->post(route('families.form'), $familyData);

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseHas('ip_families', [
            'family_name' => 'Test Family',
        ]);
    }

    #[Test]
    public function it_prevents_duplicate_family_names(): void
    {
        $this->seedModel('Family', ['family_name' => 'Existing Family']);

        $familyData = [
            'family_name' => 'Existing Family',
            'is_update'   => 0,
        ];

        $response = $this->post(route('families.form'), $familyData);

        $response->assertRedirect(route('families.form'));
        $response->assertSessionHas('alert_error');
    }

    #[Test]
    public function it_updates_existing_family(): void
    {
        $family = $this->seedModel('Family', ['family_name' => 'Original Family']);

        $updateData = [
            'family_name' => 'Updated Family',
        ];

        $response = $this->post(route('families.form', ['id' => $family->family_id]), $updateData);

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseHas('ip_families', [
            'family_id'   => $family->family_id,
            'family_name' => 'Updated Family',
        ]);
    }

    #[Test]
    public function it_deletes_family(): void
    {
        $family = $this->seedModel('Family');

        $response = $this->delete(route('families.delete', ['id' => $family->family_id]));

        $response->assertRedirect(route('families.index'));
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
    }
}
