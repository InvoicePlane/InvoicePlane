<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class PaypalTestMode extends Migration
{
    public function up()
    {
        $merchantConfig = json_decode(Setting::getByKey('merchant'), true);

        $merchantConfig['PayPalExpress']['testMode'] = 0;

        Setting::saveByKey('merchant', json_encode($merchantConfig));
    }

    public function down()
    {
        //
    }
}
