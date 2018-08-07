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

namespace IP\Modules\Quotes\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\CompanyProfiles\Models\CompanyProfile;
use IP\Modules\Quotes\Models\Quote;
use IP\Support\FileNames;
use IP\Support\PDF\PDFFactory;
use IP\Support\Statuses\QuoteStatuses;
use IP\Traits\ReturnUrl;

class QuoteController extends Controller
{
    use ReturnUrl;

    public function index()
    {
        $this->setReturnUrl();

        $status = request('status', 'all_statuses');

        $quotes = Quote::select('quotes.*')
            ->join('clients', 'clients.id', '=', 'quotes.client_id')
            ->join('quote_amounts', 'quote_amounts.quote_id', '=', 'quotes.id')
            ->with(['client', 'activities', 'amount.quote.currency'])
            ->status($status)
            ->keywords(request('search'))
            ->clientId(request('client'))
            ->companyProfileId(request('company_profile'))
            ->sortable(['quote_date' => 'desc', 'LENGTH(number)' => 'desc', 'number' => 'desc'])
            ->paginate(config('ip.resultsPerPage'));

        return view('quotes.index')
            ->with('quotes', $quotes)
            ->with('status', $status)
            ->with('statuses', QuoteStatuses::listsAllFlat())
            ->with('keyedStatuses', QuoteStatuses::lists())
            ->with('companyProfiles', ['' => trans('ip.all_company_profiles')] + CompanyProfile::getList())
            ->with('displaySearch', true);
    }

    public function delete($id)
    {
        Quote::destroy($id);

        return redirect()->route('quotes.index')
            ->with('alert', trans('ip.record_successfully_deleted'));
    }

    public function bulkDelete()
    {
        Quote::destroy(request('ids'));
    }

    public function bulkStatus()
    {
        Quote::whereIn('id', request('ids'))->update(['quote_status_id' => request('status')]);
    }

    public function pdf($id)
    {
        $quote = Quote::find($id);

        $pdf = PDFFactory::create();

        $pdf->download($quote->html, FileNames::quote($quote));
    }
}