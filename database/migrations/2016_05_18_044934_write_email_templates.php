<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class WriteEmailTemplates extends Migration
{
    public function up()
    {
        Setting::writeEmailTemplates();
    }

    public function down()
    {
        //
    }
}
