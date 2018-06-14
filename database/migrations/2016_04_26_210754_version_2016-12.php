<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201612 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-12');
    }

    public function down()
    {
        //
    }
}
