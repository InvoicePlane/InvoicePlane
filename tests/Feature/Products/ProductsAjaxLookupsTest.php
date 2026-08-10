<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class ProductsAjaxLookupsTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_renders_the_full_lookup_modal_with_no_filters(): void
    {
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
}
