<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201610 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-10');
    }

    public function down()
    {
        //
    }
}
