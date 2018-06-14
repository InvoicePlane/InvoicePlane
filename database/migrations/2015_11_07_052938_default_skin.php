<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class DefaultSkin extends Migration
{
    public function up()
    {
        Setting::saveByKey('skin', 'skin-fusioninvoice.min.css');
    }

    public function down()
    {
        //
    }
}
