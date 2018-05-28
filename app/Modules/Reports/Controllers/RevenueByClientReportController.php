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
use FI\Modules\Reports\Reports\RevenueByClientReport;
use FI\Modules\Reports\Requests\YearRequest;
use FI\Support\DateFormatter;
use FI\Support\PDF\PDFFactory;

class RevenueByClientReportController extends Controller
{
    private $report;

    public function __construct(RevenueByClientReport $report)
    {
        $this->report = $report;
    }

    public function index()
    {
        $range = range(date('Y'), date('Y') - 5);

        return view('reports.options.revenue_by_client')
            ->with('years', array_combine($range, $range));
    }

    public function validateOptions(YearRequest $request)
    {

    }

    public function html()
    {
        $results = $this->report->getResults(request('company_profile_id'), request('year'));

        $months = [];

        foreach (range(1, 12) as $month)
        {
            $months[$month] = DateFormatter::getMonthShortName($month);
        }

        return view('reports.output.revenue_by_client')
            ->with('results', $results)
            ->with('months', $months);
    }

    public function pdf()
    {
        $pdf = PDFFactory::create();
        $pdf->setPaperOrientation('landscape');

        $results = $this->report->getResults(request('company_profile_id'), request('year'));

        $months = [];

        foreach (range(1, 12) as $month)
        {
            $months[$month] = DateFormatter::getMonthShortName($month);
        }

        $html = view('reports.output.revenue_by_client')
            ->with('results', $results)
            ->with('months', $months)
            ->render();

        $pdf->download($html, trans('fi.revenue_by_client') . '.pdf');
    }
}