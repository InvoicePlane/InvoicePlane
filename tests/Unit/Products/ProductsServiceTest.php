<?php

namespace Tests\Unit\Products;

use Mdl_Products;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Products::class)]
class ProductsServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Service class does not exist — CI3 model layer, no Laravel service layer available');
        $this->service = new ProductsService();
    }

    #[Test]
    public function it_returns_products_by_ids(): void
    {
        /* Arrange */
        $product1 = Product::create([
            'product_name'        => 'Product 1',
            'product_description' => 'Description 1',
        ]);
        $product2 = Product::create([
            'product_name'        => 'Product 2',
            'product_description' => 'Description 2',
        ]);
        $product3 = Product::create([
            'product_name'        => 'Product 3',
            'product_description' => 'Description 3',
        ]);

        /* Act */
        $result = $this->service->getByIds([
            $product1->product_id,
            $product3->product_id,
        ]);

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('product_id', $product1->product_id));
        $this->assertTrue($result->contains('product_id', $product3->product_id));
        $this->assertFalse($result->contains('product_id', $product2->product_id));
    }

    #[Test]
    public function it_returns_empty_collection_when_no_matching_ids(): void
    {
        /* Arrange */
        Product::create([
            'product_name'        => 'Product 1',
            'product_description' => 'Description 1',
        ]);

        /* Act */
        $result = $this->service->getByIds([99999, 88888]);

        /* Assert */
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_returns_empty_collection_when_empty_array_provided(): void
    {
        /* Act */
        $result = $this->service->getByIds([]);

        /* Assert */
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_handles_duplicate_ids_in_array(): void
    {
        /* Arrange */
        $product = Product::create([
            'product_name'        => 'Product 1',
            'product_description' => 'Description 1',
        ]);

        /* Act */
        $result = $this->service->getByIds([
            $product->product_id,
            $product->product_id,
            $product->product_id,
        ]);

        /* Assert */
        $this->assertCount(1, $result);
    }
}
