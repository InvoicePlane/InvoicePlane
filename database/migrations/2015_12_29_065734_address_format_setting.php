<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class AddressFormatSetting extends Migration
{
    public function up()
    {
        Setting::saveByKey('addressFormat', "{{ address }}\r\n{{ city }}, {{ state }} {{ postal_code }}");
    }

    public function down()
    {
        //
    }
}
