<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class DashboardSettings extends Migration
{
    public function up()
    {
        Setting::saveByKey('widgetEnabledInvoiceSummary', 1);
        Setting::saveByKey('widgetInvoiceSummaryDashboardTotals', 'year_to_date');
        Setting::saveByKey('widgetEnabledQuoteSummary', 1);
        Setting::saveByKey('widgetQuoteSummaryDashboardTotals', 'year_to_date');
        Setting::saveByKey('widgetDisplayOrderInvoiceSummary', 1);
        Setting::saveByKey('widgetColumnWidthInvoiceSummary', 6);
        Setting::saveByKey('widgetDisplayOrderQuoteSummary', 2);
        Setting::saveByKey('widgetColumnWidthQuoteSummary', 6);
    }

    public function down()
    {
        //
    }
}
