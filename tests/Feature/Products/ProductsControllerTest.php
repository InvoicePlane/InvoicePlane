<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class ProductsControllerTest extends AbstractTestCase
{
    private int $familyId;

    private int $taxRateId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->familyId  = $this->databaseInsert('ip_families', ['family_name' => 'Test Family']);
        $this->taxRateId = $this->databaseInsert('ip_tax_rates', [
            'tax_rate_name'    => 'Test Tax',
            'tax_rate_percent' => '21.00',
        ]);
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_products(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_products', $this->productRow(['product_name' => 'Listed Widget']));

        /* Act */
        $response = $this->get('/products');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseHas('ip_products', ['product_name' => 'Listed Widget']);
        $this->assertResponseBodyContains($response, '<html');
    }

    // -------------------------------------------------------------------------
    // Create
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_create_product_form(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->get('/products/form');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_creates_a_product(): void
    {
        /**
         * POST /products/form
         * {
         *     "product_name": "Acme Widget",
         *     "product_price": "19.99",
         *     "family_id": "<familyId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/products/form', [
            'product_name'        => 'Acme Widget',
            'product_sku'         => '',
            'product_description' => '',
            'product_price'       => '19.99',
            'purchase_price'      => '0.00',
            'family_id'           => (string) $this->familyId,
            'tax_rate_id'         => (string) $this->taxRateId,
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_products', ['product_name' => 'Acme Widget']);
    }

    #[Test]
    public function it_creates_a_product_with_full_details(): void
    {
        /**
         * POST /products/form
         * {
         *     "product_name": "Full Widget",
         *     "product_sku": "SKU-FULL-001",
         *     "product_description": "A full product",
         *     "product_price": "29.99",
         *     "purchase_price": "15.00",
         *     "family_id": "<familyId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/products/form', [
            'product_name'        => 'Full Widget',
            'product_sku'         => 'SKU-FULL-001',
            'product_description' => 'A full product',
            'product_price'       => '29.99',
            'purchase_price'      => '15.00',
            'family_id'           => (string) $this->familyId,
            'tax_rate_id'         => (string) $this->taxRateId,
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful create must redirect.');
        $this->assertDatabaseHas('ip_products', [
            'product_name' => 'Full Widget',
            'product_sku'  => 'SKU-FULL-001',
        ]);
    }

    // -------------------------------------------------------------------------
    // Update
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_showing_existing_product_name(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_products', $this->productRow(['product_name' => 'Editable Widget']));

        /* Act */
        $response = $this->get('/products/form/' . $id);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertResponseBodyContains($response, 'Editable Widget');
    }

    #[Test]
    public function it_updates_a_product(): void
    {
        /**
         * POST /products/form/{id}
         * {
         *     "product_name": "Renamed Widget",
         *     "product_price": "24.99",
         *     "family_id": "<familyId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_products', $this->productRow(['product_name' => 'Original Widget']));

        /* Act */
        $response = $this->post('/products/form/' . $id, [
            'product_name'        => 'Renamed Widget',
            'product_sku'         => '',
            'product_description' => '',
            'product_price'       => '24.99',
            'purchase_price'      => '0.00',
            'family_id'           => (string) $this->familyId,
            'tax_rate_id'         => (string) $this->taxRateId,
            'btn_submit'          => '1',
        ]);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Successful update must redirect.');
        $this->assertDatabaseHas('ip_products', ['product_id' => $id, 'product_name' => 'Renamed Widget']);
        $this->assertDatabaseMissing('ip_products', ['product_id' => $id, 'product_name' => 'Original Widget']);
    }

    // -------------------------------------------------------------------------
    // Delete
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_product(): void
    {
        /* Arrange */
        $id = $this->databaseInsert('ip_products', $this->productRow(['product_name' => 'Deletable Widget']));
        $this->assertDatabaseHas('ip_products', ['product_name' => 'Deletable Widget']);

        /* Act */
        $response = $this->post('/products/delete/' . $id, []);

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Delete must redirect.');
        $this->assertDatabaseMissing('ip_products', ['product_name' => 'Deletable Widget']);
    }

    // -------------------------------------------------------------------------
    // Validation failures — missing required fields
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_product_name(): void
    {
        /**
         * POST /products/form
         * {
         *     "product_name": "",
         *     "product_price": "9.99",
         *     "family_id": "<familyId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/products/form', [
            'product_name'        => '',
            'product_sku'         => '',
            'product_description' => '',
            'product_price'       => '9.99',
            'purchase_price'      => '0.00',
            'family_id'           => (string) $this->familyId,
            'tax_rate_id'         => (string) $this->taxRateId,
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

    #[Test]
    public function it_fails_to_create_without_product_price(): void
    {
        /**
         * POST /products/form
         * {
         *     "product_name": "No Price Widget",
         *     "product_price": "",
         *     "family_id": "<familyId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */

        /* Act */
        $response = $this->post('/products/form', [
            'product_name'        => 'No Price Widget',
            'product_sku'         => '',
            'product_description' => '',
            'product_price'       => '',
            'purchase_price'      => '0.00',
            'family_id'           => (string) $this->familyId,
            'tax_rate_id'         => (string) $this->taxRateId,
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseMissing('ip_products', ['product_name' => 'No Price Widget']);
    }

    #[Test]
    public function it_fails_to_update_without_product_name(): void
    {
        /**
         * POST /products/form/{id}
         * {
         *     "product_name": "",
         *     "product_price": "9.99",
         *     "family_id": "<familyId>",
         *     "btn_submit": "1"
         * }.
         */

        /* Arrange */
        $id = $this->databaseInsert('ip_products', $this->productRow(['product_name' => 'Will Not Change']));

        /* Act */
        $response = $this->post('/products/form/' . $id, [
            'product_name'        => '',
            'product_sku'         => '',
            'product_description' => '',
            'product_price'       => '9.99',
            'purchase_price'      => '0.00',
            'family_id'           => (string) $this->familyId,
            'tax_rate_id'         => (string) $this->taxRateId,
            'btn_submit'          => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
        $this->assertDatabaseHas('ip_products', ['product_name' => 'Will Not Change']);
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
        $response = $this->get('/products');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
    }

    private function productRow(array $overrides = []): array
    {
        return array_merge([
            'product_name'        => 'Default Widget',
            'product_sku'         => '',
            'product_description' => '',
            'product_price'       => '9.99',
            'purchase_price'      => '0.00',
            'family_id'           => $this->familyId,
            'tax_rate_id'         => $this->taxRateId,
        ], $overrides);
    }
}
