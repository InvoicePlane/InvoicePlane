<?php

namespace Modules\Products\Tests\Unit;

use Modules\Products\Models\Family;
use Modules\Products\Models\Product;
use Modules\Products\Services\FamilyService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

/**
 * FamilyService Deletion Validation Tests.
 *
 * Tests business rules for family deletion:
 * - Families with products cannot be deleted
 */
#[CoversClass(FamilyService::class)]

class ProductDeletionValidationTest extends AbstractServiceTestCase
{
    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductService();
    }

    /**
     * Test that a product without invoice items can be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_product_without_invoice_items(): void
    {
        /** Arrange */
        $product = Product::factory()->create([
            'product_name'  => 'Test Product',
            'product_price' => 100.00,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($product->product_id);

        /* Assert */
        $this->assertTrue($canDelete, 'Product without invoice items should be deletable');
    }

    /**
     * Test that a product with invoice items cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_of_product_with_invoice_items(): void
    {
        /** Arrange */
        $product = Product::factory()->create([
            'product_name'  => 'Product In Use',
            'product_price' => 150.00,
        ]);

        // Create an invoice item that references this product
        InvoiceItem::factory()->create([
            'item_product_id' => $product->product_id,
            'item_name'       => 'Invoice Item',
            'item_price'      => 150.00,
            'item_quantity'   => 1,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($product->product_id);

        /* Assert */
        $this->assertFalse($canDelete, 'Product with invoice items should NOT be deletable');
    }

    /**
     * Test that invoice item count is correctly returned.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_returns_correct_invoice_item_count(): void
    {
        /** Arrange */
        $product = Product::factory()->create([
            'product_name'  => 'Popular Product',
            'product_price' => 200.00,
        ]);

        // Create multiple invoice items referencing this product
        InvoiceItem::factory()->count(3)->create([
            'item_product_id' => $product->product_id,
            'item_price'      => 200.00,
            'item_quantity'   => 1,
        ]);

        /** Act */
        $itemCount = $this->service->getInvoiceItemCount($product->product_id);

        /* Assert */
        $this->assertEquals(3, $itemCount, 'Should return correct count of invoice items');
    }

    /**
     * Test that product with single invoice item cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_single_invoice_item(): void
    {
        /** Arrange */
        $product = Product::factory()->create();

        InvoiceItem::factory()->create([
            'item_product_id' => $product->product_id,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($product->product_id);
        $itemCount = $this->service->getInvoiceItemCount($product->product_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertEquals(1, $itemCount);
    }

    /**
     * Test that product with multiple invoice items cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_multiple_invoice_items(): void
    {
        /** Arrange */
        $product = Product::factory()->create();

        // Create 5 invoice items
        InvoiceItem::factory()->count(5)->create([
            'item_product_id' => $product->product_id,
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($product->product_id);
        $itemCount = $this->service->getInvoiceItemCount($product->product_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertEquals(5, $itemCount);
    }

    /**
     * Test that non-existent product returns zero item count.
     */
    #[Group('edge-cases')]
    #[Group('deletion')]
    #[Test]
    public function it_returns_zero_count_for_nonexistent_product(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $itemCount = $this->service->getInvoiceItemCount($nonexistentId);
        $canDelete = $this->service->canDelete($nonexistentId);

        /* Assert */
        $this->assertEquals(0, $itemCount);
        $this->assertTrue($canDelete, 'Non-existent product should be "deletable" (returns true)');
    }

    /**
     * Test deletion prevention with archived invoice items.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_even_with_archived_invoice_items(): void
    {
        /** Arrange */
        $product = Product::factory()->create();

        // Even if invoice is archived/old, item still references product
        InvoiceItem::factory()->create([
            'item_product_id' => $product->product_id,
            // Invoice could be old/archived, but relationship still exists
        ]);

        /** Act */
        $canDelete = $this->service->canDelete($product->product_id);

        /* Assert */
        $this->assertFalse($canDelete, 'Product should not be deletable even with archived invoice items');
    }
}
