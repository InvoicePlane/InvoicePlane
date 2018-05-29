<?php

namespace FI\Widgets\Dashboard\InvoiceSummary\Controllers;

use FI\Http\Controllers\Controller;
use FI\Modules\Settings\Models\Setting;

class WidgetController extends Controller
{
    public function renderPartial()
    {
        Setting::saveByKey('widgetInvoiceSummaryDashboardTotals', request('widgetInvoiceSummaryDashboardTotals'));

        if (request()->has('widgetInvoiceSummaryDashboardTotalsFromDate')) {
            Setting::saveByKey('widgetInvoiceSummaryDashboardTotalsFromDate', request('widgetInvoiceSummaryDashboardTotalsFromDate'));
        }

        if (request()->has('widgetInvoiceSummaryDashboardTotalsToDate')) {
            Setting::saveByKey('widgetInvoiceSummaryDashboardTotalsToDate', request('widgetInvoiceSummaryDashboardTotalsToDate'));
        }

        Setting::setAll();

        return view('InvoiceSummaryWidget');
    }
}