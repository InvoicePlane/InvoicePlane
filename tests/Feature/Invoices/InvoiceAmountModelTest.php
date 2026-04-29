<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class InvoiceAmountModelTest extends AbstractTestCase
{
    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_totals_with_payments(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_totals_without_payments(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_with_global_discount(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_discount_with_amount_and_percent(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_for_global_discount_when_no_items(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Test]
    public function it_gets_total_invoiced_for_month(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Test]
    public function it_gets_total_paid_for_year(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Test]
    public function it_gets_total_balance_for_last_month(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Test]
    public function it_gets_status_totals_for_this_month(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }

    #[Test]
    public function it_gets_status_totals_for_different_periods(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceAmountService');
    }
}
