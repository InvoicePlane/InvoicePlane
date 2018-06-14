<?php

use IP\Modules\Settings\Models\Setting;
use Illuminate\Database\Migrations\Migration;

class MigrateMerchantSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $merchantSettings = json_decode(Setting::where('setting_key', 'merchant')->first()->setting_value, true);

        Setting::whereIn('setting_key', [
            'merchant_Stripe_enabled',
            'merchant_Stripe_publishableKey',
            'merchant_Stripe_secretKey',
            'merchant_Stripe_paymentButtonText',
            'merchant_PayPal_paymentButtonText',
            'merchant_Mollie_enabled',
            'merchant_Mollie_apiKey',
            'merchant_Mollie_paymentButtonText',
        ])->delete();

        if (isset($merchantSettings['Stripe']['enabled']))
        {
            Setting::create(['setting_key' => 'merchant_Stripe_enabled', 'setting_value' => $merchantSettings['Stripe']['enabled']]);
        }

        if (isset($merchantSettings['Stripe']['publishableKey']))
        {
            Setting::create(['setting_key' => 'merchant_Stripe_publishableKey', 'setting_value' => $merchantSettings['Stripe']['publishableKey']]);
        }

        if (isset($merchantSettings['Stripe']['secretKey']))
        {
            Setting::create(['setting_key' => 'merchant_Stripe_secretKey', 'setting_value' => $merchantSettings['Stripe']['secretKey']]);
        }

        if (isset($merchantSettings['Stripe']['paymentButtonText']))
        {
            Setting::create(['setting_key' => 'merchant_Stripe_paymentButtonText', 'setting_value' => $merchantSettings['Stripe']['paymentButtonText']]);
        }
        else
        {
            Setting::create(['setting_key' => 'merchant_Stripe_paymentButtonText', 'setting_value' => 'Pay with Stripe']);
        }

        if (isset($merchantSettings['PayPalExpress']['paymentButtonText']))
        {
            Setting::create(['setting_key' => 'merchant_PayPal_paymentButtonText', 'setting_value' => $merchantSettings['PayPalExpress']['paymentButtonText']]);
        }
        else
        {
            Setting::create(['setting_key' => 'merchant_PayPal_paymentButtonText', 'setting_value' => 'Pay with PayPal']);
        }

        if (isset($merchantSettings['Mollie']['enabled']))
        {
            Setting::create(['setting_key' => 'merchant_Mollie_enabled', 'setting_value' => $merchantSettings['Mollie']['enabled']]);
        }

        if (isset($merchantSettings['Mollie']['apiKey']))
        {
            Setting::create(['setting_key' => 'merchant_Mollie_apiKey', 'setting_value' => $merchantSettings['Mollie']['apiKey']]);
        }

        if (isset($merchantSettings['Mollie']['paymentButtonText']))
        {
            Setting::create(['setting_key' => 'merchant_Mollie_paymentButtonText', 'setting_value' => $merchantSettings['Mollie']['paymentButtonText']]);
        }
        else
        {
            Setting::create(['setting_key' => 'merchant_Mollie_paymentButtonText', 'setting_value' => 'Pay with Mollie']);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
