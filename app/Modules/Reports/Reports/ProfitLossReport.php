<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Reports\Reports;

use FI\Modules\Expenses\Models\Expense;
use FI\Modules\Payments\Models\Payment;
use FI\Support\CurrencyFormatter;
use FI\Support\DateFormatter;

class ProfitLossReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $includeProfitBasedOn = 'invoice_date')
    {
        $results = [
            'from_date'      => DateFormatter::format($fromDate),
            'to_date'        => DateFormatter::format($toDate),
            'income'         => 0,
            'total_expenses' => 0,
            'net_income'     => 0,
            'expenses'       => [],
        ];

        $payments = Payment::select('payments.*')
            ->join('invoices', 'invoices.id', '=', 'payments.invoice_id')
            ->with('invoice');

        if ($includeProfitBasedOn == 'invoice_date')
        {
            $payments->where('invoices.invoice_date', '>=', $fromDate)->where('invoices.invoice_date', '<=', $toDate);
        }
        elseif ($includeProfitBasedOn == 'payment_date')
        {
            $payments->dateRange($fromDate, $toDate);
        }

        if ($companyProfileId)
        {
            $payments->where('invoices.company_profile_id', $companyProfileId);
        }

        $payments = $payments->get();

        foreach ($payments as $payment)
        {
            $results['income'] += $payment->amount / $payment->invoice->exchange_rate;
        }

        $expenses = Expense::where('expense_date', '>=', $fromDate)->where('expense_date', '<=', $toDate)->with('category');

        if ($companyProfileId)
        {
            $expenses->where('company_profile_id', $companyProfileId);
        }

        $expenses = $expenses->get();

        foreach ($expenses as $expense)
        {
            if (isset($results['expenses'][$expense->category->name]))
            {
                $results['expenses'][$expense->category->name] += $expense->amount;
            }
            else
            {
                $results['expenses'][$expense->category->name] = $expense->amount;
            }

            $results['total_expenses'] += $expense->amount;
        }

        foreach ($results['expenses'] as $category => $amount)
        {
            $results['expenses'][$category] = CurrencyFormatter::format($amount);
        }

        $results['net_income']     = CurrencyFormatter::format($results['income'] - $results['total_expenses']);
        $results['income']         = CurrencyFormatter::format($results['income']);
        $results['total_expenses'] = CurrencyFormatter::format($results['total_expenses']);

        ksort($results['expenses']);

        return $results;
    }
}