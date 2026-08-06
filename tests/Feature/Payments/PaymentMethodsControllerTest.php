<?php

namespace Tests\Feature\Payments;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class PaymentMethodsControllerTest extends AbstractTestCase
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
    public function it_lists_payment_methods(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_payment_methods', [
            'payment_method_name' => 'Listed Method',
        ]);

        /* Act */
        $response = $this->get('/payment_methods');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Listed Method']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_payment_method_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/payment_methods/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_payment_method(): void
    {
        /**
         * POST /payment_methods/form
         * {
         *     "payment_method_name": "Bank Transfer",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Bank Transfer',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Bank Transfer']);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_payment_method_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', [
            'payment_method_name' => 'Editable Method',
        ]);

        /* Act */
        $response = $this->get('/payment_methods/form/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'Editable Method');
    }

    #[Test]
    public function it_updates_a_payment_method(): void
    {
        /**
         * POST /payment_methods/form/{id}
         * {
         *     "payment_method_name": "Renamed Method",
         *     "is_update": "1",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', [
            'payment_method_name' => 'Original Method',
        ]);

        /* Act */
        $response = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => 'Renamed Method',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Renamed Method']);
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_name' => 'Original Method']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_payment_method(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', [
            'payment_method_name' => 'Deletable Method',
        ]);
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Deletable Method']);

        /* Act */
        $response = $this->post('/payment_methods/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_payment_methods', ['payment_method_name' => 'Deletable Method']);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_payment_method_name(): void
    {
        /**
         * POST /payment_methods/form
         * {
         *     "payment_method_name": "",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => '',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseCount('ip_payment_methods', 0);
    }

    #[Test]
    public function it_fails_to_update_without_payment_method_name(): void
    {
        /**
         * POST /payment_methods/form/{id}
         * {
         *     "payment_method_name": "",
         *     "is_update": "1",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_payment_methods', [
            'payment_method_name' => 'Will Not Change',
        ]);

        /* Act */
        $response = $this->post('/payment_methods/form/' . $id, [
            'payment_method_name' => '',
            'is_update'           => '1',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseHas('ip_payment_methods', ['payment_method_name' => 'Will Not Change']);
    }

    // -------------------------------------------------------------------------
    // Edge cases
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_when_creating_a_duplicate_payment_method(): void
    {
        /*
         * POST /payment_methods/form (duplicate)
         * {
         *     "payment_method_name": "Duplicate Method",
         *     "is_update": "0",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */
        $this->databaseInsert('ip_payment_methods', ['payment_method_name' => 'Duplicate Method']);

        /* Act */
        $response = $this->post('/payment_methods/form', [
            'payment_method_name' => 'Duplicate Method',
            'is_update'           => '0',
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Creating a duplicate payment method must redirect with flash error.');
        $this->assertDatabaseCount('ip_payment_methods', 1, ['payment_method_name' => 'Duplicate Method']);
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
        $response = $this->get('/payment_methods');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
