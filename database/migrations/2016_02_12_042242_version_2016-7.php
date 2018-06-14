<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20167 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-7');
    }

    public function down()
    {
        //
    }
}
