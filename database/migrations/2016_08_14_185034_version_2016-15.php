<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201615 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-15');
    }

    public function down()
    {
        //
    }
}
