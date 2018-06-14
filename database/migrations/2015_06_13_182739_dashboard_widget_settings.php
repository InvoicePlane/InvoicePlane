<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class DashboardWidgetSettings extends Migration
{
    public function up()
    {
        Setting::saveByKey('widgetEnabledInvoiceSummary', '1');
        Setting::saveByKey('widgetInvoiceSummaryDashboardTotals', 'year_to_date');

        Setting::saveByKey('widgetEnabledQuoteSummary', '1');
        Setting::saveByKey('widgetQuoteSummaryDashboardTotals', 'year_to_date');
    }

    public function down()
    {
        //
    }
}
