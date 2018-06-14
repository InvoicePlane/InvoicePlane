<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201619 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-19');
    }

    public function down()
    {
        //
    }
}
