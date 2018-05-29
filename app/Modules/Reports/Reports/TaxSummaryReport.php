<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace FI\Modules\Reports\Reports;

use FI\Modules\Expenses\Models\Expense;
use FI\Modules\Invoices\Models\Invoice;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;
use FI\Support\NumberFormatter;
use FI\Support\Statuses\InvoiceStatuses;

class TaxSummaryReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $excludeUnpaidInvoices = 0)
    {
        $results = [
            'from_date' => DateFormatter::format($fromDate),
            'to_date' => DateFormatter::format($toDate),
            'total' => 0,
            'paid' => 0,
            'remaining' => 0,
            'records' => [],
        ];

        $invoices = Invoice::with(['items.taxRate', 'items.taxRate2', 'items.amount'])
            ->where('invoice_date', '>=', $fromDate)
            ->where('invoice_date', '<=', $toDate)
            ->where('invoice_status_id', '<>', InvoiceStatuses::getStatusId('canceled'));

        $expenseTax = (Expense::where('expense_date', '>=', $fromDate)
            ->where('expense_date', '<=', $toDate)
            ->sum('tax')) ?: 0;

        if ($companyProfileId) {
            $invoices->where('company_profile_id', $companyProfileId);
        }

        if ($excludeUnpaidInvoices) {
            $invoices->paid();
        }

        $invoices = $invoices->get();

        foreach ($invoices as $invoice) {
            foreach ($invoice->items as $invoiceItem) {
                if ($invoiceItem->tax_rate_id) {
                    $key = $invoiceItem->taxRate->name . ' (' . NumberFormatter::format($invoiceItem->taxRate->percent, null, 3) . '%)';

                    if (isset($results['records'][$key]['taxable_amount'])) {
                        $results['records'][$key]['taxable_amount'] += $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        $results['records'][$key]['taxes'] += $invoiceItem->amount->tax_1 / $invoice->exchange_rate;
                    } else {
                        $results['records'][$key]['taxable_amount'] = $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        $results['records'][$key]['taxes'] = $invoiceItem->amount->tax_1 / $invoice->exchange_rate;
                    }
                }

                if ($invoiceItem->tax_rate_2_id) {
                    $key = $invoiceItem->taxRate2->name . ' (' . NumberFormatter::format($invoiceItem->taxRate2->percent, null, 3) . '%)';

                    if (isset($results['records'][$key]['taxable_amount'])) {
                        if ($invoiceItem->taxRate2->is_compound) {
                            $results['records'][$key]['taxable_amount'] += ($invoiceItem->amount->subtotal + $invoiceItem->amount->tax_1) / $invoice->exchange_rate;
                        } else {
                            $results['records'][$key]['taxable_amount'] += $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        }

                        $results['records'][$key]['taxes'] += $invoiceItem->amount->tax_2 / $invoice->exchange_rate;
                    } else {
                        if ($invoiceItem->taxRate2->is_compound) {
                            $results['records'][$key]['taxable_amount'] = ($invoiceItem->amount->subtotal + $invoiceItem->amount->tax_2) / $invoice->exchange_rate;
                        } else {
                            $results['records'][$key]['taxable_amount'] = $invoiceItem->amount->subtotal / $invoice->exchange_rate;
                        }

                        $results['records'][$key]['taxes'] = $invoiceItem->amount->tax_2 / $invoice->exchange_rate;
                    }
                }
            }
        }

        foreach ($results['records'] as $key => $result) {
            $results['total'] = $results['total'] + $result['taxes'];
            $results['records'][$key]['taxable_amount'] = CurrencyFormatter::format($result['taxable_amount']);
            $results['records'][$key]['taxes'] = CurrencyFormatter::format($result['taxes']);
        }

        $results['paid'] = CurrencyFormatter::format($expenseTax);
        $results['remaining'] = CurrencyFormatter::format($results['total'] - $expenseTax);
        $results['total'] = CurrencyFormatter::format($results['total']);

        return $results;
    }
}