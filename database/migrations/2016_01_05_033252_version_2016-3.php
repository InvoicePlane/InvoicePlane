<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20163 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-3');
    }

    public function down()
    {
        //
    }
}
