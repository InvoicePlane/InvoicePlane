<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version266 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2.6.6');
    }

    public function down()
    {
        //
    }
}
