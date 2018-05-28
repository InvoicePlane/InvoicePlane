<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20162 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-2');
    }

    public function down()
    {
        //
    }
}
