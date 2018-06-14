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

namespace IP\Modules\Reports\Reports;

use IP\Modules\Expenses\Models\Expense;
use IP\Support\CurrencyFormatter;
use IP\Support\DateFormatter;

class ExpenseListReport
{
    public function getResults($fromDate, $toDate, $companyProfileId = null, $categoryId = null, $vendorId = null)
    {
        $results = [
            'from_date' => DateFormatter::format($fromDate),
            'to_date' => DateFormatter::format($toDate),
            'total' => '0',
            'expenses' => [],
        ];

        $expenses = Expense::defaultQuery()
            ->where('expense_date', '>=', $fromDate)
            ->where('expense_date', '<=', $toDate)
            ->orderBy('expense_date', 'desc')
            ->orderBy('id', 'desc');

        if ($companyProfileId) {
            $expenses->where('company_profile_id', $companyProfileId);
        }

        if ($categoryId) {
            $expenses->where('category_id', $categoryId);
        }

        if ($vendorId) {
            $expenses->where('vendor_id', $vendorId);
        }

        $expenses = $expenses->get();

        foreach ($expenses as $expense) {
            $results['expenses'][] = [
                'date' => $expense->formatted_expense_date,
                'amount' => $expense->formatted_amount,
                'tax' => $expense->formatted_tax,
                'category' => $expense->category_name,
                'vendor' => $expense->vendor_name,
                'client' => $expense->client_name,
                'billed' => $expense->has_been_billed,
            ];

            $results['total'] += $expense->amount;
        }

        $results['total'] = CurrencyFormatter::format($results['total']);

        return $results;
    }
}