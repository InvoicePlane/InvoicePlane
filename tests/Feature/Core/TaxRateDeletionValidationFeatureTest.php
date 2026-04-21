<?php

namespace Tests\Feature\Core;

use Modules\Core\Controllers\AjaxController as CoreAjaxController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;

/**
 * Core AjaxController Feature Tests.
 *
 * Tests AJAX requests for settings operations.
 */
#[CoversClass(CoreAjaxController::class)]

class TaxRateDeletionValidationFeatureTest extends FeatureTestCase
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
}
