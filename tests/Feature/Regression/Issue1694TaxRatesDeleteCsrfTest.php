<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Tax_rates::delete() (application/modules/tax_rates).
 */
#[Group('security')]
class Issue1694TaxRatesDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_a_tax_rate_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $taxRateId = (int) $this->seedModel('TaxRate')->tax_rate_id;

        /* Act */
        $response = $this->postWithValidCsrfToken('/tax_rates/delete/' . $taxRateId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('tax_rates/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_tax_rates', ['tax_rate_id' => $taxRateId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $taxRateId = (int) $this->seedModel('TaxRate')->tax_rate_id;

        /* Act */
        $response = $this->postWithoutCsrfToken('/tax_rates/delete/' . $taxRateId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_tax_rates', ['tax_rate_id' => $taxRateId]);
    }
}
