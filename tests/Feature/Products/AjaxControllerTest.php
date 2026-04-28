<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(AjaxController::class)]
#[CoversClass(Tests\Feature\Products\AjaxController::class)]
class AjaxControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    #[Test]
    public function it_modal_product_lookups_returns_expected_results(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $family = $this->seedModel('Family', ['family_name' => 'Hardware']);
        $this->seedModel('Product', [
            'family_id'    => $family->family_id,
            'product_name' => 'Widget A',
        ]);

        /* Act */
        $response = $this->get('/products/ajax/modal_product_lookups');

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('products');
        $response->assertViewHas('families');
        $response->assertSee('Widget A');
    }

    #[Test]
    public function it_process_product_selections_handles_selection_logic(): void
    {
        /* Arrange */
        $this->actingAsAdmin();
        $product = $this->seedModel('Product', [
            'product_name'  => 'Selected Product',
            'product_price' => 25.5,
        ]);

        /* Act */
        $response = $this->post('/products/ajax/process_product_selections', [
            'product_ids' => [$product->product_id],
        ]);

        /* Assert */
        $response->assertOk();
        $response->assertJsonStructure([
            ['product_id', 'product_name', 'product_price'],
        ]);
        $response->assertJsonFragment([
            'product_id'   => $product->product_id,
            'product_name' => 'Selected Product',
        ]);
    }
}
