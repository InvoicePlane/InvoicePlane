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
 * @param $amount
 *
 * @return string
 */
function format_currency($amount)
{
    $CI =& get_instance();
    $amount = floatval($amount);
    $currency_symbol = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    //prevent null format
    if(is_null($amount)) $amount = 0;

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    } elseif ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    } else {
        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $currency_symbol;
    }
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
    return null;
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

        return number_format($amount, ($decimal_point) ? (int) get_setting('default_item_decimals') : 0, $decimal_point, $thousands_separator);
    }
    return null;
}

/**
 * Standardize a database amount based on the system settings
 *
 * @param $amount
 *
 * @return mixed
 */
function standardize_amount($amount)
{
    if($amount && !is_numeric($amount)){
        $CI =& get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        #Detect point at last 2nd|3rd place #.##? Min 3 chars to Fix strrpos Argument #3 ($offset) must be contained in argument #1
        if($thousands_separator=='.' && isset($amount[2]) && strrpos($amount, '.', strlen($amount)-3) !== false){
            $amount[ strrpos($amount, '.') ] = ',';# replace last position of point to comma
        }

        $amount = str_replace($thousands_separator, '', $amount);
        $amount = str_replace($decimal_point, '.', $amount);

    }
    return $amount;
}