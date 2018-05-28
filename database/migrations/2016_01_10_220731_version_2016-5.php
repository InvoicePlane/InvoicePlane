<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20165 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-5');
    }

    public function down()
    {
        //
    }
}
