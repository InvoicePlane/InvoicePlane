<?php

namespace Tests\Feature\Invoices;

use Modules\Products\Controllers\FamiliesController;
use Modules\Products\Models\Family;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\FeatureTestCase;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(FamiliesController::class)]

class ProductDeletionValidationFeatureTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test that product without invoice items can be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_product_without_invoice_items(): void
    {
        /** Arrange */
        $product = $this->seedModel('Product', [
            'product_name'  => 'Deletable Product',
            'product_price' => 50.00,
        ]);

        /** Act */
        $response = $this->post(route('products.delete', ['product_id' => $product->product_id]));

        /* Assert */
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('alert_success');

        // Verify product was actually deleted
        $this->assertDatabaseMissing('ip_products', [
            'product_id' => $product->product_id,
        ]);
    }

    /**
     * Test that product with invoice items cannot be deleted via HTTP.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_of_product_with_invoice_items(): void
    {
        /** Arrange */
        $invoice = $this->seedModel('Invoice');

        $product = $this->seedModel('Product', [
            'product_name'  => 'Product In Use',
            'product_price' => 75.00,
        ]);

        $this->seedModel('InvoiceItem', [
            'invoice_id'      => $invoice->invoice_id,
            'item_product_id' => $product->product_id,
            'item_name'       => 'Invoice Item',
            'item_price'      => 75.00,
        ]);

        /** Act */
        $response = $this->post(route('products.delete', ['product_id' => $product->product_id]));

        /* Assert */
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('alert_error');

        // Verify product still exists in database
        $this->assertDatabaseHas('ip_products', [
            'product_id'   => $product->product_id,
            'product_name' => 'Product In Use',
        ]);
    }

    /**
     * Test error message includes invoice item count.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_returns_error_message_with_item_count(): void
    {
        /** Arrange */
        $invoice = $this->seedModel('Invoice');

        $product = $this->seedModel('Product');

        // Create 3 invoice items
        $this->seedModelMany('InvoiceItem', 3, [
            'invoice_id'      => $invoice->invoice_id,
            'item_product_id' => $product->product_id,
        ]);

        /** Act */
        $response = $this->post(route('products.delete', ['product_id' => $product->product_id]));

        /* Assert */
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('alert_error');

        // Optionally verify the count appears in the error message
        $errorMessage = session('alert_error');
        $this->assertStringContainsString('3', $errorMessage);

        // Product should still exist
        $this->assertDatabaseHas('ip_products', [
            'product_id' => $product->product_id,
        ]);
    }

    /**
     * Test that product with single invoice item cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_single_invoice_item(): void
    {
        /** Arrange */
        $invoice = $this->seedModel('Invoice');
        $product = $this->seedModel('Product');

        $this->seedModel('InvoiceItem', [
            'invoice_id'      => $invoice->invoice_id,
            'item_product_id' => $product->product_id,
        ]);

        /** Act */
        $response = $this->post(route('products.delete', ['product_id' => $product->product_id]));

        /* Assert */
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_products', ['product_id' => $product->product_id]);
    }

    /**
     * Test that product with multiple invoices cannot be deleted.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_multiple_invoice_references(): void
    {
        /** Arrange */
        $product = $this->seedModel('Product');

        // Create 2 different invoices, each with an item referencing the product
        for ($i = 0; $i < 2; $i++) {
            $invoice = $this->seedModel('Invoice');
            $this->seedModel('InvoiceItem', [
                'invoice_id'      => $invoice->invoice_id,
                'item_product_id' => $product->product_id,
            ]);
        }

        /** Act */
        $response = $this->post(route('products.delete', ['product_id' => $product->product_id]));

        /* Assert */
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_products', ['product_id' => $product->product_id]);
    }

    /**
     * Test deletion with invalid product ID.
     */
    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_product_id(): void
    {
        /** Arrange */
        $invalidId = -1;

        /** Act */
        $response = $this->post(route('products.delete', ['product_id' => $invalidId]));

        /* Assert */
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('alert_error');
    }

    /**
     * Test deletion with non-existent product ID.
     */
    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_nonexistent_product_id(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->post(route('products.delete', ['product_id' => $nonexistentId]));

        /* Assert */
        $response->assertRedirect(route('products.index'));
        $response->assertSessionHas('alert_error');
    }

    /**
     * Test that deletion works after invoice items are removed.
     */
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_invoice_items_removed(): void
    {
        /** Arrange */
        $invoice = $this->seedModel('Invoice');
        $product = $this->seedModel('Product');

        $item = $this->seedModel('InvoiceItem', [
            'invoice_id'      => $invoice->invoice_id,
            'item_product_id' => $product->product_id,
        ]);

        // Initially cannot delete
        $response1 = $this->post(route('products.delete', ['product_id' => $product->product_id]));
        $response1->assertSessionHas('alert_error');

        // Remove the invoice item
        $item->delete();

        /** Act */
        $response2 = $this->post(route('products.delete', ['product_id' => $product->product_id]));

        /* Assert */
        $response2->assertRedirect(route('products.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_products', ['product_id' => $product->product_id]);
    }
}
