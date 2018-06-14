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
use IP\Modules\Reports\Reports\ClientStatementReport;
use IP\Modules\Reports\Requests\ClientStatementReportRequest;
use IP\Support\PDF\PDFFactory;

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

        $pdf->download($html, trans('ip.client_statement') . '.pdf');
    }
}