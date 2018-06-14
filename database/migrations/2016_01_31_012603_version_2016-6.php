<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20166 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-6');
    }

    public function down()
    {
        //
    }
}
