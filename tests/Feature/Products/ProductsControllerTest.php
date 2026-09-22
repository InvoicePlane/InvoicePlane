<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * Products controller — application/modules/products/controllers/Products.php.
 *
 * Required fields (Mdl_Products::validation_rules): product_name, product_price.
 * Absorbs Issue1694ProductsDeleteCsrfTest. Unit and family CRUD live in
 * UnitsControllerTest / FamiliesControllerTest.
 */
#[Group('products')]
#[CoversClass(\Products::class)]
class ProductsControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // List
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_every_product(): void
    {
        /* Arrange */
        $this->seedProduct(['product_name' => 'Widget Alpha']);
        $this->seedProduct(['product_name' => 'Widget Beta']);

        /* Act */
        $response = $this->get('/products');

        /* Assert */
        $this->assertResponseBodyContains($response, 'Widget Alpha');
        $this->assertResponseBodyContains($response, 'Widget Beta');
    }

    // -------------------------------------------------------------------------
    // Create — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_product(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/products/form', [
            'product_sku'   => 'SKU-CREATE',
            'product_name'  => 'Deluxe Widget',
            'product_price' => '19.99',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'products');
        $this->assertDatabaseHas('ip_products', ['product_name' => 'Deluxe Widget', 'product_price' => '19.99']);
        $this->assertDatabaseCount('ip_products', 1);
    }

    // -------------------------------------------------------------------------
    // Create — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_create_without_product_name(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/products/form', [
            'product_sku'   => 'SKU-NONAME',
            'product_name'  => '',
            'product_price' => '5.00',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseCount('ip_products', 0);
    }

    #[Test]
    public function it_fails_to_create_without_product_price(): void
    {
        /* Arrange */

        /* Act */
        $response = $this->post('/products/form', [
            'product_sku'   => 'SKU-NOPRICE',
            'product_name'  => 'Priceless Widget',
            'product_price' => '',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid create must re-render the form, not redirect.');
        $this->assertDatabaseMissing('ip_products', ['product_name' => 'Priceless Widget']);
    }

    // -------------------------------------------------------------------------
    // Update — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_renders_the_edit_form_for_the_requested_product_only(): void
    {
        /* Arrange */
        $target = $this->seedProduct(['product_name' => 'Editable Widget']);
        $this->seedProduct(['product_name' => 'Other Widget']);

        /* Act */
        $response = $this->get('/products/form/' . $target);

        /* Assert */
        $this->assertResponseBodyContains($response, 'Editable Widget');
        $this->assertResponseBodyNotContains($response, 'Other Widget');
    }

    #[Test]
    public function it_updates_a_product(): void
    {
        /* Arrange */
        $id = $this->seedProduct(['product_name' => 'Original Widget', 'product_price' => 10]);

        /* Act */
        $response = $this->post('/products/form/' . $id, [
            'product_sku'   => 'SKU-UPD',
            'product_name'  => 'Renamed Widget',
            'product_price' => '12.50',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'products');
        $this->assertDatabaseHas('ip_products', ['product_id' => $id, 'product_name' => 'Renamed Widget', 'product_price' => '12.50']);
        $this->assertDatabaseMissing('ip_products', ['product_name' => 'Original Widget']);
    }

    // -------------------------------------------------------------------------
    // Update — validation (one omitted required field per test)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_fails_to_update_without_product_name(): void
    {
        /* Arrange */
        $id = $this->seedProduct(['product_name' => 'Keep This Widget']);

        /* Act */
        $response = $this->post('/products/form/' . $id, [
            'product_sku'   => 'SKU-KEEP',
            'product_name'  => '',
            'product_price' => '9.99',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_products', ['product_id' => $id, 'product_name' => 'Keep This Widget']);
    }

    #[Test]
    public function it_fails_to_update_without_product_price(): void
    {
        /* Arrange */
        $id = $this->seedProduct(['product_name' => 'Price Kept Widget', 'product_price' => 7]);

        /* Act */
        $response = $this->post('/products/form/' . $id, [
            'product_sku'   => 'SKU-KEEP2',
            'product_name'  => 'Price Kept Widget',
            'product_price' => '',
            'btn_submit'    => '1',
        ]);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'Invalid update must re-render the form, not redirect.');
        $this->assertDatabaseHas('ip_products', ['product_id' => $id, 'product_price' => '7.00']);
    }

    // -------------------------------------------------------------------------
    // Delete — happy path
    // -------------------------------------------------------------------------

    #[Test]
    public function it_deletes_a_product(): void
    {
        /* Arrange */
        $id   = $this->seedProduct(['product_name' => 'Deletable Widget']);
        $keep = $this->seedProduct(['product_name' => 'Kept Widget']);

        /* Act */
        $response = $this->post('/products/delete/' . $id, []);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'products');
        $this->assertDatabaseMissing('ip_products', ['product_id' => $id]);
        $this->assertDatabaseHas('ip_products', ['product_id' => $keep]);
    }

    // -------------------------------------------------------------------------
    // Delete — CSRF regression (#1694)
    // -------------------------------------------------------------------------

    #[Test]
    public function it_still_deletes_a_product_when_csrf_protection_is_on_and_the_token_is_valid(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedProduct(['product_name' => 'CSRF Widget']);

        /* Act */
        $response = $this->postWithValidCsrfToken('/products/delete/' . $id);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'products');
        $this->assertDatabaseMissing('ip_products', ['product_id' => $id]);
    }

    #[Test]
    public function it_does_not_delete_a_product_when_the_csrf_token_is_missing(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();
        $id = $this->seedProduct(['product_name' => 'CSRF Widget Kept']);

        /* Act */
        $response = $this->postWithoutCsrfToken('/products/delete/' . $id);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less delete must not reach the controller.');
        $this->assertDatabaseHas('ip_products', ['product_id' => $id, 'product_name' => 'CSRF Widget Kept']);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_leaks_no_product(): void
    {
        /* Arrange */
        $this->seedProduct(['product_name' => 'Secret Widget']);
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/products');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'Unauthenticated request must redirect to login.');
        $this->assertResponseBodyNotContains($response, 'Secret Widget');
    }

    /** @param array<string,mixed> $overrides */
    private function seedProduct(array $overrides = []): int
    {
        return $this->databaseInsert('ip_products', array_merge([
            'family_id'           => 0,
            'product_sku'         => 'SKU-' . random_int(1000, 9999),
            'product_name'        => 'Seeded Product',
            'product_description' => '',
            'product_price'       => 10,
            'purchase_price'      => 0,
            'tax_rate_id'         => 0,
        ], $overrides));
    }
}
