<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Return a formated amount as a currency based on the system settings, e.g. 1.234,56 €.
 *
 * @param $amount
 *
 * @return string
 */
function format_currency($amount)
{
    $CI                        = & get_instance();
    $currency_symbol           = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator       = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point             = $CI->mdl_settings->setting('decimal_point');
    $decimals                  = $decimal_point ? (int) $CI->mdl_settings->setting('tax_rate_decimal_places') : 0;
    $amount                    = (float) (is_numeric($amount) ? $amount : standardize_amount($amount)); // prevent null format

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, $decimals, $decimal_point, $thousands_separator);
    }

    if ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, $decimals, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    }

    return number_format($amount, $decimals, $decimal_point, $thousands_separator) . $currency_symbol;
}

/**
 * Return a formated amount based on the system settings, e.g. 1.234,56.
 *
 *
 * @return null|string
 */
function format_amount($amount = null)
{
    if ($amount) {
        $CI                  = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point       = $CI->mdl_settings->setting('decimal_point');
        $decimals            = $decimal_point ? (int) $CI->mdl_settings->setting('tax_rate_decimal_places') : 0;
        $amount              = is_numeric($amount) ? $amount : standardize_amount($amount);

        return number_format($amount, $decimals, $decimal_point, $thousands_separator);
    }
}

/**
 * Return a formated amount as a quantity based on the system settings, e.g. 1.234,56.
 *
 *
 * @return null|string
 */
function format_quantity($amount = null)
{
    if ($amount) {
        $CI                  = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point       = $CI->mdl_settings->setting('decimal_point');
        $decimals            = $decimal_point ? (int) $CI->mdl_settings->setting('default_item_decimals') : 0;
        $amount              = is_numeric($amount) ? $amount : standardize_amount($amount);

        return number_format($amount, $decimals, $decimal_point, $thousands_separator);
    }
}

/**
 * Return a standardized amount for database based on the system settings, e.g. 1234.56.
 *
 * @param $amount
 *
 * @return mixed
 */
function standardize_amount($amount)
{
    if ($amount && ! is_numeric($amount)) {
        $CI                  = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point       = $CI->mdl_settings->setting('decimal_point');

        if ($thousands_separator == '.' && ! mb_substr_count($amount, ',') && mb_substr_count($amount, '.') > 1) {
            $amount[mb_strrpos($amount, '.')] = ','; // Replace last position of dot to comma
        }

        $amount = strtr($amount, [$thousands_separator => '', $decimal_point => '.']);
    }

    return $amount;
}
