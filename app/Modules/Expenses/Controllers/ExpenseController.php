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

namespace FI\Modules\Expenses\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\Expenses\Models\Expense;
use FI\Modules\Expenses\Models\ExpenseCategory;
use FI\Modules\Expenses\Models\ExpenseVendor;
use FI\Traits\ReturnUrl;

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
            ->paginate(config('fi.defaultNumPerPage'));

        return view('expenses.index')
            ->with('expenses', $expenses)
            ->with('displaySearch', true)
            ->with('categories', ['' => trans('fi.all_categories')] + ExpenseCategory::getList())
            ->with('vendors', ['' => trans('fi.all_vendors')] + ExpenseVendor::getList())
            ->with('statuses', ['' => trans('fi.all_statuses'), 'billed' => trans('fi.billed'), 'not_billed' => trans('fi.not_billed'), 'not_billable' => trans('fi.not_billable')])
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList());
    }

    public function delete($id)
    {
        Expense::destroy($id);

        return redirect($this->getReturnUrl())
            ->with('alertInfo', trans('fi.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Expense::destroy(request('ids'));
    }
}