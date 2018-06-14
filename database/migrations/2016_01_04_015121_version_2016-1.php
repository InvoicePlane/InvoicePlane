<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20161 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-1');
    }

    public function down()
    {
        //
    }
}
