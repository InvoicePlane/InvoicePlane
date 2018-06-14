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
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\CustomFields\Models\CustomField;
use IP\Modules\Expenses\Models\Expense;
use IP\Modules\Expenses\Requests\ExpenseRequest;
use IP\Support\DateFormatter;
use IP\Support\NumberFormatter;
use IP\Traits\ReturnUrl;

class ExpenseCreateController extends Controller
{
    use ReturnUrl;

    public function create()
    {
        return view('expenses.form')
            ->with('editMode', false)
            ->with('companyProfiles', CompanyProfile::getList())
            ->with('currentDate', DateFormatter::format(date('Y-m-d')))
            ->with('customFields', CustomField::forTable('expenses')->get());
    }

    public function store(ExpenseRequest $request)
    {
        $record = request()->except('attachments', 'custom');

        $record['expense_date'] = DateFormatter::unformat($record['expense_date']);
        $record['amount'] = NumberFormatter::unformat($record['amount']);
        $record['tax'] = ($record['tax']) ? NumberFormatter::unformat($record['tax']) : 0;

        $expense = Expense::create($record);

        $expense->custom->update(request('custom', []));

        return redirect($this->getReturnUrl())
            ->with('alertSuccess', trans('ip.record_successfully_created'));
    }
}