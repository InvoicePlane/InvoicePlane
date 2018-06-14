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

namespace IP\Modules\Expenses\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Expenses\Models\ExpenseCategory;
use IP\Modules\Expenses\Models\ExpenseVendor;

class ExpenseLookupController extends Controller
{
    public function lookupCategory()
    {
        $expenses = ExpenseCategory::select('name')
            ->where('name', 'like', '%' . request('query') . '%')
            ->orderBy('name')
            ->get();

        $list = [];

        foreach ($expenses as $expense) {
            $list[]['value'] = $expense->name;
        }

        return json_encode($list);
    }

    public function lookupVendor()
    {
        $expenses = ExpenseVendor::select('name')
            ->where('name', 'like', '%' . request('query') . '%')
            ->orderBy('name')
            ->get();

        $list = [];

        foreach ($expenses as $expense) {
            $list[]['value'] = $expense->name;
        }

        return json_encode($list);
    }
}