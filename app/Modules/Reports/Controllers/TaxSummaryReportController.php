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
use IP\Modules\Reports\Reports\TaxSummaryReport;
use IP\Modules\Reports\Requests\DateRangeRequest;
use IP\Support\PDF\PDFFactory;

class TaxSummaryReportController extends Controller
{
    private $report;

    public function __construct(TaxSummaryReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.tax_summary');
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
            request('exclude_unpaid_invoices')
        );

        return view('reports.output.tax_summary')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('exclude_unpaid_invoices')
        );

        $html = view('reports.output.tax_summary')
            ->with('results', $results)->render();

        $pdf->download($html, trans('ip.tax_summary') . '.pdf');
    }
}