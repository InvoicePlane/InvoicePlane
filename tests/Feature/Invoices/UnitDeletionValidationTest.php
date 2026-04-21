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
