<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201613 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-13');
    }

    public function down()
    {
        //
    }
}
