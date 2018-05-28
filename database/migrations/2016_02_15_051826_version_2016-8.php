<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class Version20168 extends Migration
{
    public function up()
    {
        Setting::saveByKey('version', '2016-8');
    }

    public function down()
    {
        //
    }
}
