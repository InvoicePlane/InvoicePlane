<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Products::delete() (application/modules/products).
 *
 * ip_products has no NULLable columns, so the row is inserted explicitly.
 */
#[Group('security')]
class Issue1694ProductsDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_product_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $productId = $this->seedProduct();

        /* Act */
        $response = $this->postWithValidCsrfToken('/products/delete/' . $productId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('products/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_products', ['product_id' => $productId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $productId = $this->seedProduct();

        /* Act */
        $response = $this->postWithoutCsrfToken('/products/delete/' . $productId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_products', ['product_id' => $productId]);
    }

    private function seedProduct(): int
    {
        return $this->databaseInsert('ip_products', [
            'family_id'           => 0,
            'product_sku'         => 'sku-' . random_int(1000, 9999),
            'product_name'        => 'Issue 1694 Product',
            'product_description' => '',
            'product_price'       => 0,
            'purchase_price'      => 0,
            'tax_rate_id'         => 0,
        ]);
    }
}
