<?php

use FI\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class MerchantSettings extends Migration
{
    public function up()
    {
        Setting::saveByKey('merchant', json_encode([
            'PayPalExpress' => ['enabled' => 0, 'username' => '', 'password' => '', 'signature' => ''],
            'Stripe'        => ['enabled' => 0, 'secretKey' => '', 'publishableKey' => ''],
            'Mollie'        => ['enabled' => 0, 'apiKey' => ''],
        ]));
    }

    public function down()
    {
        //
    }
}
