<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tax_Rates;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Tax Rate Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for tax rate deletion with business rules:
 * - Tax rates referenced by products, invoice items, or quote items cannot be deleted.
 */
#[CoversClass(Tax_Rates::class)]

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
        $response = $this->post('/tax_rates/delete/' . ($taxRate->tax_rate_id));

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
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
        $response = $this->post('/tax_rates/delete/' . ($taxRate->tax_rate_id));

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
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
        $response = $this->post('/tax_rates/delete/' . ($taxRate->tax_rate_id));

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
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
        $response = $this->post('/tax_rates/delete/' . ($taxRate->tax_rate_id));

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
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
        $response = $this->post('/tax_rates/delete/' . ($invalidId));

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
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
        $response = $this->post('/tax_rates/delete/' . ($nonexistentId));

        /* Assert */
        $response->assertRedirect('/tax_rates/index');
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
        $response1 = $this->post('/tax_rates/delete/' . ($taxRate->tax_rate_id));
        $response1->assertSessionHas('alert_error');

        // Remove reference
        $product->delete();

        /* Act */
        $response2 = $this->post('/tax_rates/delete/' . ($taxRate->tax_rate_id));

        /* Assert */
        $response2->assertRedirect('/tax_rates/index');
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRate->tax_rate_id]);
    }

}
