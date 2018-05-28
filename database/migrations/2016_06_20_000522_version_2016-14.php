<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version201614 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-14');
    }

    public function down()
    {
        //
    }
}
