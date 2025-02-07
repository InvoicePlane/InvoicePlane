<?php

if (! defined('BASEPATH'))
{
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
    $CI = & get_instance();
    $amount = floatval($amount); // prevent null format
    $currency_symbol = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

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
 * Standardize an amount for database based on the system settings
 *
 * @param $amount
 *
 * @return mixed
 */
function standardize_amount($amount)
{
    if ($amount && ! is_numeric($amount))
    {
        $CI = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        if ($thousands_separator == '.' && ! substr_count($amount, ',') && substr_count($amount, '.') > 1)
        {
            $amount[ strrpos($amount, '.') ] = ','; // Replace last position of dot to comma
        }

        $amount = strtr($amount, [$thousands_separator => '', $decimal_point => '.']);
    }
    return $amount;
}
