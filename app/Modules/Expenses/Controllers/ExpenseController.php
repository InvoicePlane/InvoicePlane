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
use IP\Modules\Expenses\Models\Expense;
use IP\Modules\Expenses\Models\ExpenseCategory;
use IP\Modules\Expenses\Models\ExpenseVendor;
use IP\Traits\ReturnUrl;

class ExpenseController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $expenses = Expense::defaultQuery()
            ->keywords(request('search'))
            ->categoryId(request('category'))
            ->vendorId(request('vendor'))
            ->status(request('status'))
            ->companyProfileId(request('company_profile'))
            ->sortable(['expense_date' => 'desc'])
            ->paginate(config('ip.defaultNumPerPage'));

        return view('expenses.index')
            ->with('expenses', $expenses)
            ->with('displaySearch', true)
            ->with('categories', ['' => trans('ip.all_categories')] + ExpenseCategory::getList())
            ->with('vendors', ['' => trans('ip.all_vendors')] + ExpenseVendor::getList())
            ->with('statuses', [
                '' => trans('ip.all_statuses'),
                'billed' => trans('ip.billed'),
                'not_billed' => trans('ip.not_billed'),
                'not_billable' => trans('ip.not_billable'),
            ])
            ->with('companyProfiles', ['' => trans('ip.all_company_profiles')] + CompanyProfile::getList());
    }

    public function delete($id)
    {
        Expense::destroy($id);

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('ip.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Expense::destroy(request('ids'));
    }
}
