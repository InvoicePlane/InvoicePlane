<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class DashboardTotalSetting extends Migration
{
    public function up()
    {
        Setting::saveByKey('dashboardTotals', 'year_to_date');
    }

    public function down()
    {
        //
    }
}
