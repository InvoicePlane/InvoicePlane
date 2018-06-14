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
use IP\Modules\Expenses\Models\ExpenseCategory;
use IP\Modules\Expenses\Models\ExpenseVendor;
use IP\Modules\Reports\Reports\ExpenseListReport;
use IP\Modules\Reports\Requests\DateRangeRequest;
use IP\Support\PDF\PDFFactory;

class ExpenseListReportController extends Controller
{
    private $report;

    public function __construct(ExpenseListReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        return view('reports.options.expense_list')
            ->with('categories', ['' => trans('ip.all_categories')] + ExpenseCategory::getList())
            ->with('vendors', ['' => trans('ip.all_vendors')] + ExpenseVendor::getList());
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
            request('category_id'),
            request('vendor_id')
        );

        return view('reports.output.expense_list')
            ->with('results', $results);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(
            request('from_date'),
            request('to_date'),
            request('company_profile_id'),
            request('category_id'),
            request('vendor_id')
        );

        $html = view('reports.output.expense_list')
            ->with('results', $results)->render();

        $pdf->download($html, trans('ip.expense_list') . '.pdf');
    }
}