<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Expenses\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Expenses\Models\ExpenseCategory;
use FI\Modules\Expenses\Models\ExpenseVendor;

class ExpenseLookupController extends Controller
{
    public function lookupCategory()
    {
        $expenses = ExpenseCategory::select('name')
            ->where('name', 'like', '%' . request('query') . '%')
            ->orderBy('name')
            ->get();

        $list = [];

        foreach ($expenses as $expense)
        {
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

        foreach ($expenses as $expense)
        {
            $list[]['value'] = $expense->name;
        }

        return json_encode($list);
    }
}