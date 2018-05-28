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
use FI\Modules\Reports\Reports\ClientStatementReport;
use FI\Modules\Reports\Requests\ClientStatementReportRequest;
use FI\Support\PDF\PDFFactory;

class ClientStatementReportController extends Controller
{
    private $report;

    public function __construct(ClientStatementReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.client_statement');
    }

    public function validateOptions(ClientStatementReportRequest $request)
    {

    }

    public function html()
    {
        $results = $this->report->getResults(
            request('client_name'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'));

        return view('reports.output.client_statement')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('client_name'),
            request('from_date'),
            request('to_date'),
            request('company_profile_id'));

        $html = view('reports.output.client_statement')
            ->with('results', $results)->render();

        $pdf->download($html, trans('fi.client_statement') . '.pdf');
    }
}