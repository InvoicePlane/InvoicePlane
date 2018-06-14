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

namespace IP\Modules\Reports\Controllers;

use IP\Http\Controllers\Controller;
use IP\Modules\Reports\Reports\ProfitLossReport;
use IP\Modules\Reports\Requests\DateRangeRequest;
use IP\Support\PDF\PDFFactory;

class ProfitLossReportController extends Controller
{
    private $report;

    public function __construct(ProfitLossReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.profit_loss');
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('include_profit_based_on')
        );

        return view('reports.output.profit_loss')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('include_profit_based_on')
        );

        $html = view('reports.output.profit_loss')
            ->with('results', $results)->render();

        $pdf->download($html, trans('ip.profit_and_loss') . '.pdf');
    }
}