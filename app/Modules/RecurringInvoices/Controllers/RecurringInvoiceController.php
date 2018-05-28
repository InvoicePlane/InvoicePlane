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

namespace FI\Modules\RecurringInvoices\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\CompanyProfiles\Models\CompanyProfile;
use FI\Modules\RecurringInvoices\Models\RecurringInvoice;
use FI\Support\Frequency;
use FI\Traits\ReturnUrl;

class RecurringInvoiceController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $recurringInvoices = RecurringInvoice::select('recurring_invoices.*')
            ->join('clients', 'clients.id', '=', 'recurring_invoices.client_id')
            ->join('recurring_invoice_amounts', 'recurring_invoice_amounts.recurring_invoice_id', '=', 'recurring_invoices.id')
            ->with(['client', 'activities', 'amount.recurringInvoice.currency'])
            ->keywords(request('search'))
            ->clientId(request('client'))
            ->status($status)
            ->companyProfileId(request('company_profile'))
            ->sortable(['next_date' => 'desc', 'id' => 'desc'])
            ->paginate(config('fi.resultsPerPage'));

        return view('recurring_invoices.index')
            ->with('recurringInvoices', $recurringInvoices)
            ->with('displaySearch', true)
            ->with('frequencies', Frequency::lists())
            ->with('status', $status)
            ->with('statuses', ['all_statuses' => trans('fi.all_statuses'), 'active' => trans('fi.active'), 'inactive' => trans('fi.inactive')])
            ->with('companyProfiles', ['' => trans('fi.all_company_profiles')] + CompanyProfile::getList());
    }

    public function delete($id)
    {
        RecurringInvoice::destroy($id);

        return redirect()->route('recurringInvoices.index')
            ->with('alert', trans('fi.record_successfully_deleted'));
    }
}