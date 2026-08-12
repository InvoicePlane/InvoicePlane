<?php

declare(strict_types=1);

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\AbstractTestCase;

class ProductsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function setUpFamiliesController(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_families(): void

        {

            $this->setUpFamiliesController();

            /* Arrange */

            $this->databaseInsert('ip_families', ['family_name' => 'Listed Family']);



            /* Act */

            $response = $this->get('/families');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Family');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_family_form(): void

        {

            $this->setUpFamiliesController();

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

            $this->setUpFamiliesController();

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

            $this->assertResponseRedirectsToRoute($response, 'families');

            $this->assertDatabaseHas('ip_families', ['family_name' => 'Electronics']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_family_name(): void

        {

            $this->setUpFamiliesController();

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

            $this->setUpFamiliesController();

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

            $this->assertResponseRedirectsToRoute($response, 'families');

            $this->assertDatabaseHas('ip_families', ['family_name' => 'Renamed Family']);

            $this->assertDatabaseMissing('ip_families', ['family_name' => 'Original Family']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_family(): void

        {

            $this->setUpFamiliesController();

            /* Arrange */

            $id = $this->databaseInsert('ip_families', ['family_name' => 'Deletable Family']);

            $this->assertDatabaseHas('ip_families', ['family_name' => 'Deletable Family']);



            /* Act */

            $response = $this->post('/families/delete/' . $id, []);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'families');

            $this->assertDatabaseMissing('ip_families', ['family_name' => 'Deletable Family']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_family_name(): void

        {

            $this->setUpFamiliesController();

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

            $this->setUpFamiliesController();

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

            $this->setUpFamiliesController();

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

            $this->setUpFamiliesController();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/families');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
    protected function setUpProductsAjaxController(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]
    #[Group('smoke')]

    public function it_returns_a_successful_response_or_redirect(): void

        {

            $this->setUpProductsAjaxController();

            /* Arrange */

            $this->databaseInsert('ip_products', [

                'product_name'        => 'Ajax Widget Beta',

                'family_id'           => 0,

                'product_sku'         => 'SKU-AJAX-001',

                'product_description' => 'An ajax test product',

                'product_price'       => '19.99',

                'purchase_price'      => '0.00',

                'tax_rate_id'         => 0,

            ]);



            /* Act */

            $response = $this->get('/products');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Ajax Widget Beta');

        }
    #[Test]

    public function it_redirects_a_guest_to_login_from_productsajaxcontroller(): void

        {

            $this->setUpProductsAjaxController();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/products');



            /* Assert */

            self::assertTrue(

                $response->isRedirect(),

                sprintf('Unauthenticated GET [/products] must redirect. Got [%d].', $response->statusCode())

            );

        }
    protected function setUpProductsAjaxLookups(): void

        {



            $this->actingAsAdmin();

        }
    #[Test]

    public function it_renders_the_full_lookup_modal_with_no_filters(): void

        {

            $this->setUpProductsAjaxLookups();

            /* Arrange */

            $this->seedProduct(['product_name' => 'Modal Product Marker']);



            /* Act */

            $response = $this->ajax('GET', '/products/ajax/modal_product_lookups', []);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseHasNoPhpErrors($response);

        }
    #[Test]

    public function it_filters_the_lookup_table_by_product_name(): void

        {

            $this->setUpProductsAjaxLookups();

            /* Arrange */

            $this->seedProduct(['product_name' => 'Filter Match Product']);

            $this->seedProduct(['product_name' => 'Other Product']);



            /* Act */

            $response = $this->request('GET', '/products/ajax/modal_product_lookups', ['filter_product' => 'Filter Match Product'], [], true);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Filter Match Product');

            $this->assertResponseBodyNotContains($response, 'Other Product');

        }
    #[Test]

    public function it_processes_a_product_selection(): void

        {

            $this->setUpProductsAjaxLookups();

            /* Arrange */

            $productId = $this->seedProduct(['product_name' => 'Selected Product', 'product_price' => '42.00']);



            /* Act */

            $response = $this->ajax('POST', '/products/ajax/process_product_selections', ['product_ids' => [(string) $productId]]);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Selected Product');

        }
    #[Test]

    public function it_returns_an_empty_result_when_no_product_ids_are_selected(): void

        {

            $this->setUpProductsAjaxLookups();

            /* Arrange */

            /* Act */

            $response = $this->ajax('POST', '/products/ajax/process_product_selections', []);



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            self::assertSame([], json_decode($response->body(), true));

        }
    private function seedProduct(array $overrides = []): int

        {

            return $this->databaseInsert('ip_products', array_merge([

                'product_sku'         => 'SKU-' . bin2hex(random_bytes(3)),

                'family_id'           => 0,

                'product_name'        => 'Lookup Product',

                'product_description' => '',

                'product_price'       => '10.00',

                'tax_rate_id'         => 0,

                'unit_id'             => 0,

                'purchase_price'      => '0.00',

                'provider_name'       => '',

                'product_tariff'      => 0,

            ], $overrides));

        }
    private int $familyId;
    private int $taxRateId;
    protected function setUpProductsController(): void

        {



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

            $this->setUpProductsController();

            /* Arrange */

            $this->databaseInsert('ip_products', $this->productRow(['product_name' => 'Listed Widget']));



            /* Act */

            $response = $this->get('/products');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Widget');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_product_form(): void

        {

            $this->setUpProductsController();

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

            $this->setUpProductsController();

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

            $this->assertResponseRedirectsToRoute($response, 'products');

            $this->assertDatabaseHas('ip_products', ['product_name' => 'Acme Widget']);

        }
    #[Test]

    public function it_creates_a_product_with_full_details(): void

        {

            $this->setUpProductsController();

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

            $this->assertResponseRedirectsToRoute($response, 'products');

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

            $this->setUpProductsController();

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

            $this->setUpProductsController();

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

            $this->assertResponseRedirectsToRoute($response, 'products');

            $this->assertDatabaseHas('ip_products', ['product_id' => $id, 'product_name' => 'Renamed Widget']);

            $this->assertDatabaseMissing('ip_products', ['product_id' => $id, 'product_name' => 'Original Widget']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_product(): void

        {

            $this->setUpProductsController();

            /* Arrange */

            $id = $this->databaseInsert('ip_products', $this->productRow(['product_name' => 'Deletable Widget']));

            $this->assertDatabaseHas('ip_products', ['product_name' => 'Deletable Widget']);



            /* Act */

            $response = $this->post('/products/delete/' . $id, []);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'products');

            $this->assertDatabaseMissing('ip_products', ['product_name' => 'Deletable Widget']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_product_name(): void

        {

            $this->setUpProductsController();

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

            $this->assertDatabaseCount('ip_products', 0);

        }
    #[Test]

    public function it_fails_to_create_without_product_price(): void

        {

            $this->setUpProductsController();

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

            $this->setUpProductsController();

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

    public function it_redirects_a_guest_to_login_from_productscontroller(): void

        {

            $this->setUpProductsController();

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
    protected function setUpUnitsController(): void

        {



            $this->actingAsAdmin();

        }



        // -------------------------------------------------------------------------

        // List

        // -------------------------------------------------------------------------
    #[Test]

    public function it_lists_units(): void

        {

            $this->setUpUnitsController();

            /* Arrange */

            $this->databaseInsert('ip_units', [

                'unit_name'      => 'Listed Unit',

                'unit_name_plrl' => 'Listed Units',

            ]);



            /* Act */

            $response = $this->get('/units');



            /* Assert */

            $this->assertResponseStatusCode($response, 200);

            $this->assertResponseBodyContains($response, 'Listed Unit');

        }



        // -------------------------------------------------------------------------

        // Create

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_create_unit_form(): void

        {

            $this->setUpUnitsController();

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

            $this->setUpUnitsController();

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

            $this->assertResponseRedirectsToRoute($response, 'units');

            $this->assertDatabaseHas('ip_units', ['unit_name' => 'Kilogram']);

        }



        // -------------------------------------------------------------------------

        // Update

        // -------------------------------------------------------------------------
    #[Test]

    public function it_renders_the_edit_form_showing_existing_unit_name(): void

        {

            $this->setUpUnitsController();

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

            $this->setUpUnitsController();

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

            $this->assertResponseRedirectsToRoute($response, 'units');

            $this->assertDatabaseHas('ip_units', ['unit_name' => 'Renamed Unit']);

            $this->assertDatabaseMissing('ip_units', ['unit_name' => 'Original Unit']);

        }



        // -------------------------------------------------------------------------

        // Delete

        // -------------------------------------------------------------------------
    #[Test]

    public function it_deletes_a_unit(): void

        {

            $this->setUpUnitsController();

            /* Arrange */

            $id = $this->databaseInsert('ip_units', [

                'unit_name'      => 'Deletable Unit',

                'unit_name_plrl' => 'Deletable Units',

            ]);

            $this->assertDatabaseHas('ip_units', ['unit_name' => 'Deletable Unit']);



            /* Act */

            $response = $this->post('/units/delete/' . $id, []);



            /* Assert */

            $this->assertResponseRedirectsToRoute($response, 'units');

            $this->assertDatabaseMissing('ip_units', ['unit_name' => 'Deletable Unit']);

        }



        // -------------------------------------------------------------------------

        // Validation failures — missing required fields

        // -------------------------------------------------------------------------
    #[Test]

    public function it_fails_to_create_without_unit_name(): void

        {

            $this->setUpUnitsController();

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

            $this->setUpUnitsController();

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

            $this->setUpUnitsController();

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

            $this->setUpUnitsController();

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

    public function it_redirects_a_guest_to_login_from_unitscontroller(): void

        {

            $this->setUpUnitsController();

            /* Arrange */

            $this->actingAsGuest();



            /* Act */

            $response = $this->get('/units');



            /* Assert */

            self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');

        }
}
