<?php

namespace Tests\Feature\Products;

use Families;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(Families::class)]
#[CoversClass(Tests\Feature\Products\FamiliesController::class)]

class FamiliesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    /**
     * Test index displays paginated list of families.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_families(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Family', 5);

        /* Act */
        $response = $this->actingAs($user)->get('/families/index');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::families_index');
        $response->assertViewHas('families');
        $response->assertViewHas('filter_display', true);
        $response->assertViewHas('filter_placeholder');
        $response->assertViewHas('filter_method', 'filter_families');
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get('/families/form');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::families_form');
        $response->assertViewHas('family');
        $response->assertViewHas('is_update', false);

        $family = $response->viewData('family');
        $this->assertInstanceOf(Family::class, $family);
        $this->assertFalse($family->exists);
    }

    /**
     * Test form displays edit form with existing family.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_family(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $family = $this->seedModel('Family');

        /* Act */
        $response = $this->actingAs($user)->get('/families/form/' . ($family->family_id));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::families_form');
        $response->assertViewHas('family');
        $response->assertViewHas('is_update', true);

        $viewFamily = $response->viewData('family');
        $this->assertEquals($family->family_id, $viewFamily->family_id);
    }

    /**
     * Test form creates new family with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_family_with_valid_data(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "family_name": "Electronics",
         *     "btn_submit": "1"
         * }.
         */
        $familyData = [
            'family_name' => 'Electronics',
            'btn_submit'  => '1',
        ];

        /* Act */
        $response = $this->actingAs($user)->post('/families/form', $familyData);

        /* Assert */
        $response->assertRedirect('/families/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_families', [
            'family_name' => 'Electronics',
        ]);
    }

    #[Test]
    public function it_creates_new_family(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $familyData = [
            'family_name' => 'Test Family',
            'is_update'   => 0,
        ];

        $response = $this->post('/families/form', $familyData);

        $response->assertRedirect('/families/index');
        $this->assertDatabaseHas('ip_families', [
            'family_name' => 'Test Family',
        ]);
    }

    /**
     * Test form updates existing family.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_family_with_valid_data(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $family = $this->seedModel('Family', ['family_name' => 'Old Name']);

        /**
         * {
         *     "family_name": "Updated Name",
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'family_name' => 'Updated Name',
            'btn_submit'  => '1',
        ];

        /* Act */
        $response = $this->actingAs($user)->post('/families/form/' . ($family->family_id), $updateData);

        /* Assert */
        $response->assertRedirect('/families/index');
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_families', [
            'family_id'   => $family->family_id,
            'family_name' => 'Updated Name',
        ]);
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $response = $this->actingAs($user)->post('/families/form', $cancelData);

        /* Assert */
        $response->assertRedirect('/families/index');
    }

    /**
     * Test form validates required family name.
     */
    #[Test]
    public function it_validates_required_family_name(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "family_name": "",
         *     "btn_submit": "1"
         * }.
         */
        $invalidData = [
            'family_name' => '',
            'btn_submit'  => '1',
        ];

        /* Act */
        $response = $this->actingAs($user)->post('/families/form', $invalidData);

        /* Assert */
        $response->assertSessionHasErrors('family_name');
    }

    /**
     * Test form validates unique family name.
     */
    #[Test]
    public function it_validates_unique_family_name(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModel('Family', ['family_name' => 'Existing Family']);

        /**
         * {
         *     "family_name": "Existing Family",
         *     "btn_submit": "1"
         * }.
         */
        $duplicateData = [
            'family_name' => 'Existing Family',
            'btn_submit'  => '1',
        ];

        /* Act */
        $response = $this->actingAs($user)->post('/families/form', $duplicateData);

        /* Assert */
        $response->assertSessionHasErrors('family_name');
    }

    /**
     * Test delete removes family.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_family(): void
    {
        /* Arrange */
        $family = $this->seedModel('Family');

        /**
         * {
         *     "family_id": 1
         * }.
         */
        $deletePayload = [
            'family_id' => $family->family_id,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(
            '/families/delete/' . ($family->family_id),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect('/families/index');
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_families', [
            'family_id' => $family->family_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent family.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_family(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /**
         * {
         *     "family_id": 99999
         * }.
         */
        $deletePayload = [
            'family_id' => 99999,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(
            '/families/delete/' . (99999),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }

    #[Test]
    public function it_prevents_duplicate_family_names(): void
    {
    /* Arrange */
    // ...

    /* Act */
    // ...

    /* Assert */
    // ...

        $this->seedModel('Family', ['family_name' => 'Existing Family']);

        $familyData = [
            'family_name' => 'Existing Family',
            'is_update'   => 0,
        ];

        $response = $this->post('/families/form', $familyData);

        $response->assertRedirect('/families/form');
        $response->assertSessionHas('alert_error');
    }
}
