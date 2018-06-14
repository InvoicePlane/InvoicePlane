<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class RecentClientActivityWidget extends Migration
{
    public function up()
    {
        $maxDisplayOrder = Setting::where('setting_key', 'like', 'widgetDisplayOrder%')->max('setting_value');

        Setting::saveByKey('widgetEnabledClientActivity', 0);
        Setting::saveByKey('widgetDisplayOrderClientActivity', ($maxDisplayOrder + 1));
        Setting::saveByKey('widgetColumnWidthClientActivity', 4);
    }

    public function down()
    {
        //
    }
}
