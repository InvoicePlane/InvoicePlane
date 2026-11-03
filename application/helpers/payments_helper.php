<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

use Money\Currencies\ISOCurrencies;

function get_currencies(): array
{
    //retrieve the available currencies
    $currencies    = new ISOCurrencies();
    $ISOCurrencies = [];
    foreach ($currencies as $currency) {
        $ISOCurrencies[$currency->getCode()] = $currency->getCode();
    }

    return $ISOCurrencies;
}

/**
 * The enabled gateways offered on the "Pay Now" page. Tabby has its own button.
 */
function pay_now_gateways(): array
{
    $CI = & get_instance();
    $CI->config->load('payment_gateways');

    $gateways = [];
    foreach (array_keys($CI->config->item('payment_gateways')) as $driver) {
        if ($driver !== 'Tabby' && get_setting('gateway_' . mb_strtolower($driver) . '_enabled') == 1) {
            $gateways[] = $driver;
        }
    }

    return $gateways;
}

function pay_now_enabled(): bool
{
    return get_setting('enable_online_payments') == 1 && pay_now_gateways() !== [];
}
