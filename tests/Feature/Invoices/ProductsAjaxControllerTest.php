<?php

namespace Tests\Feature\Invoices;

use Tests\Concerns\InteractsWithDatabase;

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

class ProductsAjaxControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test modal_product_lookups displays modal with products.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_modal_with_products(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Product', 3);
        $this->seedModelMany('Family', 2);

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
        $user    = $this->seedModel('User');
        $family1 = $this->seedModel('Family');
        $family2 = $this->seedModel('Family');

        $product1 = $this->seedModel('Product', ['family_id' => $family1->family_id]);
        $product2 = $this->seedModel('Product', ['family_id' => $family2->family_id]);

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
        $user = $this->seedModel('User');
        $this->seedModel('Product', ['product_name' => 'Widget']);
        $this->seedModel('Product', ['product_name' => 'Gadget']);

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
        $user = $this->seedModel('User');

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
        $user = $this->seedModel('User');

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
        $user = $this->seedModel('User');

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
