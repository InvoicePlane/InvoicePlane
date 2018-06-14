<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class PaymentButtonText extends Migration
{
    public function up()
    {
        $merchantConfig = json_decode(Setting::getByKey('merchant'), true);

        $merchantConfig['PayPalExpress']['paymentButtonText'] = 'Pay with PayPal';
        $merchantConfig['Stripe']['paymentButtonText']        = 'Pay with Stripe';
        $merchantConfig['Mollie']['paymentButtonText']        = 'Pay with Mollie';

        Setting::saveByKey('merchant', json_encode($merchantConfig));
    }

    public function down()
    {
        //
    }
}
