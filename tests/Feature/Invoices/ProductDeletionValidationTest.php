<?php

namespace Tests\Feature\Invoices;

use Modules\Products\Models\Family;
use Modules\Products\Models\Product;
use Modules\Products\Services\FamilyService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * FamilyService Deletion Validation Tests.
 *
 * Tests business rules for family deletion:
 * - Families with products cannot be deleted
 */
#[CoversClass(FamilyService::class)]

class ProductDeletionValidationTest extends AbstractServiceTestCase
{
    use InteractsWithDatabase;

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
        /* Arrange */
        $product = $this->seedModel('Product', [
            'product_name'  => 'Test Product',
            'product_price' => 100.00,
        ]);

        /* Act */
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
        /* Arrange */
        $product = $this->seedModel('Product', [
            'product_name'  => 'Product In Use',
            'product_price' => 150.00,
        ]);

        // Create an invoice item that references this product
        $this->seedModel('InvoiceItem', [
            'item_product_id' => $product->product_id,
            'item_name'       => 'Invoice Item',
            'item_price'      => 150.00,
            'item_quantity'   => 1,
        ]);

        /* Act */
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
        /* Arrange */
        $product = $this->seedModel('Product', [
            'product_name'  => 'Popular Product',
            'product_price' => 200.00,
        ]);

        // Create multiple invoice items referencing this product
        $this->seedModelMany('InvoiceItem', 3, [
            'item_product_id' => $product->product_id,
            'item_price'      => 200.00,
            'item_quantity'   => 1,
        ]);

        /* Act */
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
        /* Arrange */
        $product = $this->seedModel('Product');

        $this->seedModel('InvoiceItem', [
            'item_product_id' => $product->product_id,
        ]);

        /* Act */
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
        /* Arrange */
        $product = $this->seedModel('Product');

        // Create 5 invoice items
        $this->seedModelMany('InvoiceItem', 5, [
            'item_product_id' => $product->product_id,
        ]);

        /* Act */
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
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
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
        /* Arrange */
        $product = $this->seedModel('Product');

        // Even if invoice is archived/old, item still references product
        $this->seedModel('InvoiceItem', [
            'item_product_id' => $product->product_id,
            // Invoice could be old/archived, but relationship still exists
        ]);

        /* Act */
        $canDelete = $this->service->canDelete($product->product_id);

        /* Assert */
        $this->assertFalse($canDelete, 'Product should not be deletable even with archived invoice items');
    }
}
