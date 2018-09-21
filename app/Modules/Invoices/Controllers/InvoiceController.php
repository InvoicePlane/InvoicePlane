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

namespace IP\Modules\Invoices\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\Invoices\Models\Invoice;
use IP\Support\FileNames;
use IP\Support\PDF\PDFFactory;
use IP\Support\Statuses\InvoiceStatuses;
use IP\Traits\ReturnUrl;

class InvoiceController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $invoices = Invoice::select('invoices.*')
            ->join('clients', 'clients.id', '=', 'invoices.client_id')
            ->join('invoice_amounts', 'invoice_amounts.invoice_id', '=', 'invoices.id')
            ->with(['client', 'activities', 'amount.invoice.currency'])
            ->status($status)
            ->keywords(request('search'))
            ->clientId(request('client'))
            ->companyProfileId(request('company_profile'))
            ->sortable(['invoice_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('ip.resultsPerPage'));

        return view('invoices.index')
            ->with('invoices', $invoices)
            ->with('status', $status)
            ->with('statuses', InvoiceStatuses::listsAllFlat() + ['overdue' => trans('ip.overdue')])
            ->with('keyedStatuses', collect(InvoiceStatuses::lists())->except(3))
            ->with('companyProfiles', ['' => trans('ip.all_company_profiles')] + CompanyProfile::getList())
            ->with('displaySearch', true);
    }

    public function delete($id)
    {
        Invoice::destroy($id);

        return redirect()->route('invoices.index')
            ->with('alert', trans('ip.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Invoice::destroy(request('ids'));
    }

    public function bulkStatus()
    {
        Invoice::whereIn('id', request('ids'))
            ->where('invoice_status_id', '<>', InvoiceStatuses::getStatusId('paid'))
            ->update(['invoice_status_id' => request('status')]);
    }

    public function pdf($id)
    {
        $invoice = Invoice::find($id);

        $pdf = PDFFactory::create();

        $pdf->download($invoice->html, FileNames::invoice($invoice));
    }
}