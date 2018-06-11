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
use FI\Modules\Reports\Reports\PaymentsCollectedReport;
use FI\Modules\Reports\Requests\DateRangeRequest;
use FI\Support\PDF\PDFFactory;

class PaymentsCollectedReportController extends Controller
{
    private $report;

    public function __construct(PaymentsCollectedReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.payments_collected');
    }

    public function validateOptions(DateRangeRequest $request)
    {

    }

    public function html()
    {
        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id')
        );

        return view('reports.output.payments_collected')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id')
        );

        $html = view('reports.output.payments_collected')
            ->with('results', $results)->render();

        $pdf->download($html, trans('ip.payments_collected') . '.pdf');
    }
}