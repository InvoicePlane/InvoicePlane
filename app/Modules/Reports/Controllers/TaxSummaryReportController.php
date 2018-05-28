<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

        $pdf->download($html, trans('fi.tax_summary') . '.pdf');
    }
}