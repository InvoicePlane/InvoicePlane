<?php

namespace Tests\Feature\Invoices;

use Modules\Products\Controllers\FamiliesController;
use Modules\Products\Models\Family;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\Core\FeatureTestCase;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(FamiliesController::class)]

class UnitDeletionValidationFeatureTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_unit_without_references(): void
    {
        /* Arrange */
        $unit = $this->seedModel('Unit', ['unit_name' => 'Deletable Unit']);

        /* Act */
        $response = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /* Arrange */
        $unit = $this->seedModel('Unit');
        $this->seedModel('Product', ['unit_id' => $unit->unit_id]);

        /* Act */
        $response = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $unit->unit_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_invoice_items(): void
    {
        /* Arrange */
        $unit = $this->seedModel('Unit');
        $this->seedModel('InvoiceItem', ['item_product_unit_id' => $unit->unit_id]);

        /* Act */
        $response = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $unit->unit_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_quote_items(): void
    {
        /* Arrange */
        $unit = $this->seedModel('Unit');
        $this->seedModel('QuoteItem', ['item_product_unit_id' => $unit->unit_id]);

        /* Act */
        $response = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $unit->unit_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_multiple_references(): void
    {
        /* Arrange */
        $unit = $this->seedModel('Unit');

        $this->seedModelMany('Product', 2, ['unit_id' => $unit->unit_id]);
        $this->seedModel('InvoiceItem', ['item_product_unit_id' => $unit->unit_id]);

        /* Act */
        $response = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_units', ['unit_id' => $unit->unit_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_references_removed(): void
    {
        /* Arrange */
        $unit    = $this->seedModel('Unit');
        $product = $this->seedModel('Product', ['unit_id' => $unit->unit_id]);

        // Initially cannot delete
        $response1 = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));
        $response1->assertSessionHas('alert_error');

        // Remove reference
        $product->delete();

        /* Act */
        $response2 = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));

        /* Assert */
        $response2->assertRedirect(route('units.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }
}
