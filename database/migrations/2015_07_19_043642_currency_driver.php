<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class CurrencyDriver extends Migration
{
    public function up()
    {
        Setting::saveByKey('currencyConversionDriver', 'YQLCurrencyConverter');
    }

    public function down()
    {
        //
    }
}
