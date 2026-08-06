<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class UnitsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_units(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_units', [
            'unit_name'      => 'Listed Unit',
            'unit_name_plrl' => 'Listed Units',
        ]);

        /* Act */
        $response = $this->get('/units');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Listed Unit']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_unit_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/units/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_unit(): void
    {
        /**
         * POST /units/form
         * {
         *     "unit_name": "Kilogram",
         *     "unit_name_plrl": "Kilograms",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Kilogram']);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_unit_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_units', [
            'unit_name'      => 'Editable Unit',
            'unit_name_plrl' => 'Editable Units',
        ]);

        /* Act */
        $response = $this->get('/units/form/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'Editable Unit');
    }

    #[Test]
    public function it_updates_a_unit(): void
    {
        /**
         * POST /units/form/{id}
         * {
         *     "unit_name": "Renamed Unit",
         *     "unit_name_plrl": "Renamed Units",
         *     "is_update": "1",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_units', [
            'unit_name'      => 'Original Unit',
            'unit_name_plrl' => 'Original Units',
        ]);

        /* Act */
        $response = $this->post('/units/form/' . $id, [
            'unit_name'      => 'Renamed Unit',
            'unit_name_plrl' => 'Renamed Units',
            'is_update'      => '1',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Renamed Unit']);
        $this->assertDatabaseMissing('ip_units', ['unit_name' => 'Original Unit']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_unit(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_units', [
            'unit_name'      => 'Deletable Unit',
            'unit_name_plrl' => 'Deletable Units',
        ]);
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Deletable Unit']);

        /* Act */
        $response = $this->post('/units/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_units', ['unit_name' => 'Deletable Unit']);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_unit_name(): void
    {
        /**
         * POST /units/form
         * {
         *     "unit_name": "",
         *     "unit_name_plrl": "Items",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => '',
            'unit_name_plrl' => 'Items',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseCount('ip_units', 0);
    }

    #[Test]
    public function it_fails_to_create_without_unit_name_plural(): void
    {
        /**
         * POST /units/form
         * {
         *     "unit_name": "Item",
         *     "unit_name_plrl": "",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => 'Item',
            'unit_name_plrl' => '',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseMissing('ip_units', ['unit_name' => 'Item']);
    }

    #[Test]
    public function it_fails_to_update_without_unit_name(): void
    {
        /**
         * POST /units/form/{id}
         * {
         *     "unit_name": "",
         *     "unit_name_plrl": "Original Units",
         *     "is_update": "1",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_units', [
            'unit_name'      => 'Will Not Change',
            'unit_name_plrl' => 'Will Not Change Plural',
        ]);

        /* Act */
        $response = $this->post('/units/form/' . $id, [
            'unit_name'      => '',
            'unit_name_plrl' => 'Will Not Change Plural',
            'is_update'      => '1',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseHas('ip_units', ['unit_name' => 'Will Not Change']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_when_creating_a_duplicate_unit(): void
    {
        /*
         * POST /units/form (duplicate)
         * {
         *     "unit_name": "Duplicate Unit",
         *     "unit_name_plrl": "Duplicate Units",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */
        $this->databaseInsert('ip_units', [
            'unit_name'      => 'Duplicate Unit',
            'unit_name_plrl' => 'Duplicate Units',
        ]);

        /* Act */
        $response = $this->post('/units/form', [
            'unit_name'      => 'Duplicate Unit',
            'unit_name_plrl' => 'Duplicate Units',
            'is_update'      => '0',
            'btn_submit'     => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Creating a duplicate unit must redirect with flash error.');
        $this->assertDatabaseCount('ip_units', 1, ['unit_name' => 'Duplicate Unit']);
    }

    // -------------------------------------------------------------------------
    // Guest redirect — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/units');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
