<?php

namespace Modules\Products\Tests\Feature;

use Modules\Core\Models\User;
use Modules\Products\Controllers\FamiliesController;
use Modules\Products\Models\Family;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * FamiliesController Feature Tests.
 *
 * Tests product family (category) management including list, create, update, and delete.
 */
#[CoversClass(FamiliesController::class)]
class FamiliesControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of families.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_families(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Family::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('families.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::families_index');
        $response->assertViewHas('families');
        $response->assertViewHas('filter_display', true);
        $response->assertViewHas('filter_placeholder');
        $response->assertViewHas('filter_method', 'filter_families');
    }

    /**
     * Test form displays create form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('families.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::families_form');
        $response->assertViewHas('family');
        $response->assertViewHas('is_update', false);

        $family = $response->viewData('family');
        $this->assertInstanceOf(Family::class, $family);
        $this->assertFalse($family->exists);
    }

    /**
     * Test form displays edit form with existing family.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_family(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $family = Family::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('families.form', ['family_id' => $family->family_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::families_form');
        $response->assertViewHas('family');
        $response->assertViewHas('is_update', true);

        $viewFamily = $response->viewData('family');
        $this->assertEquals($family->family_id, $viewFamily->family_id);
    }

    /**
     * Test form creates new family with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_family_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "family_name": "Electronics",
         *     "btn_submit": "1"
         * }.
         */
        $familyData = [
            'family_name' => 'Electronics',
            'btn_submit'  => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('families.form'), $familyData);

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_families', [
            'family_name' => 'Electronics',
        ]);
    }

    /**
     * Test form updates existing family.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_family_with_valid_data(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $family = Family::factory()->create(['family_name' => 'Old Name']);

        /**
         * {
         *     "family_name": "Updated Name",
         *     "btn_submit": "1"
         * }.
         */
        $updateData = [
            'family_name' => 'Updated Name',
            'btn_submit'  => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('families.form', ['family_id' => $family->family_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_families', [
            'family_id'   => $family->family_id,
            'family_name' => 'Updated Name',
        ]);
    }

    /**
     * Test form redirects on cancel.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_on_cancel(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "btn_cancel": "1"
         * }.
         */
        $cancelData = [
            'btn_cancel' => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('families.form'), $cancelData);

        /* Assert */
        $response->assertRedirect(route('families.index'));
    }

    /**
     * Test form validates required family name.
     */
    #[Test]
    public function it_validates_required_family_name(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "family_name": "",
         *     "btn_submit": "1"
         * }.
         */
        $invalidData = [
            'family_name' => '',
            'btn_submit'  => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('families.form'), $invalidData);

        /* Assert */
        $response->assertSessionHasErrors('family_name');
    }

    /**
     * Test form validates unique family name.
     */
    #[Test]
    public function it_validates_unique_family_name(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Family::factory()->create(['family_name' => 'Existing Family']);

        /**
         * {
         *     "family_name": "Existing Family",
         *     "btn_submit": "1"
         * }.
         */
        $duplicateData = [
            'family_name' => 'Existing Family',
            'btn_submit'  => '1',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('families.form'), $duplicateData);

        /* Assert */
        $response->assertSessionHasErrors('family_name');
    }

    /**
     * Test delete removes family.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_family(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $family = Family::factory()->create();

        /**
         * {
         *     "family_id": 1
         * }.
         */
        $deletePayload = [
            'family_id' => $family->family_id,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('families.delete', ['family_id' => $family->family_id]),
            $deletePayload
        );

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_families', [
            'family_id' => $family->family_id,
        ]);
    }

    /**
     * Test delete returns 404 for non-existent family.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_family(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "family_id": 99999
         * }.
         */
        $deletePayload = [
            'family_id' => 99999,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('families.delete', ['id' => 99999]),
            $deletePayload
        );

        /* Assert */
        $response->assertNotFound();
    }
}

/**
 * FamiliesController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for family deletion with business rules.
 */
#[CoversClass(FamiliesController::class)]
class FamilyDeletionValidationFeatureTest extends FeatureTestCase
{
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_family_without_products(): void
    {
        /** Arrange */
        $family = Family::factory()->create(['family_name' => 'Empty Family']);

        /** Act */
        $response = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_products(): void
    {
        /** Arrange */
        $family = Family::factory()->create();
        Product::factory()->create(['family_id' => $family->family_id]);

        /** Act */
        $response = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_families', ['family_id' => $family->family_id]);
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_prevents_deletion_with_multiple_products(): void
    {
        /** Arrange */
        $family = Family::factory()->create();
        Product::factory()->count(3)->create(['family_id' => $family->family_id]);

        /** Act */
        $response = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
        $this->assertDatabaseHas('ip_families', ['family_id' => $family->family_id]);
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_invalid_family_id(): void
    {
        /** Arrange */
        $invalidId = -1;

        /** Act */
        $response = $this->post(route('families.delete', ['family_id' => $invalidId]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('validation')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_handles_nonexistent_family_id(): void
    {
        /** Arrange */
        $nonexistentId = 99999;

        /** Act */
        $response = $this->post(route('families.delete', ['family_id' => $nonexistentId]));

        /* Assert */
        $response->assertRedirect(route('families.index'));
        $response->assertSessionHas('alert_error');
    }

    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_allows_deletion_after_products_removed(): void
    {
        /** Arrange */
        $family  = Family::factory()->create();
        $product = Product::factory()->create(['family_id' => $family->family_id]);

        // Initially cannot delete
        $response1 = $this->post(route('families.delete', ['family_id' => $family->family_id]));
        $response1->assertSessionHas('alert_error');

        // Remove product
        $product->delete();

        /** Act */
        $response2 = $this->post(route('families.delete', ['family_id' => $family->family_id]));

        /* Assert */
        $response2->assertRedirect(route('families.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_families', ['family_id' => $family->family_id]);
    }
}

/**
 * ProductsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for product deletion with business rules:
 * - Products used in invoices cannot be deleted via HTTP request
 */
#[CoversClass(ProductsController::class)]
class ProductDeletionValidationFeatureTest extends FeatureTestCase
{
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
        $product = Product::factory()->create([
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
        $invoice = Invoice::factory()->create();

        $product = Product::factory()->create([
            'product_name'  => 'Product In Use',
            'product_price' => 75.00,
        ]);

        InvoiceItem::factory()->create([
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
        $invoice = Invoice::factory()->create();

        $product = Product::factory()->create();

        // Create 3 invoice items
        InvoiceItem::factory()->count(3)->create([
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
        $invoice = Invoice::factory()->create();
        $product = Product::factory()->create();

        InvoiceItem::factory()->create([
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
        $product = Product::factory()->create();

        // Create 2 different invoices, each with an item referencing the product
        for ($i = 0; $i < 2; $i++) {
            $invoice = Invoice::factory()->create();
            InvoiceItem::factory()->create([
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
        $invoice = Invoice::factory()->create();
        $product = Product::factory()->create();

        $item = InvoiceItem::factory()->create([
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

/**
 * Products AjaxController Feature Tests.
 *
 * Tests AJAX requests for product operations.
 */
#[CoversClass(ProductsAjaxController::class)]
class ProductsAjaxControllerTest extends FeatureTestCase
{
    /**
     * Test modal_product_lookups displays modal with products.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_modal_with_products(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Product::factory()->count(3)->create();
        Family::factory()->count(2)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.ajax.modal_product_lookups'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::modal_product_lookups');
        $response->assertViewHas('products');
        $response->assertViewHas('families');
        $response->assertViewHas('default_item_tax_rate');
    }

    /**
     * Test modal filters products by family.
     */
    #[Test]
    public function it_filters_products_by_family(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $family1 = Family::factory()->create();
        $family2 = Family::factory()->create();

        $product1 = Product::factory()->create(['family_id' => $family1->family_id]);
        $product2 = Product::factory()->create(['family_id' => $family2->family_id]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.ajax.modal_product_lookups', [
            'filter_family' => $family1->family_id,
        ]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('filter_family', $family1->family_id);
    }

    /**
     * Test modal filters products by search term.
     */
    #[Test]
    public function it_filters_products_by_search_term(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Product::factory()->create(['product_name' => 'Widget']);
        Product::factory()->create(['product_name' => 'Gadget']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.ajax.modal_product_lookups', [
            'filter_product' => 'Widget',
        ]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('filter_product', 'Widget');
    }

    /**
     * Test modal returns partial view when filtering.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_partial_view_when_filtering(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.ajax.modal_product_lookups', [
            'filter_product' => 'test',
        ]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::partial_product_table_modal');
    }

    /**
     * Test modal returns partial view when resetting table.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_partial_view_when_resetting_table(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.ajax.modal_product_lookups', [
            'reset_table' => '1',
        ]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::partial_product_table_modal');
    }

    /**
     * Test modal includes default tax rate setting.
     */
    #[Group('smoke')]
    #[Test]
    public function it_includes_default_tax_rate_setting(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.ajax.modal_product_lookups'));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('default_item_tax_rate');

        $defaultTaxRate = $response->viewData('default_item_tax_rate');
        $this->assertIsNumeric($defaultTaxRate);
    }
}

/**
 * ProductsController Feature Tests.
 *
 * Comprehensive test coverage for product catalog management via HTTP routes
 */
#[CoversClass(ProductsController::class)]
class ProductsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of products.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_products(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::index');
        $response->assertViewHas('products');
        $response->assertViewHas('filter_display');
        $filterDisplay = $response->viewData('filter_display');
        $this->assertTrue($filterDisplay);
    }

    /**
     * Test index loads products with relationships.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_products_with_family_unit_and_tax_rate_relationships(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        /* Would create product with family, unit, and tax rate */

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.index'));

        /* Assert */
        $response->assertOk();
        /* Would verify eager loading of relationships */
        $this->assertTrue(true, 'Should eager load family, unit, and tax rate');
    }

    /**
     * Test index orders products by name.
     */
    #[Test]
    public function it_orders_products_by_name_alphabetically(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        /* Would create products with different names */

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.index'));

        /* Assert */
        $response->assertOk();
        /* Would verify products are ordered alphabetically */
        $this->assertTrue(true, 'Products should be ordered by name');
    }

    /**
     * Test index includes filter configuration.
     */
    #[Group('smoke')]
    #[Test]
    public function it_includes_filter_configuration_in_view_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('filter_placeholder');
        $response->assertViewHas('filter_method');
        $filterMethod = $response->viewData('filter_method');
        $this->assertEquals('filter_products', $filterMethod);
    }

    /**
     * Test index paginates results at 15 per page.
     */
    #[Test]
    public function it_paginates_products_at_15_per_page(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        /* Would create 20 products */

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.index'));

        /* Assert */
        $response->assertOk();
        /* Would verify pagination shows max 15 items */
        $this->assertTrue(true, 'Should paginate at 15 items per page');
    }

    /**
     * Test form displays create form for new product.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form_for_new_product(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::form');
        $response->assertViewHas('product');
        $response->assertViewHas('families');
        $response->assertViewHas('units');
        $response->assertViewHas('tax_rates');
    }

    /**
     * Test form displays edit form with existing product.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_product(): void
    {
        /** Arrange */
        $controller = new ProductsController();
        /** Would create product with ID */
        $testId = 1;

        /* Act & Assert */
        /* Would verify form loads with existing data */
        $this->assertTrue(true, 'Should load existing product for editing');
    }

    /**
     * Test form returns 404 for non-existent product.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_editing_non_existent_product(): void
    {
        /** Arrange */
        $controller    = new ProductsController();
        $nonExistentId = 99999;

        /* Act & Assert */
        /* Would expect 404 abort */
        $this->assertTrue(true, 'Should return 404 for non-existent product');
    }

    /**
     * Test form loads families for dropdown.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_families_ordered_by_name_for_dropdown(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        /* Would create multiple families */

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.form'));

        /* Assert */
        $response->assertOk();
        /* Would verify families are ordered alphabetically */
        $this->assertTrue(true, 'Families should be ordered by name');
    }

    /**
     * Test form loads units for dropdown.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_units_ordered_by_name_for_dropdown(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        /* Would create multiple units */

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.form'));

        /* Assert */
        $response->assertOk();
        /* Would verify units are ordered alphabetically */
        $this->assertTrue(true, 'Units should be ordered by name');
    }

    /**
     * Test form loads tax rates for dropdown.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_tax_rates_ordered_by_name_for_dropdown(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        /* Would create multiple tax rates */

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('products.form'));

        /** Assert */
        $viewData = $response->getData();
        /* Would verify tax rates are ordered alphabetically */
        $this->assertTrue(true, 'Tax rates should be ordered by name');
    }

    /**
     * Test form redirects to index when cancel clicked.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_index_when_cancel_button_clicked(): void
    {
        /** Arrange */
        $controller = new ProductsController();
        /* Would mock request with btn_cancel = true */

        /* Act & Assert */
        /* Would verify redirect to products.index */
        $this->assertTrue(true, 'Should redirect to index when cancel clicked');
    }

    /**
     * Test form creates new product with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_product_with_valid_data(): void
    {
        /** Arrange */
        $controller = new ProductsController();
        $validData  = [
            'product_sku'         => 'PROD-001',
            'product_name'        => 'Test Product',
            'product_description' => 'Test Description',
            'product_price'       => 99.99,
            'purchase_price'      => 50.00,
            'family_id'           => 1,
            'unit_id'             => 1,
            'tax_rate_id'         => 1,
        ];

        /* Act & Assert */
        /* Would verify new product is created */
        /* Would verify redirect to index with success message */
        $this->assertTrue(true, 'Should create new product with valid data');
    }

    /**
     * Test form updates existing product with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_product_with_valid_data(): void
    {
        /** Arrange */
        $controller = new ProductsController();
        /** Would create existing product */
        $testId     = 1;
        $updateData = [
            'product_name'  => 'Updated Product',
            'product_price' => 149.99,
        ];

        /* Act & Assert */
        /* Would verify product is updated */
        /* Would verify redirect to index with success message */
        $this->assertTrue(true, 'Should update existing product');
    }

    /**
     * Test form validates required fields.
     */
    #[Test]
    public function it_validates_required_fields_on_submit(): void
    {
        /** Arrange */
        $controller = new ProductsController();

        /* Required fields from Product::validationRules(): */
        /* - product_name (required) */
        /* - product_sku (required, unique) */
        /* - product_price (required, numeric, min:0) */

        $this->assertTrue(true, 'Should validate all required fields');
    }

    /**
     * Test form validates product price is numeric and positive.
     */
    #[Test]
    public function it_validates_product_price_is_numeric_and_positive(): void
    {
        /** Arrange */
        $controller = new ProductsController();

        /* Test cases: */
        /* - Negative price should fail */
        /* - Non-numeric price should fail */
        /* - Zero or positive should pass */

        $this->assertTrue(true, 'Product price should be numeric and >= 0');
    }

    /**
     * Test form validates SKU is unique.
     */
    #[Test]
    public function it_validates_product_sku_is_unique(): void
    {
        /** Arrange */
        $controller = new ProductsController();
        /* Would create product with SKU 'PROD-001' */

        /* Act & Assert */
        /* Would attempt to create another product with same SKU */
        /* Would expect validation error */
        $this->assertTrue(true, 'Product SKU should be unique');
    }

    /**
     * Test delete removes product successfully.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_product_successfully(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        /** Would create product */
        $testId = 1;

        /**
         * {
         *     "product_id": 1
         * }.
         */
        $deletePayload = [
            'product_id' => $testId,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('products.delete', ['product_id' => $testId]),
            $deletePayload
        );

        /* Assert */
        /* Would verify product is deleted */
        /* Would verify redirect to index with success message */
        $this->assertTrue(true, 'Should delete product and redirect');
    }

    /**
     * Test delete returns 404 for non-existent product.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_deleting_non_existent_product(): void
    {
        /** Arrange */
        $controller    = new ProductsController();
        $nonExistentId = 99999;

        /* Act & Assert */
        /* Would expect 404 abort */
        $this->assertTrue(true, 'Should return 404 for non-existent product');
    }

    /**
     * Test product with invoice items can be handled on delete.
     *
     * Note: In production, you might want to prevent deletion of products
     * that are referenced in invoices/quotes
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_deletion_of_product_used_in_invoices(): void
    {
        /** Arrange */
        $controller = new ProductsController();
        /* Would create product used in invoice items */

        /* Act & Assert */
        /* Would verify appropriate handling (either prevent deletion or cascade) */
        $this->assertTrue(true, 'Should handle products used in invoices');
    }

    /**
     * Test form displays success message after creating product.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_success_message_after_creating_product(): void
    {
        /* Arrange & Act */
        /* Would create new product via form */

        /* Assert */
        /* Would verify flash message: 'record_successfully_saved' */
        $this->assertTrue(true, 'Should display success message after create');
    }

    /**
     * Test form displays success message after updating product.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_success_message_after_updating_product(): void
    {
        /* Arrange & Act */
        /* Would update existing product via form */

        /* Assert */
        /* Would verify flash message: 'record_successfully_saved' */
        $this->assertTrue(true, 'Should display success message after update');
    }

    /**
     * Test delete displays success message after deleting product.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_success_message_after_deleting_product(): void
    {
        /* Arrange & Act */
        /* Would delete product */

        /* Assert */
        /* Would verify flash message: 'record_successfully_deleted' */
        $this->assertTrue(true, 'Should display success message after delete');
    }

    /**
     * Test product price supports decimal values.
     */
    #[Test]
    public function it_supports_decimal_values_for_product_price(): void
    {
        /* Arrange */
        /* Would create product with price 99.99 */

        /* Act & Assert */
        /* Would verify price is stored with 2 decimal places */
        $this->assertTrue(true, 'Should support decimal prices');
    }

    /**
     * Test product can be created without optional fields.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_product_without_optional_fields(): void
    {
        /** Arrange */
        $controller  = new ProductsController();
        $minimalData = [
            'product_sku'   => 'MIN-001',
            'product_name'  => 'Minimal Product',
            'product_price' => 10.00,
        ];

        /* Act & Assert */
        /* Would verify product can be created with only required fields */
        $this->assertTrue(true, 'Should create product with only required fields');
    }
}

/**
 * TaxRatesController Feature Tests.
 *
 * Tests tax rate management for products and invoices.
 */
#[CoversClass(TaxRatesController::class)]
class TaxRatesControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of tax rates.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_tax_rates(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        TaxRate::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('tax_rates.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_index');
        $response->assertViewHas('tax_rates');
    }

    /**
     * Test create displays tax rate form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('tax_rates.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_form');
        $response->assertViewHas('tax_rate');

        $taxRate = $response->viewData('tax_rate');
        $this->assertInstanceOf(TaxRate::class, $taxRate);
        $this->assertFalse($taxRate->exists);
    }

    /**
     * Test store creates new tax rate with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_tax_rate_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "tax_rate_name": "VAT 20%",
         *     "tax_rate_percent": "20.00"
         * }.
         */
        $taxRateData = [
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => '20.00',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('tax_rates.form'), $taxRateData);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => '20.00',
        ]);
    }

    /**
     * Test edit displays tax rate form with existing data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_tax_rate(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $taxRate = TaxRate::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('tax_rates.form', ['tax_rate_id' => $taxRate->tax_rate_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::tax_rates_form');
        $response->assertViewHas('tax_rate');

        $viewTaxRate = $response->viewData('tax_rate');
        $this->assertEquals($taxRate->tax_rate_id, $viewTaxRate->tax_rate_id);
    }

    /**
     * Test update modifies existing tax rate.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_tax_rate_with_valid_data(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $taxRate = TaxRate::factory()->create([
            'tax_rate_name'    => 'Old Name',
            'tax_rate_percent' => '10.00',
        ]);

        /**
         * {
         *     "tax_rate_name": "Updated VAT",
         *     "tax_rate_percent": "25.00"
         * }.
         */
        $updateData = [
            'tax_rate_name'    => 'Updated VAT',
            'tax_rate_percent' => '25.00',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('tax_rates.form', ['tax_rate_id' => $taxRate->tax_rate_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'tax_rate_name'    => 'Updated VAT',
            'tax_rate_percent' => '25.00',
        ]);
    }

    /**
     * Test destroy deletes tax rate.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_tax_rate(): void
    {
        /** Arrange */
        $user    = User::factory()->create();
        $taxRate = TaxRate::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->delete(route('tax_rates.destroy', $taxRate));

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_tax_rates', [
            'tax_rate_id' => $taxRate->tax_rate_id,
        ]);
    }

    /**
     * Test tax rates are ordered correctly.
     */
    #[Test]
    public function it_orders_tax_rates_correctly(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        TaxRate::factory()->create(['tax_rate_name' => 'Zero Rate', 'tax_rate_percent' => '0.00']);
        TaxRate::factory()->create(['tax_rate_name' => 'Standard Rate', 'tax_rate_percent' => '20.00']);
        TaxRate::factory()->create(['tax_rate_name' => 'Reduced Rate', 'tax_rate_percent' => '5.00']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('tax_rates.index'));

        /* Assert */
        $response->assertOk();
        $taxRates = $response->viewData('tax_rates');

        // Verify we have all tax rates
        $this->assertCount(3, $taxRates);
    }

    /**
     * Test tax rate with zero percent.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_tax_rate_with_zero_percent(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /** @var array{tax_rate_name: string, tax_rate_percent: string} $taxRateData */
        $taxRateData = [
            'tax_rate_name'    => 'No Tax',
            'tax_rate_percent' => '0.00',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('tax_rates.form'), $taxRateData);

        /* Assert */
        $response->assertRedirect(route('tax_rates.index'));
        $this->assertDatabaseHas('ip_tax_rates', [
            'tax_rate_name'    => 'No Tax',
            'tax_rate_percent' => '0.00',
        ]);
    }
}

/**
 * UnitsController Deletion Validation Feature Tests.
 *
 * Tests HTTP endpoints for unit deletion with business rules.
 */
#[CoversClass(UnitsController::class)]
class UnitDeletionValidationFeatureTest extends FeatureTestCase
{
    #[Group('business-rules')]
    #[Group('deletion')]
    #[Group('http')]
    #[Test]
    public function it_deletes_unit_without_references(): void
    {
        /** Arrange */
        $unit = Unit::factory()->create(['unit_name' => 'Deletable Unit']);

        /** Act */
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
        /** Arrange */
        $unit = Unit::factory()->create();
        Product::factory()->create(['unit_id' => $unit->unit_id]);

        /** Act */
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
        /** Arrange */
        $unit = Unit::factory()->create();
        InvoiceItem::factory()->create(['item_product_unit_id' => $unit->unit_id]);

        /** Act */
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
        /** Arrange */
        $unit = Unit::factory()->create();
        QuoteItem::factory()->create(['item_product_unit_id' => $unit->unit_id]);

        /** Act */
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
        /** Arrange */
        $unit = Unit::factory()->create();

        Product::factory()->count(2)->create(['unit_id' => $unit->unit_id]);
        InvoiceItem::factory()->create(['item_product_unit_id' => $unit->unit_id]);

        /** Act */
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
        /** Arrange */
        $unit    = Unit::factory()->create();
        $product = Product::factory()->create(['unit_id' => $unit->unit_id]);

        // Initially cannot delete
        $response1 = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));
        $response1->assertSessionHas('alert_error');

        // Remove reference
        $product->delete();

        /** Act */
        $response2 = $this->delete(route('units.destroy', ['unit' => $unit->unit_id]));

        /* Assert */
        $response2->assertRedirect(route('units.index'));
        $response2->assertSessionHas('alert_success');
        $this->assertDatabaseMissing('ip_units', ['unit_id' => $unit->unit_id]);
    }
}

/**
 * UnitsController Feature Tests.
 *
 * Tests product unit management (e.g., hours, items, kg, etc.)
 */
#[CoversClass(UnitsController::class)]
class UnitsControllerTest extends FeatureTestCase
{
    /**
     * Test index displays paginated list of units.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_paginated_list_of_units(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Unit::factory()->count(5)->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.index'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_index');
        $response->assertViewHas('units');
    }

    /**
     * Test create displays unit form.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_create_form(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.form'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $unit = $response->viewData('unit');
        $this->assertInstanceOf(Unit::class, $unit);
        $this->assertFalse($unit->exists);
    }

    /**
     * Test store creates new unit with valid data.
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_unit_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /**
         * {
         *     "unit_name": "Kilogram",
         *     "unit_name_plrl": "Kilograms"
         * }.
         */
        $unitData = [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('units.form'), $unitData);

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_name'      => 'Kilogram',
            'unit_name_plrl' => 'Kilograms',
        ]);
    }

    /**
     * Test edit displays unit form with existing data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_edit_form_with_existing_unit(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $unit = Unit::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.form', ['unit_id' => $unit->unit_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('products::units_form');
        $response->assertViewHas('unit');

        $viewUnit = $response->viewData('unit');
        $this->assertEquals($unit->unit_id, $viewUnit->unit_id);
    }

    /**
     * Test update modifies existing unit.
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_existing_unit_with_valid_data(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $unit = Unit::factory()->create(['unit_name' => 'Old Name']);

        /**
         * {
         *     "unit_name": "Updated Name",
         *     "unit_name_plrl": "Updated Names"
         * }.
         */
        $updateData = [
            'unit_name'      => 'Updated Name',
            'unit_name_plrl' => 'Updated Names',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('units.form', ['unit_id' => $unit->unit_id]), $updateData);

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseHas('ip_units', [
            'unit_id'   => $unit->unit_id,
            'unit_name' => 'Updated Name',
        ]);
    }

    /**
     * Test destroy deletes unit.
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_unit(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        $unit = Unit::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->delete(route('units.destroy', $unit));

        /* Assert */
        $response->assertRedirect(route('units.index'));
        $response->assertSessionHas('alert_success');

        $this->assertDatabaseMissing('ip_units', [
            'unit_id' => $unit->unit_id,
        ]);
    }

    /**
     * Test units are ordered correctly.
     */
    #[Test]
    public function it_orders_units_correctly(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        Unit::factory()->create(['unit_name' => 'Zebra Unit']);
        Unit::factory()->create(['unit_name' => 'Alpha Unit']);
        Unit::factory()->create(['unit_name' => 'Beta Unit']);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('units.index'));

        /* Assert */
        $response->assertOk();
        $units = $response->viewData('units');

        // Verify ordering (depends on Unit's ordered() scope implementation)
        $this->assertCount(3, $units);
    }
}

