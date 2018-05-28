<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201616 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-16');
    }

    public function down()
    {
        //
    }
}
