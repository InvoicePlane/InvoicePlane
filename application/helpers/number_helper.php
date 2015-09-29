<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

function format_currency($amount, $decimals = 2)
{
    // like in application/helpers/pdf_helper.php - generate_invoice_pdf()
    $CI =& get_instance();

    $currency_symbol = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator = format_thousands_separator($CI->mdl_settings->setting('thousands_separator'));
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, ($decimal_point) ? $decimals : 0, $decimal_point,
            $thousands_separator);
    } elseif ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, ($decimal_point) ? $decimals : 0, $decimal_point,
            $thousands_separator) . '&nbsp;' . $currency_symbol;
    } else {
        return number_format($amount, ($decimal_point) ? $decimals : 0, $decimal_point,
            $thousands_separator) . $currency_symbol;
    }
}

function format_amount($amount = NULL, $decimals = 2)
{
    if ($amount) {
        $CI =& get_instance();
        $thousands_separator = format_thousands_separator($CI->mdl_settings->setting('thousands_separator'));
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        return number_format($amount, ($decimal_point) ? $decimals : 0, $decimal_point, $thousands_separator);
    }
    return null;
}

function standardize_amount($amount)
{
    $CI =& get_instance();
    $thousands_separator = format_thousands_separator($CI->mdl_settings->setting('thousands_separator'));
    $decimal_point = $CI->mdl_settings->setting('decimal_point');

    $amount = str_replace($thousands_separator, '', $amount);
    $amount = str_replace($decimal_point, '.', $amount);

    return $amount;
}

function format_thousands_separator($thousands_separator)
{
    if (preg_match('/\\s+/', $thousands_separator)) {
        $thousands_separator = '&nbsp;';
    }
    return $thousands_separator;
}
