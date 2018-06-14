<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201618 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-18');
    }

    public function down()
    {
        //
    }
}
