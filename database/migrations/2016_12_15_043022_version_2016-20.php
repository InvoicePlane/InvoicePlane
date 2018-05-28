<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201620 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-20');
    }

    public function down()
    {
        //
    }
}
