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

namespace FI\Modules\Reports\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Reports\Reports\TaxSummaryReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\PDF\PDFFactory;

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