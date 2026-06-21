<?php

namespace Tests\Feature\Products;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * ProductsAjaxController Feature Tests.
 *
 * Tests product list route accessible to authenticated admins.
 */
class ProductsAjaxControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->databaseInsert('ip_products', [
            'product_name'        => 'Ajax Widget Beta',
            'family_id'           => 0,
            'product_sku'         => 'SKU-AJAX-001',
            'product_description' => 'An ajax test product',
            'product_price'       => '19.99',
            'purchase_price'      => '0.00',
            'tax_rate_id'         => 0,
        ]);

        /* Act */
        $response = $this->get('/products');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Ajax Widget Beta');
    }

    #[Test]
    public function it_redirects_a_guest_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/products');

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('Unauthenticated GET [/products] must redirect. Got [%d].', $response->statusCode())
        );
    }
}
