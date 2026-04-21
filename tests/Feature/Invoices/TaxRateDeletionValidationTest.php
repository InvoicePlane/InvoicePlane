<?php

namespace Tests\Feature\Invoices;

use Modules\Products\Models\Family;
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

class TaxRateDeletionValidationTest extends AbstractServiceTestCase
{
    use InteractsWithDatabase;

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
        $taxRate = $this->seedModel('TaxRate', ['tax_rate_name' => 'Unused Tax Rate']);

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
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('Product', ['tax_rate_id' => $taxRate->tax_rate_id]);

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
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('InvoiceItem', ['item_tax_rate_id' => $taxRate->tax_rate_id]);

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
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('InvoiceTaxRate', ['tax_rate_id' => $taxRate->tax_rate_id]);

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
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('QuoteItem', ['item_tax_rate_id' => $taxRate->tax_rate_id]);

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
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('QuoteTaxRate', ['tax_rate_id' => $taxRate->tax_rate_id]);

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
        $taxRate = $this->seedModel('TaxRate');

        $this->seedModelMany('Product', 2, ['tax_rate_id' => $taxRate->tax_rate_id]);
        $this->seedModelMany('InvoiceItem', 3, ['item_tax_rate_id' => $taxRate->tax_rate_id]);
        $this->seedModelMany('QuoteItem', 1, ['item_tax_rate_id' => $taxRate->tax_rate_id]);
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
        $taxRate = $this->seedModel('TaxRate');
        $product = $this->seedModel('Product', ['tax_rate_id' => $taxRate->tax_rate_id]);

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
