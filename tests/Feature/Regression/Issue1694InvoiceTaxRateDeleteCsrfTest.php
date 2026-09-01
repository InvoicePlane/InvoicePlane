<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Invoices::delete_invoice_tax()
 * (application/modules/invoices, route invoices/delete_invoice_tax).
 *
 * After removing the rate the controller recalculates invoice amounts, so a
 * full invoice (with its ip_invoice_amounts row) is seeded.
 */
#[Group('security')]
class Issue1694InvoiceTaxRateDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    #[Test]
    public function it_deletes_an_invoice_tax_rate_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        [$invoiceId, $invoiceTaxRateId] = $this->seedInvoiceTaxRate();

        /* Act */
        $response = $this->postWithValidCsrfToken(
            '/invoices/delete_invoice_tax/' . $invoiceId . '/' . $invoiceTaxRateId
        );

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('invoices/delete_invoice_tax must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_invoice_tax_rates', ['invoice_tax_rate_id' => $invoiceTaxRateId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        [$invoiceId, $invoiceTaxRateId] = $this->seedInvoiceTaxRate();

        /* Act */
        $response = $this->postWithoutCsrfToken(
            '/invoices/delete_invoice_tax/' . $invoiceId . '/' . $invoiceTaxRateId
        );

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_invoice_tax_rates', ['invoice_tax_rate_id' => $invoiceTaxRateId]);
    }

    /**
     * @return array{0: int, 1: int} [invoiceId, invoiceTaxRateId]
     */
    private function seedInvoiceTaxRate(): array
    {
        $invoiceId        = $this->seedInvoice($this->seedClient());
        $invoiceTaxRateId = $this->databaseInsert('ip_invoice_tax_rates', [
            'invoice_id'              => $invoiceId,
            'tax_rate_id'             => 1,
            'include_item_tax'        => 0,
            'invoice_tax_rate_amount' => '0.00',
        ]);

        return [$invoiceId, $invoiceTaxRateId];
    }
}
