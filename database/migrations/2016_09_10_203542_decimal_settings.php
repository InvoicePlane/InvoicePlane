<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class DecimalSettings extends Migration
{
    public function up()
    {
        Setting::saveByKey('amountDecimals', 2);
        Setting::saveByKey('roundTaxDecimals', 3);
    }

    public function down()
    {
        //
    }
}
