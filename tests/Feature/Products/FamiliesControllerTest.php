<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class FamiliesControllerTest extends AbstractTestCase
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
    public function it_lists_families(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_families', ['family_name' => 'Listed Family']);

        /* Act */
        $response = $this->get('/families');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_families', ['family_name' => 'Listed Family']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_family_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/families/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_family(): void
    {
        /**
         * POST /families/form
         * {
         *     "family_name": "Electronics",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/families/form', [
            'family_name' => 'Electronics',
            'is_update'   => '0',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_families', ['family_name' => 'Electronics']);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_family_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_families', ['family_name' => 'Editable Family']);

        /* Act */
        $response = $this->get('/families/form/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'Editable Family');
    }

    #[Test]
    public function it_updates_a_family(): void
    {
        /**
         * POST /families/form/{id}
         * {
         *     "family_name": "Renamed Family",
         *     "is_update": "1",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_families', ['family_name' => 'Original Family']);

        /* Act */
        $response = $this->post('/families/form/' . $id, [
            'family_name' => 'Renamed Family',
            'is_update'   => '1',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_families', ['family_name' => 'Renamed Family']);
        $this->assertDatabaseMissing('ip_families', ['family_name' => 'Original Family']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_family(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_families', ['family_name' => 'Deletable Family']);
        $this->assertDatabaseHas('ip_families', ['family_name' => 'Deletable Family']);

        /* Act */
        $response = $this->post('/families/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_families', ['family_name' => 'Deletable Family']);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_family_name(): void
    {
        /**
         * POST /families/form
         * {
         *     "family_name": "",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/families/form', [
            'family_name' => '',
            'is_update'   => '0',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseCount('ip_families', 0);
    }

    #[Test]
    public function it_fails_to_update_without_family_name(): void
    {
        /**
         * POST /families/form/{id}
         * {
         *     "family_name": "",
         *     "is_update": "1",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_families', ['family_name' => 'Will Not Change']);

        /* Act */
        $response = $this->post('/families/form/' . $id, [
            'family_name' => '',
            'is_update'   => '1',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseHas('ip_families', ['family_name' => 'Will Not Change']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_when_creating_a_duplicate_family(): void
    {
        /*
         * POST /families/form (duplicate)
         * {
         *     "family_name": "Duplicate Family",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */
        $this->databaseInsert('ip_families', ['family_name' => 'Duplicate Family']);

        /* Act */
        $response = $this->post('/families/form', [
            'family_name' => 'Duplicate Family',
            'is_update'   => '0',
            'btn_submit'  => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Creating a duplicate family must redirect with flash error.');
        $this->assertDatabaseCount('ip_families', 1, ['family_name' => 'Duplicate Family']);
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
        $response = $this->get('/families');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
