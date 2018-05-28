<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class TitleSetting extends Migration
{
    public function up()
    {
        Setting::saveByKey('headerTitleText', 'FusionInvoice');
    }

    public function down()
    {
        //
    }
}
