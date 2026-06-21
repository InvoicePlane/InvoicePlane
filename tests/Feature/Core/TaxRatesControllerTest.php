<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class TaxRatesControllerTest extends AbstractTestCase
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
    public function it_lists_tax_rates(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'Listed VAT',
            'tax_rate_percent' => '21.00',
        ]);

        /* Act */
        $response = $this->get('/tax_rates');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Listed VAT']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_tax_rate_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/tax_rates/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_tax_rate(): void
    {
        /**
         * POST /tax_rates/form
         * {
         *     "tax_rate_name": "Standard VAT",
         *     "tax_rate_percent": "21.00",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/tax_rates/form', [
            'tax_rate_name'    => 'Standard VAT',
            'tax_rate_percent' => '21.00',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Standard VAT']);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_tax_rate_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'Editable VAT',
            'tax_rate_percent' => '9.00',
        ]);

        /* Act */
        $response = $this->get('/tax_rates/form/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'Editable VAT');
    }

    #[Test]
    public function it_updates_a_tax_rate(): void
    {
        /**
         * POST /tax_rates/form/{id}
         * {
         *     "tax_rate_name": "Renamed VAT",
         *     "tax_rate_percent": "15.00",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'Original VAT',
            'tax_rate_percent' => '9.00',
        ]);

        /* Act */
        $response = $this->post('/tax_rates/form/' . $id, [
            'tax_rate_name'    => 'Renamed VAT',
            'tax_rate_percent' => '15.00',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Renamed VAT', 'tax_rate_percent' => '15.00']);
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Original VAT']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_tax_rate(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'Deletable VAT',
            'tax_rate_percent' => '5.00',
        ]);
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Deletable VAT']);

        /* Act */
        $response = $this->post('/tax_rates/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Deletable VAT']);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_tax_rate_name(): void
    {
        /**
         * POST /tax_rates/form
         * {
         *     "tax_rate_name": "",
         *     "tax_rate_percent": "21.00",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/tax_rates/form', [
            'tax_rate_name'    => '',
            'tax_rate_percent' => '21.00',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_fails_to_create_without_tax_rate_percent(): void
    {
        /**
         * POST /tax_rates/form
         * {
         *     "tax_rate_name": "Incomplete VAT",
         *     "tax_rate_percent": "",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/tax_rates/form', [
            'tax_rate_name'    => 'Incomplete VAT',
            'tax_rate_percent' => '',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_name' => 'Incomplete VAT']);
    }

    #[Test]
    public function it_fails_to_update_without_tax_rate_name(): void
    {
        /**
         * POST /tax_rates/form/{id}
         * {
         *     "tax_rate_name": "",
         *     "tax_rate_percent": "21.00",
         *     "btn_submit": "1"
         * }
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'Will Not Change',
            'tax_rate_percent' => '9.00',
        ]);

        /* Act */
        $response = $this->post('/tax_rates/form/' . $id, [
            'tax_rate_name'    => '',
            'tax_rate_percent' => '21.00',
            'btn_submit'       => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_name' => 'Will Not Change']);
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
        $response = $this->get('/tax_rates');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }
}
