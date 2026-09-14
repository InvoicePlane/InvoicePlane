<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * Products AJAX controller — application/modules/products/controllers/Ajax.php.
 *
 * $ajax_controller = true. Covers the item-picker modal
 * (modal_product_lookups, filterable by product name) and bulk selection
 * (process_product_selections). Restores coverage dropped when the old
 * ProductsTest.php was absorbed into ProductsControllerTest without ever
 * creating this file.
 */
#[Group('products')]
#[CoversClass(\Ajax::class)]
class ProductsAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // modal_product_lookups
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_full_lookup_modal_with_no_filters(): void
    {
        /* Arrange */
        $this->seedProduct(['product_name' => 'Modal Product Marker']);

        /* Act */
        $response = $this->ajax('GET', '/products/ajax/modal_product_lookups', []);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Modal Product Marker');
        $this->assertResponseBodyContains($response, 'filter_product');
    }

    #[Test]
    public function it_filters_the_lookup_table_by_product_name(): void
    {
        /* Arrange */
        $this->seedProduct(['product_name' => 'Filter Match Product']);
        $this->seedProduct(['product_name' => 'Other Product']);

        /* Act */
        $response = $this->request('GET', '/products/ajax/modal_product_lookups', ['filter_product' => 'Filter Match Product'], [], true);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Filter Match Product');
        $this->assertResponseBodyNotContains($response, 'Other Product');
    }

    // -------------------------------------------------------------------------
    // process_product_selections
    // -------------------------------------------------------------------------

    #[Test]
    public function it_processes_a_product_selection(): void
    {
        /**
         * POST /products/ajax/process_product_selections
         * { "product_ids": ["<seeded product id>"] }.
         */
        /* Arrange */
        $productId = $this->seedProduct(['product_name' => 'Selected Product', 'product_price' => '42.00']);

        /* Act */
        $response = $this->ajax('POST', '/products/ajax/process_product_selections', ['product_ids' => [(string) $productId]]);
        $json     = json_decode($response->body(), true);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Selected Product');
        self::assertSame((string) $productId, (string) ($json[0]['product_id'] ?? ''));
    }

    #[Test]
    public function it_returns_an_empty_result_when_no_product_ids_are_selected(): void
    {
        /* Arrange */
        $this->seedProduct(['product_name' => 'Not Selected Product']);

        /* Act */
        $response = $this->ajax('POST', '/products/ajax/process_product_selections', []);

        /* Assert */
        self::assertSame([], json_decode($response->body(), true));
        $this->assertResponseBodyNotContains($response, 'Not Selected Product');
    }

    private function seedProduct(array $overrides = []): int
    {
        return $this->databaseInsert('ip_products', array_merge([
            'family_id'           => 0,
            'product_sku'         => 'SKU-' . random_int(1000, 9999),
            'product_name'        => 'Seeded Product',
            'product_description' => '',
            'product_price'       => 10,
            'purchase_price'      => 0,
            'tax_rate_id'         => 0,
        ], $overrides));
    }
}
