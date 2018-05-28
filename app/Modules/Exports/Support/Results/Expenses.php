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

namespace FI\Modules\Exports\Support\Results;

use FI\Modules\Expenses\Models\Expense;

class Expenses implements SourceInterface
{
    public function getResults($params = [])
    {
        return Expense::select('expenses.expense_date', 'expenses.description', 'expenses.amount',
            'clients.name AS client_name', 'expense_categories.name AS category_name', 'expense_vendors.name AS vendor_name',
            'invoices.number AS invoice_number', 'users.name AS user_name', 'company_profiles.company')
            ->leftJoin('users', 'users.id', '=', 'expenses.user_id')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.category_id')
            ->leftJoin('clients', 'clients.id', '=', 'expenses.client_id')
            ->leftJoin('expense_vendors', 'expense_vendors.id', '=', 'expenses.vendor_id')
            ->leftJoin('invoices', 'invoices.id', '=', 'expenses.invoice_id')
            ->join('company_profiles', 'company_profiles.id', '=', 'expenses.company_profile_id')
            ->orderBy('invoices.number')
            ->get()
            ->toArray();
    }
}