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

namespace IP\Modules\RecurringInvoices\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\RecurringInvoices\Models\RecurringInvoice;
use IP\Support\Frequency;
use IP\Traits\ReturnUrl;

class RecurringInvoiceController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $recurringInvoices = RecurringInvoice::select('recurring_invoices.*')
            ->join('clients', 'clients.id', '=', 'recurring_invoices.client_id')
            ->join('recurring_invoice_amounts', 'recurring_invoice_amounts.recurring_invoice_id', '=',
                'recurring_invoices.id')
            ->with(['client', 'activities', 'amount.recurringInvoice.currency'])
            ->keywords(request('search'))
            ->clientId(request('client'))
            ->status($status)
            ->companyProfileId(request('company_profile'))
            ->sortable(['next_date' => 'desc', 'id' => 'desc'])
            ->paginate(config('ip.resultsPerPage'));

        return view('recurring_invoices.index')
            ->with('recurringInvoices', $recurringInvoices)
            ->with('displaySearch', true)
            ->with('frequencies', Frequency::lists())
            ->with('status', $status)
            ->with('statuses', [
                'all_statuses' => trans('ip.all_statuses'),
                'active' => trans('ip.active'),
                'inactive' => trans('ip.inactive'),
            ])
            ->with('companyProfiles', ['' => trans('ip.all_company_profiles')] + CompanyProfile::getList());
    }

    public function delete($id)
    {
        RecurringInvoice::destroy($id);

        return redirect()->route('recurringInvoices.index')
            ->with('alert', trans('ip.record_successfully_deleted'));
    }
}
