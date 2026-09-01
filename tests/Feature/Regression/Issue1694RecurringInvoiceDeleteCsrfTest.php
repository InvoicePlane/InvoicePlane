<?php

namespace Tests\Feature\Regression;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

/**
 * #1694 regression — Controller: Recurring::delete() (application/modules/invoices,
 * route invoices/recurring/delete).
 */
#[Group('security')]
class Issue1694RecurringInvoiceDeleteCsrfTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
        $this->enableCsrfProtection();
    }

    private function seedRecurringInvoice(): int
    {
        $invoiceId = $this->seedInvoice($this->seedClient());

        return (int) $this->seedModel('RecurringInvoice', ['invoice_id' => $invoiceId])->invoice_recurring_id;
    }

    #[Test]
    public function it_deletes_a_recurring_invoice_with_a_valid_csrf_token(): void
    {
        /* Arrange */
        $recurringId = $this->seedRecurringInvoice();

        /* Act */
        $response = $this->postWithValidCsrfToken('/invoices/recurring/delete/' . $recurringId);

        /* Assert */
        self::assertTrue(
            $response->isRedirect(),
            sprintf('invoices/recurring/delete must redirect. Got [%d].', $response->statusCode())
        );
        $this->assertDatabaseMissing('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId]);
    }

    #[Test]
    public function it_rejects_the_delete_without_a_csrf_token(): void
    {
        /* Arrange */
        $recurringId = $this->seedRecurringInvoice();

        /* Act */
        $response = $this->postWithoutCsrfToken('/invoices/recurring/delete/' . $recurringId);

        /* Assert */
        self::assertGreaterThanOrEqual(400, $response->statusCode());
        $this->assertDatabaseHas('ip_invoices_recurring', ['invoice_recurring_id' => $recurringId]);
    }
}
