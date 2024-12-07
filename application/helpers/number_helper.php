<?php

if (! defined('BASEPATH')) {
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

/**
 * Output the amount as a currency amount, e.g. 1.234,56 €.
 *
 *
 * @return string
 */
function format_currency($amount)
{
    global $CI;
    $currency_symbol = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    //prevent null format
    if (null === $amount) {
        $amount = 0;
    }

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    }
    if ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    }

    return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $currency_symbol;

}

/**
 * Output the amount as a currency amount, e.g. 1.234,56.
 *
 * @param null $amount
 *
 * @return null|string
 */
function format_amount($amount = null)
{
    if ($amount) {
        $CI = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    }

}

/**
 * Output the amount as a currency amount, e.g. 1.234,56.
 *
 * @param null $amount
 *
 * @return null|string
 */
function format_quantity($amount = null)
{
    if ($amount) {
        $CI = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');
        /**
         * 1112: Since we have a new setting we have to deal with what if the "default_item_decimals" returns ""
         * That will crap out, because we're sending a string to number_format's second parameter.
         * Solution: cast it to an integer, set 2 as default (it's also 2 in the database when the setting is there)
         * And log an error.
         */
        $decimals = ($decimal_point) ? get_setting('default_item_decimals') : 0;
        if ($decimals == '') {
            log_message('error', 'setting for default_item_decimals is empty, did you upgrade the database?');
            $decimals = 2;
        }

        return number_format($amount, $decimals, $decimal_point, $thousands_separator);
    }

}

/**
 * Standardize an amount based on the system settings.
 *
 *
 * @return mixed
 */
function standardize_amount($amount)
{
    $CI = & get_instance();
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    $amount = str_replace($thousands_separator, '', $amount);
    $amount = str_replace($decimal_point, '.', $amount);

    return $amount;
}
