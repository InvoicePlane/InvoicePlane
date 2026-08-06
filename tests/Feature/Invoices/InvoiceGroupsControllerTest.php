<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class InvoiceGroupsControllerTest extends AbstractTestCase
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
    public function it_lists_invoice_groups(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_invoice_groups', [
            'invoice_group_name'              => 'Listed Group',
            'invoice_group_next_id'           => 1,
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_left_pad'          => 0,
        ]);

        /* Act */
        $response = $this->get('/invoice_groups');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Listed Group']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_invoice_group_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/invoice_groups/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_an_invoice_group(): void
    {
        /**
         * POST /invoice_groups/form
         * {
         *     "invoice_group_name": "Yearly 2025",
         *     "invoice_group_identifier_format": "{number}",
         *     "invoice_group_next_id": "1",
         *     "invoice_group_left_pad": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', [
            'invoice_group_name'              => 'Yearly 2025',
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_next_id'           => '1',
            'invoice_group_left_pad'          => '0',
            'btn_submit'                      => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Yearly 2025']);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_invoice_group_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_invoice_groups', [
            'invoice_group_name'              => 'Editable Group',
            'invoice_group_next_id'           => 1,
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_left_pad'          => 0,
        ]);

        /* Act */
        $response = $this->get('/invoice_groups/form/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'Editable Group');
    }

    #[Test]
    public function it_updates_an_invoice_group(): void
    {
        /**
         * POST /invoice_groups/form/{id}
         * {
         *     "invoice_group_name": "Renamed Group",
         *     "invoice_group_identifier_format": "{number}",
         *     "invoice_group_next_id": "5",
         *     "invoice_group_left_pad": "3",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_invoice_groups', [
            'invoice_group_name'              => 'Original Group',
            'invoice_group_next_id'           => 1,
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_left_pad'          => 0,
        ]);

        /* Act */
        $response = $this->post('/invoice_groups/form/' . $id, [
            'invoice_group_name'              => 'Renamed Group',
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_next_id'           => '5',
            'invoice_group_left_pad'          => '3',
            'btn_submit'                      => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Renamed Group']);
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Original Group']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_an_invoice_group(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_invoice_groups', [
            'invoice_group_name'              => 'Deletable Group',
            'invoice_group_next_id'           => 1,
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_left_pad'          => 0,
        ]);
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Deletable Group']);

        /* Act */
        $response = $this->post('/invoice_groups/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Deletable Group']);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_invoice_group_name(): void
    {
        /**
         * POST /invoice_groups/form
         * {
         *     "invoice_group_name": "",
         *     "invoice_group_identifier_format": "{number}",
         *     "invoice_group_next_id": "1",
         *     "invoice_group_left_pad": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', [
            'invoice_group_name'              => '',
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_next_id'           => '1',
            'invoice_group_left_pad'          => '0',
            'btn_submit'                      => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        // Baseline seeding always creates one default invoice group; a failed
        // create must not add a second one.
        $this->assertDatabaseCount('ip_invoice_groups', 1);
    }

    #[Test]
    public function it_fails_to_create_without_identifier_format(): void
    {
        /**
         * POST /invoice_groups/form
         * {
         *     "invoice_group_name": "Missing Format",
         *     "invoice_group_identifier_format": "",
         *     "invoice_group_next_id": "1",
         *     "invoice_group_left_pad": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/invoice_groups/form', [
            'invoice_group_name'              => 'Missing Format',
            'invoice_group_identifier_format' => '',
            'invoice_group_next_id'           => '1',
            'invoice_group_left_pad'          => '0',
            'btn_submit'                      => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseMissing('ip_invoice_groups', ['invoice_group_name' => 'Missing Format']);
    }

    #[Test]
    public function it_fails_to_update_without_invoice_group_name(): void
    {
        /**
         * POST /invoice_groups/form/{id}
         * {
         *     "invoice_group_name": "",
         *     "invoice_group_identifier_format": "{number}",
         *     "invoice_group_next_id": "1",
         *     "invoice_group_left_pad": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_invoice_groups', [
            'invoice_group_name'              => 'Will Not Change',
            'invoice_group_next_id'           => 1,
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_left_pad'          => 0,
        ]);

        /* Act */
        $response = $this->post('/invoice_groups/form/' . $id, [
            'invoice_group_name'              => '',
            'invoice_group_identifier_format' => '{number}',
            'invoice_group_next_id'           => '1',
            'invoice_group_left_pad'          => '0',
            'btn_submit'                      => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseHas('ip_invoice_groups', ['invoice_group_name' => 'Will Not Change']);
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
        $response = $this->get('/invoice_groups');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
