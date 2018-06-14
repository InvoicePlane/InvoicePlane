<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20169 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-9');
    }

    public function down()
    {
        //
    }
}
