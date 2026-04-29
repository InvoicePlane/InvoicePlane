<?php

namespace Tests\Unit\Products;

use Mdl_Products;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Products::class)]
class ProductsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('products/mdl_products');
        $this->model = $this->CI->mdl_products;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_products', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('product_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('product_name', $rules);
        $this->assertArrayHasKey('product_price', $rules);
        $this->assertArrayHasKey('family_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('unit_id', $rules);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_default_order_by_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_order_by'));
    }

    #[Test]
    public function it_has_default_join_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_join'));
    }

    #[Test]
    public function it_has_by_product_filter_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'by_product'));
    }

    #[Test]
    public function it_has_by_family_filter_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'by_family'));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_and_retrieves_product(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $name       = 'TestProduct_' . uniqid();
        $product_id = $this->seedProduct([
            'product_name'  => $name,
            'product_price' => 99.99,
        ]);

        /* Act */
        $row = $this->databaseFetchOne('ip_products', ['product_id' => $product_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($name, $row['product_name']);
        $this->assertEquals(99.99, (float) $row['product_price']);

        /* Cleanup */
        $this->databaseDelete('ip_products', ['product_id' => $product_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_filters_products_by_name(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $name       = 'FindMe_' . uniqid();
        $product_id = $this->seedProduct(['product_name' => $name]);

        /* Act */
        $this->model->by_product($name);
        $results = $this->model->get(false)->result();

        /* Assert */
        $this->assertNotEmpty($results);
        $found = array_filter($results, fn ($r) => (int) $r->product_id === $product_id);
        $this->assertNotEmpty($found);

        /* Cleanup */
        $this->databaseDelete('ip_products', ['product_id' => $product_id]);
    }
}
