<?php

if (! defined('BASEPATH')) {
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
    $CI = & get_instance();
    $currency_symbol           = $CI->mdl_settings->setting('currency_symbol');
    $currency_symbol_placement = $CI->mdl_settings->setting('currency_symbol_placement');
    $thousands_separator       = $CI->mdl_settings->setting('thousands_separator');
    $decimal_point             = $CI->mdl_settings->setting('decimal_point');
    $decimals                  = $decimal_point ? (int) $CI->mdl_settings->setting('tax_rate_decimal_places') : 0;
    $amount                    = floatval(is_numeric($amount) ? $amount : standardize_amount($amount)); // prevent null format

    if ($currency_symbol_placement == 'before') {
        return $currency_symbol . number_format($amount, $decimals, $decimal_point, $thousands_separator);
    } elseif ($currency_symbol_placement == 'afterspace') {
        return number_format($amount, $decimals, $decimal_point, $thousands_separator) . '&nbsp;' . $currency_symbol;
    } else {
        return number_format($amount, $decimals, $decimal_point, $thousands_separator) . $currency_symbol;
    }
}

/**
 * Return a formated amount based on the system settings, e.g. 1.234,56
 *
 *
 * @return null|string
 */
function format_amount($amount = null)
{
    if ($amount) {
        $CI = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point       = $CI->mdl_settings->setting('decimal_point');
        $decimals            = $decimal_point ? (int) $CI->mdl_settings->setting('tax_rate_decimal_places') : 0;
        $amount              = is_numeric($amount) ? $amount : standardize_amount($amount);

        return number_format($amount, $decimals, $decimal_point, $thousands_separator);
    }

    return null;
}

/**
 * Return a formated amount as a quantity based on the system settings, e.g. 1.234,56
 *
 *
 * @return null|string
 */
function format_quantity($amount = null)
{
    if ($amount) {
        $CI = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point       = $CI->mdl_settings->setting('decimal_point');
        $decimals            = $decimal_point ? (int) $CI->mdl_settings->setting('default_item_decimals') : 0;
        $amount              = is_numeric($amount) ? $amount : standardize_amount($amount);

        return number_format($amount, $decimals, $decimal_point, $thousands_separator);
    }

    return null;
}

/**
 * Return a standardized amount for database based on the system settings, e.g. 1234.56
 *
 * @param $amount
 *
 * @return mixed
 */
function standardize_amount($amount)
{
    if ($amount && ! is_numeric($amount)) {
        $CI = & get_instance();
        $thousands_separator = $CI->mdl_settings->setting('thousands_separator');
        $decimal_point = $CI->mdl_settings->setting('decimal_point');

        if ($thousands_separator == '.' && ! substr_count($amount, ',') && substr_count($amount, '.') > 1) {
            $amount[ strrpos($amount, '.') ] = ','; // Replace last position of dot to comma
        }

        $amount = strtr($amount, [$thousands_separator => '', $decimal_point => '.']);
    }

    return $amount;
}

/** since 1.6.3
 * Return if a is standardized taxes with legacy_calculation false in ipconfig
 * For obtain a Valid xml data. See in temp/einvoice-test.xml (debug true)
 *
 * @Scopes Invoices controllers
 *
 * @param $items
 */
function items_tax_usages_bad($items): mixed
{
    // Only Legacy calculation have global taxes - since v1.6.3
    if (config_item('legacy_calculation')) {
        return false;
    }

    // Check if taxe are in all or not alert
    $checks = [];
    $oks = [0,0];
    foreach ($items as $item) {
        if ($item->item_tax_rate_percent) {
            $oks[1]++;
            $checks[1][] = $item->item_id;
        } else {
            $oks[0]++;
            $checks[0][] = $item->item_id;
        }
    }

   // Bad: One with 0 Ok (false), No 0 NoOk (true)
    if ($oks[0] != 0 && $oks[1] != 0) {
        $CI = & get_instance();
        $CI->session->set_flashdata(
            'alert_warning',
            '<h3 class="pull-right"><a class="btn btn-default" href="javascript:check_items_tax_usages(true);">
           <i class="fa fa-cogs"></i> ' . trans('view') . '</a></h3>'
            . trans('items_tax_usages_bad_set')
        );
         return $checks;
    }

    return false;
}
