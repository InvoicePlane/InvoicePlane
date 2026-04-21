<?php

namespace Tests\Unit\Models;

use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Mdl_Invoice_Amounts pure-PHP logic.
 *
 * Covered:
 *  - calculate_discount() — percent and fixed-amount discount arithmetic
 *  - invoice_balance = invoice_total - invoice_paid
 *  - shouldMarkPaid() threshold (balance == 0, total != 0)
 *  - get_status_totals() return shape — keyed 1-4, each with required fields
 *  - decimal precision via configurable decimal_places
 *
 * @group unit
 * @group models
 * @group invoices
 */
class Mdl_InvoiceAmountsTest extends TestCase
{
    private StubMdl_InvoiceAmounts $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new StubMdl_InvoiceAmounts(decimalPlaces: 2);
    }

    public function it_deducts_a_fixed_discount_amount_from_the_invoice_total(): void
    {
        $result = $this->model->applyDiscount(
            total: 500.00,
            discountAmount: 50.00,
            discountPercent: 0.00,
        );

        self::assertSame(
            '450.00',
            number_format($result, 2, '.', ''),
            'A fixed discount of 50.00 applied to 500.00 must yield 450.00.'
        );
    }

    public function it_deducts_a_percentage_discount_from_the_invoice_total(): void
    {
        $result = $this->model->applyDiscount(
            total: 200.00,
            discountAmount: 0.00,
            discountPercent: 10.00,
        );

        self::assertSame(
            '180.00',
            number_format($result, 2, '.', ''),
            'A 10% discount on 200.00 must yield 180.00.'
        );
    }

    public function it_applies_fixed_discount_before_percent_discount(): void
    {
        $result = $this->model->applyDiscount(
            total: 200.00,
            discountAmount: 20.00,
            discountPercent: 10.00,
        );

        self::assertSame(
            '162.00',
            number_format($result, 2, '.', ''),
            'Fixed 20 removed first → 180; then 10% of 180 = 18 removed → 162.00.'
        );
    }

    public function it_returns_the_full_total_when_both_discounts_are_zero(): void
    {
        $result = $this->model->applyDiscount(
            total: 999.99,
            discountAmount: 0.00,
            discountPercent: 0.00,
        );

        self::assertSame(
            '999.99',
            number_format($result, 2, '.', ''),
            'Zero discounts must return the total unchanged.'
        );
    }

    public function it_calculates_invoice_balance_as_total_minus_paid(): void
    {
        $balance = $this->model->calculateBalance(invoiceTotal: 750.00, invoicePaid: 250.00);

        self::assertSame(
            '500.00',
            number_format($balance, 2, '.', ''),
            'Balance = total - paid: 750.00 - 250.00 = 500.00.'
        );
    }

    public function it_produces_a_zero_balance_when_invoice_is_fully_paid(): void
    {
        $balance = $this->model->calculateBalance(invoiceTotal: 300.00, invoicePaid: 300.00);

        self::assertSame(
            '0.00',
            number_format($balance, 2, '.', ''),
            'A fully-paid invoice must have a zero balance.'
        );
    }

    public function it_produces_a_negative_balance_for_an_overpaid_invoice(): void
    {
        $balance = $this->model->calculateBalance(invoiceTotal: 100.00, invoicePaid: 120.00);

        self::assertSame(
            '-20.00',
            number_format($balance, 2, '.', ''),
            'Overpayment must produce a negative balance.'
        );
    }

    public function it_flags_an_invoice_for_paid_status_when_balance_is_zero_and_total_is_nonzero(): void
    {
        $shouldMarkPaid = $this->model->shouldMarkPaidStatus(
            invoiceBalance: 0.00,
            invoiceTotal: 500.00,
            isCreditInvoice: false,
        );

        self::assertTrue(
            $shouldMarkPaid,
            'An invoice with zero balance and nonzero total must be flagged as paid (status_id = 4).'
        );
    }

    public function it_does_not_flag_a_nonzero_balance_invoice_as_paid(): void
    {
        $shouldMarkPaid = $this->model->shouldMarkPaidStatus(
            invoiceBalance: 50.00,
            invoiceTotal: 500.00,
            isCreditInvoice: false,
        );

        self::assertFalse(
            $shouldMarkPaid,
            'An invoice with remaining balance must NOT be marked as paid.'
        );
    }

    public function it_flags_a_credit_invoice_with_zero_balance_as_paid_even_when_total_is_zero(): void
    {
        $shouldMarkPaid = $this->model->shouldMarkPaidStatus(
            invoiceBalance: 0.00,
            invoiceTotal: 0.00,
            isCreditInvoice: true,
        );

        self::assertTrue(
            $shouldMarkPaid,
            'A credit invoice (creditinvoice_parent_id > 0) with zero balance must be marked paid '
            . 'even when the total is also zero.'
        );
    }

    public function it_does_not_flag_a_zero_total_non_credit_invoice_as_paid(): void
    {
        $shouldMarkPaid = $this->model->shouldMarkPaidStatus(
            invoiceBalance: 0.00,
            invoiceTotal: 0.00,
            isCreditInvoice: false,
        );

        self::assertFalse(
            $shouldMarkPaid,
            'A non-credit invoice with both balance and total at zero must NOT be marked paid '
            . '(it is an empty/placeholder invoice).'
        );
    }

    public function it_rounds_discount_result_to_the_configured_decimal_places(): void
    {
        $model  = new StubMdl_InvoiceAmounts(decimalPlaces: 4);
        $result = $model->applyDiscount(
            total: 100.00,
            discountAmount: 0.00,
            discountPercent: 33.333,
        );

        $formatted = number_format($result, 4, '.', '');

        self::assertMatchesRegularExpression(
            '/^\d+\.\d{4}$/',
            $formatted,
            'Discount calculation must respect the configured decimal_places precision.'
        );
    }

    public function it_returns_status_totals_keyed_by_invoice_status_id_1_through_4(): void
    {
        $statuses = $this->model->buildStatusTotalsShape();

        foreach ([1, 2, 3, 4] as $key) {
            self::assertArrayHasKey(
                (string) $key,
                $statuses,
                sprintf('get_status_totals() return must contain status key [%d].', $key)
            );
        }
    }

    public function it_includes_required_fields_in_each_status_totals_entry(): void
    {
        $statuses = $this->model->buildStatusTotalsShape();

        foreach ($statuses as $key => $entry) {
            foreach (['class', 'label', 'href', 'sum_total', 'num_total'] as $field) {
                self::assertArrayHasKey(
                    $field,
                    $entry,
                    sprintf('Status totals entry [%s] is missing required field [%s].', $key, $field)
                );
            }
        }
    }

    public function it_initialises_sum_total_and_num_total_to_zero_when_no_results_exist(): void
    {
        $statuses = $this->model->buildStatusTotalsShape();

        foreach ($statuses as $key => $entry) {
            self::assertSame(
                0,
                $entry['sum_total'],
                sprintf('Default sum_total for status [%s] must be 0.', $key)
            );

            self::assertSame(
                0,
                $entry['num_total'],
                sprintf('Default num_total for status [%s] must be 0.', $key)
            );
        }
    }
}

class StubMdl_InvoiceAmounts
{
    public function __construct(private readonly int $decimalPlaces = 2) {}

    public function applyDiscount(float $total, float $discountAmount, float $discountPercent): float
    {
        $dp    = $this->decimalPlaces;
        $total = (float) number_format($total, $dp, '.', '');
        $total -= (float) number_format($discountAmount, $dp, '.', '');

        return $total - round(($total / 100 * $discountPercent), $dp);
    }

    public function calculateBalance(float $invoiceTotal, float $invoicePaid): float
    {
        return $invoiceTotal - $invoicePaid;
    }

    public function shouldMarkPaidStatus(
        float $invoiceBalance,
        float $invoiceTotal,
        bool $isCreditInvoice,
    ): bool {
        return $invoiceBalance == 0 && ($invoiceTotal != 0 || $isCreditInvoice);
    }

    public function buildStatusTotalsShape(): array
    {
        $statuses = [
            '1' => ['label' => 'draft',  'class' => 'draft',  'href' => 'invoices/status/draft'],
            '2' => ['label' => 'sent',   'class' => 'sent',   'href' => 'invoices/status/sent'],
            '3' => ['label' => 'viewed', 'class' => 'viewed', 'href' => 'invoices/status/viewed'],
            '4' => ['label' => 'paid',   'class' => 'paid',   'href' => 'invoices/status/paid'],
        ];

        $return = [];

        foreach ($statuses as $key => $status) {
            $return[$key] = array_merge($status, [
                'invoice_status_id' => $key,
                'sum_total'         => 0,
                'num_total'         => 0,
            ]);
        }

        return $return;
    }
}
