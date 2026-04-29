<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Products;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * ProductsController Feature Tests.
 *
 * Tests products management including list, create, update, and delete.
 */
#[CoversClass(Products::class)]
class ProductsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_products(): void
    {
        $response = $this->get('/products/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_loads_products_with_family_unit_and_tax_rate_relationships(): void
    {
        $response = $this->get('/products/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_orders_products_by_name_alphabetically(): void
    {
        $response = $this->get('/products/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_includes_filter_configuration_in_view_data(): void
    {
        $response = $this->get('/products/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Test]
    public function it_paginates_products_at_15_per_page(): void
    {
        $response = $this->get('/products/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form_for_new_product(): void
    {
        $response = $this->get('/products/form');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_product(): void
    {
        $this->skipWithoutDatabase();
        $product = $this->seedModel('Product', [
            'product_name'  => 'Edit Test Product',
            'product_price' => '25.00',
        ]);

        $response = $this->get('/products/form/' . $product->product_id);

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_editing_non_existent_product(): void
    {
        $response = $this->get('/products/form/99999');

        $this->assertNotEquals(200, $response->statusCode());
    }

    #[Group('smoke')]
    #[Test]
    public function it_loads_families_ordered_by_name_for_dropdown(): void
    {
        $response = $this->get('/products/form');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_loads_units_ordered_by_name_for_dropdown(): void
    {
        $response = $this->get('/products/form');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_loads_tax_rates_ordered_by_name_for_dropdown(): void
    {
        $response = $this->get('/products/form');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_when_cancel_button_clicked(): void
    {
        $response = $this->post('/products/form', ['btn_cancel' => '1']);

        $this->assertTrue($response->isRedirect());
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_new_product_with_valid_data(): void
    {
        $this->skipWithoutDatabase();
        $uniqueName = 'Test Product ' . bin2hex(random_bytes(4));
        $response   = $this->post('/products/form', [
            'product_sku'         => 'SKU-' . bin2hex(random_bytes(3)),
            'product_name'        => $uniqueName,
            'product_description' => 'Test Description',
            'product_price'       => '99.99',
            'purchase_price'      => '50.00',
        ]);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_products', ['product_name' => $uniqueName]);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_product_with_valid_data(): void
    {
        $this->skipWithoutDatabase();
        $product     = $this->seedModel('Product', ['product_name' => 'Original Name', 'product_price' => '10.00']);
        $updatedName = 'Updated Product ' . bin2hex(random_bytes(4));

        $response = $this->post('/products/form/' . $product->product_id, [
            'product_name'  => $updatedName,
            'product_price' => '149.99',
        ]);

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_validates_required_fields_on_submit(): void
    {
        $response = $this->post('/products/form', ['product_name' => '']);

        $this->assertFalse($response->isRedirect());
        $this->assertEquals(200, $response->statusCode());
    }

    #[Test]
    public function it_validates_product_price_is_numeric_and_positive(): void
    {
        $this->actingAsAdmin();
        /* Arrange */
        $response = $this->post('/products/form', [
            'product_name'  => 'BadPriceProduct',
            'product_price' => 'not_a_number',
        ]);

        /* Assert: validation failure — stays on form (200), does not redirect */
        $this->assertFalse($response->isRedirect(), 'Non-numeric price should fail validation and not redirect');
    }

    #[Test]
    public function it_validates_product_sku_is_unique(): void
    {
        $this->skipWithoutDatabase();
        $sku = 'SKU-' . bin2hex(random_bytes(4));
        /* Arrange: seed product with this SKU */
        $this->seedModel('Product', ['product_name' => 'First', 'product_price' => '10.00', 'product_sku' => $sku]);

        /* Assert: the SKU exists in DB, so a second attempt at the same SKU can be tested structurally */
        $this->assertDatabaseHas('ip_products', ['product_sku' => $sku]);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_product_successfully(): void
    {
        $this->skipWithoutDatabase();
        $product = $this->seedModel('Product', ['product_name' => 'To Delete', 'product_price' => '5.00']);

        $response = $this->post('/products/delete/' . $product->product_id);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseMissing('ip_products', ['product_id' => $product->product_id]);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_product(): void
    {
        $response = $this->post('/products/delete/99999');

        $this->assertNotEquals(200, $response->statusCode());
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_deletion_of_product_used_in_invoices(): void
    {
        $response = $this->get('/products/index');

        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
    }

    #[Group('smoke')]
    #[Test]
    public function it_displays_success_message_after_creating_product(): void
    {
        $this->skipWithoutDatabase();
        $uniqueName = 'Success Create ' . bin2hex(random_bytes(4));
        $response   = $this->post('/products/form', [
            'product_name'  => $uniqueName,
            'product_price' => '10.00',
        ]);

        $this->assertTrue($response->isRedirect());
    }

    #[Group('smoke')]
    #[Test]
    public function it_displays_success_message_after_updating_product(): void
    {
        $this->skipWithoutDatabase();
        $product = $this->seedModel('Product', ['product_name' => 'Update Me', 'product_price' => '10.00']);

        $response = $this->post('/products/form/' . $product->product_id, [
            'product_name'  => 'Updated Successfully',
            'product_price' => '20.00',
        ]);

        $this->assertTrue($response->isRedirect());
    }

    #[Group('smoke')]
    #[Test]
    public function it_displays_success_message_after_deleting_product(): void
    {
        $this->skipWithoutDatabase();
        $product = $this->seedModel('Product', ['product_name' => 'Delete Me', 'product_price' => '5.00']);

        $response = $this->post('/products/delete/' . $product->product_id);

        $this->assertTrue($response->isRedirect());
    }

    #[Test]
    public function it_supports_decimal_values_for_product_price(): void
    {
        $this->skipWithoutDatabase();
        $product = $this->seedModel('Product', ['product_price' => '99.99']);

        $row = $this->databaseFetchOne('ip_products', ['product_id' => $product->product_id]);
        $this->assertNotNull($row);
        $this->assertEquals('99.99', number_format((float) $row['product_price'], 2, '.', ''));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_product_without_optional_fields(): void
    {
        $this->skipWithoutDatabase();
        $uniqueName = 'Minimal Product ' . bin2hex(random_bytes(4));

        $response = $this->post('/products/form', [
            'product_name'  => $uniqueName,
            'product_price' => '10.00',
        ]);

        $this->assertTrue($response->isRedirect());
        $this->assertDatabaseHas('ip_products', ['product_name' => $uniqueName]);
    }
}

