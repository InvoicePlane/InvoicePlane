<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Output the amount as a currency amount, e.g. 1.234,56 €
 *
 * @param $amount
 * @return string
 */
function format_currency($amount, $currency_id= '')
{
    global $CI;
    $currency_symbol = empty($currency_id) ? $CI->mdl_settings->setting('currency_symbol') : get_currency_symbol($currency_id);
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
 * Output the amount as a currency amount, e.g. 1.234,56
 *
 * @param null $amount
 * @return null|string
 */
function format_amount($amount = null)
{
    if ($amount) {
        $CI =& get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        return number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
    }
    return null;
}

/**
 * Standardize an amount based on the system settings
 *
 * @param $amount
 * @return mixed
 */
function standardize_amount($amount)
{
    $CI =& get_instance();
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    $amount = str_replace($thousands_separator, '', $amount);
    $amount = str_replace($decimal_point, '.', $amount);

    return $amount;
}

/**
 * Output the amount in string format like $12,000 + €50,000
 *
 * @param $amount, $symbol, $other_amount (amount which is formatted already)
 * @return $amount string
 */
function format_string_type_currency($amount, $symbol, $other_amount = '') {
    global $CI;
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $decimal_point = $CI->mdl_settings->setting('decimal_point');
    $thousands_separator = $CI->mdl_settings->setting('thousands_separator');

    if (!empty($other_amount)) {
        switch ($currency_symbol_placement) {
            case 'before':
                $amount = $other_amount . ' + ' . $symbol . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
                break;
            case 'afterspace':
                $amount = $other_amount . ' + ' . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $symbol;
                break;
            default:
                $amount = $other_amount . ' + ' . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $symbol;
        }
    } else {
        switch ($currency_symbol_placement) {
            case 'before':
                $amount = $symbol . number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator);
                break;
            case 'afterspace':
                $amount = number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . '&nbsp;' . $symbol;
                break;
            default:
                $amount = number_format($amount, ($decimal_point) ? 2 : 0, $decimal_point, $thousands_separator) . $symbol;
        }
    }
    return $amount;
}