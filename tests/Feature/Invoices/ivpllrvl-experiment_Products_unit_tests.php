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
class FamilyDeletionValidationTest extends AbstractServiceTestCase
{
    private FamilyService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new FamilyService();
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_family_without_products(): void
    {
        /** Arrange */
        $family = Family::factory()->create(['family_name' => 'Empty Family']);

        /** Act */
        $canDelete = $this->service->canDelete($family->family_id);
        $blockers  = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertTrue($canDelete);
        $this->assertEquals(0, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /** Arrange */
        $family = Family::factory()->create();
        Product::factory()->create(['family_id' => $family->family_id]);

        /** Act */
        $canDelete = $this->service->canDelete($family->family_id);
        $blockers  = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertGreaterThan(0, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_multiple_products(): void
    {
        /** Arrange */
        $family = Family::factory()->create();
        Product::factory()->count(5)->create(['family_id' => $family->family_id]);

        /** Act */
        $canDelete = $this->service->canDelete($family->family_id);
        $blockers  = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertEquals(5, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_after_products_removed(): void
    {
        /** Arrange */
        $family  = Family::factory()->create();
        $product = Product::factory()->create(['family_id' => $family->family_id]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($family->family_id));

        // Remove product
        $product->delete();

        /** Act */
        $canDelete = $this->service->canDelete($family->family_id);

        /* Assert */
        $this->assertTrue($canDelete);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_returns_correct_blocker_structure(): void
    {
        /** Arrange */
        $family = Family::factory()->create();

        /** Act */
        $blockers = $this->service->getDeletionBlockers($family->family_id);

        /* Assert */
        $this->assertIsArray($blockers);
        $this->assertArrayHasKey('products', $blockers);
    }
}

/**
 * ProductService Deletion Validation Tests.
 *
 * Tests business rules for product deletion:
 * - Products used in invoices cannot be deleted
 */
#[CoversClass(ProductService::class)]
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

#[CoversClass(ProductService::class)]
class ProductServiceTest extends AbstractServiceTestCase
{
    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('product_name', $rules);
        $this->assertArrayHasKey('product_price', $rules);
        $this->assertArrayHasKey('family_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('unit_id', $rules);
    }
}

/**
 * TaxRateService Deletion Validation Tests.
 *
 * Tests business rules for tax rate deletion:
 * - Tax rates used in products cannot be deleted
 * - Tax rates used in invoice items cannot be deleted
 * - Tax rates used in invoice tax rates cannot be deleted
 * - Tax rates used in quote items cannot be deleted
 * - Tax rates used in quote tax rates cannot be deleted
 */
#[CoversClass(TaxRateService::class)]
class TaxRateDeletionValidationTest extends AbstractServiceTestCase
{
    private TaxRateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaxRateService();
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_tax_rate_without_references(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create(['tax_rate_name' => 'Unused Tax Rate']);

        /** Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);
        $blockers  = $this->service->getDeletionBlockers($taxRate->tax_rate_id);

        /* Assert */
        $this->assertTrue($canDelete);
        $this->assertEquals(0, $blockers['products']);
        $this->assertEquals(0, $blockers['invoice_items']);
        $this->assertEquals(0, $blockers['invoice_tax_rates']);
        $this->assertEquals(0, $blockers['quote_items']);
        $this->assertEquals(0, $blockers['quote_tax_rates']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        Product::factory()->create(['tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);
        $blockers  = $this->service->getDeletionBlockers($taxRate->tax_rate_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertGreaterThan(0, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_invoice_items(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        InvoiceItem::factory()->create(['item_tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertFalse($canDelete);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_invoice_tax_rates(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        InvoiceTaxRate::factory()->create(['tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertFalse($canDelete);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_quote_items(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        QuoteItem::factory()->create(['item_tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertFalse($canDelete);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_quote_tax_rates(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        QuoteTaxRate::factory()->create(['tax_rate_id' => $taxRate->tax_rate_id]);

        /** Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertFalse($canDelete);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_returns_correct_blocker_counts(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();

        Product::factory()->count(2)->create(['tax_rate_id' => $taxRate->tax_rate_id]);
        InvoiceItem::factory()->count(3)->create(['item_tax_rate_id' => $taxRate->tax_rate_id]);
        QuoteItem::factory()->count(1)->create(['item_tax_rate_id' => $taxRate->tax_rate_id]);
        /** Act */
        $blockers = $this->service->getDeletionBlockers($taxRate->tax_rate_id);

        /* Assert */
        $this->assertEquals(2, $blockers['products']);
        $this->assertEquals(3, $blockers['invoice_items']);
        $this->assertEquals(1, $blockers['quote_items']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_after_all_references_removed(): void
    {
        /** Arrange */
        $taxRate = TaxRate::factory()->create();
        $product = Product::factory()->create(['tax_rate_id' => $taxRate->tax_rate_id]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($taxRate->tax_rate_id));

        // Remove reference
        $product->delete();

        /** Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertTrue($canDelete);
    }
}

/**
 * UnitService Deletion Validation Tests.
 *
 * Tests business rules for unit deletion:
 * - Units used in products cannot be deleted
 * - Units used in invoice items cannot be deleted
 * - Units used in quote items cannot be deleted
 */
#[CoversClass(UnitService::class)]
class UnitDeletionValidationTest extends AbstractServiceTestCase
{
    private UnitService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UnitService();
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_of_unit_without_references(): void
    {
        /** Arrange */
        $unit = Unit::factory()->create(['unit_name' => 'Unused Unit']);

        /** Act */
        $canDelete = $this->service->canDelete($unit->unit_id);
        $blockers  = $this->service->getDeletionBlockers($unit->unit_id);

        /* Assert */
        $this->assertTrue($canDelete);
        $this->assertEquals(0, $blockers['products']);
        $this->assertEquals(0, $blockers['invoice_items']);
        $this->assertEquals(0, $blockers['quote_items']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /** Arrange */
        $unit = Unit::factory()->create();
        Product::factory()->create(['unit_id' => $unit->unit_id]);

        /** Act */
        $canDelete = $this->service->canDelete($unit->unit_id);
        $blockers  = $this->service->getDeletionBlockers($unit->unit_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertGreaterThan(0, $blockers['products']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_invoice_items(): void
    {
        /** Arrange */
        $unit = Unit::factory()->create();
        InvoiceItem::factory()->create(['item_product_unit_id' => $unit->unit_id]);

        /** Act */
        $canDelete = $this->service->canDelete($unit->unit_id);
        $blockers  = $this->service->getDeletionBlockers($unit->unit_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertGreaterThan(0, $blockers['invoice_items']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_quote_items(): void
    {
        /** Arrange */
        $unit = Unit::factory()->create();
        QuoteItem::factory()->create(['item_product_unit_id' => $unit->unit_id]);

        /** Act */
        $canDelete = $this->service->canDelete($unit->unit_id);
        $blockers  = $this->service->getDeletionBlockers($unit->unit_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertGreaterThan(0, $blockers['quote_items']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_prevents_deletion_with_multiple_references(): void
    {
        /** Arrange */
        $unit = Unit::factory()->create();

        Product::factory()->count(2)->create(['unit_id' => $unit->unit_id]);
        InvoiceItem::factory()->count(3)->create(['item_product_unit_id' => $unit->unit_id]);
        QuoteItem::factory()->count(1)->create(['item_product_unit_id' => $unit->unit_id]);

        /** Act */
        $canDelete = $this->service->canDelete($unit->unit_id);
        $blockers  = $this->service->getDeletionBlockers($unit->unit_id);

        /* Assert */
        $this->assertFalse($canDelete);
        $this->assertEquals(2, $blockers['products']);
        $this->assertEquals(3, $blockers['invoice_items']);
        $this->assertEquals(1, $blockers['quote_items']);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Test]
    public function it_allows_deletion_after_references_removed(): void
    {
        /** Arrange */
        $unit    = Unit::factory()->create();
        $product = Product::factory()->create(['unit_id' => $unit->unit_id]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($unit->unit_id));

        // Remove reference
        $product->delete();

        /** Act */
        $canDelete = $this->service->canDelete($unit->unit_id);

        /* Assert */
        $this->assertTrue($canDelete);
    }
}

#[CoversClass(UnitService::class)]
class UnitServiceTest extends AbstractServiceTestCase
{
    private UnitService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new UnitService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('unit_name', $rules);
        $this->assertArrayHasKey('unit_name_plrl', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_string_when_unit_id_is_null(): void
    {
        $result = $this->service->getUnitName(null, 1);
        $this->assertEquals('', $result);
    }

    #[Test]
    public function it_gets_unit_name(): void
    {
        /* Arrange */
        $this->cleanupTables(['ip_units']);

        $unit = \Modules\Products\Models\Unit::create([
            'unit_name'      => 'Hour',
            'unit_name_plrl' => 'Hours',
        ]);

        /** Act */
        $singularName = $this->service->getUnitName($unit->unit_id, 1);
        $pluralName   = $this->service->getUnitName($unit->unit_id, 2);

        /* Assert */
        $this->assertEquals('Hour', $singularName);
        $this->assertEquals('Hours', $pluralName);
    }
}
