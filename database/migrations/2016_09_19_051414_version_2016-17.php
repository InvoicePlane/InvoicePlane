<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201617 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-17');
    }

    public function down()
    {
        //
    }
}
