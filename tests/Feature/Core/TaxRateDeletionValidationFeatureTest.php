<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tax_Rates;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(Tax_Rates::class)]
#[CoversClass(Tests\Feature\Core\TaxRateDeletionValidationFeature::class)]

class TaxRateDeletionValidationFeatureTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_tax_rate_without_references(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate', ['tax_rate_name' => 'Deletable']);

        /* Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('Product', ['tax_rate_id' => $taxRate->tax_rate_id]);

        /* Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_invoice_items(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('InvoiceItem', ['item_tax_rate_id' => $taxRate->tax_rate_id]);

        /* Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_quote_items(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('QuoteItem', ['item_tax_rate_id' => $taxRate->tax_rate_id]);

        /* Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_tax_rate_id(): void
    {
        /* Arrange */
        $invalidId = -1;

        /* Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $invalidId]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_nonexistent_tax_rate_id(): void
    {
        /* Arrange */
        $nonexistentId = 99999;

        /* Act */
        $response = $this->post(route('tax_rates.delete', ['tax_rate_id' => $nonexistentId]));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_references_removed(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');
        $product = $this->seedModel('Product', ['tax_rate_id' => $taxRate->tax_rate_id]);

        // Initially cannot delete
        $response1 = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));
        $response1->assertSessionHas('alert_error');

        // Remove reference
        $product->delete();

        /* Act */
        $response2 = $this->post(route('tax_rates.delete', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response2->assertRedirect(route('tax_rates.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }


    // Migrated from BckpTaxRateDeletionValidationTest.php
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_deletion_of_tax_rate_without_references(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate', ['tax_rate_name' => 'Unused Tax Rate']);

        /* Act */
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

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_deletion_with_invoice_tax_rates(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('InvoiceTaxRate', ['tax_rate_id' => $taxRate->tax_rate_id]);

        /* Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertFalse($canDelete);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_deletion_with_quote_tax_rates(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');
        $this->seedModel('QuoteTaxRate', ['tax_rate_id' => $taxRate->tax_rate_id]);

        /* Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertFalse($canDelete);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_correct_blocker_counts(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');

        $this->seedModelMany('Product', 2, ['tax_rate_id' => $taxRate->tax_rate_id]);
        $this->seedModelMany('InvoiceItem', 3, ['item_tax_rate_id' => $taxRate->tax_rate_id]);
        $this->seedModelMany('QuoteItem', 1, ['item_tax_rate_id' => $taxRate->tax_rate_id]);
        /* Act */
        $blockers = $this->service->getDeletionBlockers($taxRate->tax_rate_id);

        /* Assert */
        $this->assertEquals(2, $blockers['products']);
        $this->assertEquals(3, $blockers['invoice_items']);
        $this->assertEquals(1, $blockers['quote_items']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_deletion_after_all_references_removed(): void
    {
        /* Arrange */
        $taxRate = $this->seedModel('TaxRate');
        $product = $this->seedModel('Product', ['tax_rate_id' => $taxRate->tax_rate_id]);

        // Initially cannot delete
        $this->assertFalse($this->service->canDelete($taxRate->tax_rate_id));

        // Remove reference
        $product->delete();

        /* Act */
        $canDelete = $this->service->canDelete($taxRate->tax_rate_id);

        /* Assert */
        $this->assertTrue($canDelete);
    }

}
