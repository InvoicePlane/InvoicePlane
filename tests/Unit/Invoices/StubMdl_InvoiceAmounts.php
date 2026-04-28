<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Invoices;

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
