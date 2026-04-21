<?php

namespace tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use src\Models\Family;
use src\Services\FamiliesService;
use Tests\TestCase;

class ProductsServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProductsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductsService();
    }

    #[Test]
    public function it_returns_products_by_ids(): void
    {
        // Arrange
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

        // Act
        $result = $this->service->getByIds([
            $product1->product_id,
            $product3->product_id,
        ]);

        // Assert
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('product_id', $product1->product_id));
        $this->assertTrue($result->contains('product_id', $product3->product_id));
        $this->assertFalse($result->contains('product_id', $product2->product_id));
    }

    #[Test]
    public function it_returns_empty_collection_when_no_matching_ids(): void
    {
        // Arrange
        Product::create([
            'product_name'        => 'Product 1',
            'product_description' => 'Description 1',
        ]);

        // Act
        $result = $this->service->getByIds([99999, 88888]);

        // Assert
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_returns_empty_collection_when_empty_array_provided(): void
    {
        // Act
        $result = $this->service->getByIds([]);

        // Assert
        $this->assertCount(0, $result);
    }

    #[Test]
    public function it_handles_duplicate_ids_in_array(): void
    {
        // Arrange
        $product = Product::create([
            'product_name'        => 'Product 1',
            'product_description' => 'Description 1',
        ]);

        // Act: Pass same ID multiple times
        $result = $this->service->getByIds([
            $product->product_id,
            $product->product_id,
            $product->product_id,
        ]);

        // Assert: Should only return one instance
        $this->assertCount(1, $result);
    }
}
