<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace FI\Support;

class NumberFormatter
{
    /**
     * Formats a number accordingly.
     *
     * @param  float $number
     * @param  object $currency
     * @param  integer $decimalPlaces
     * @return float
     */
    public static function format($number, $currency = null, $decimalPlaces = null)
    {
        $currency      = ($currency) ?: config('fi.currency');
        $decimalPlaces = ($decimalPlaces) ?: config('fi.amountDecimals');

        return number_format($number, $decimalPlaces, $currency->decimal, $currency->thousands);
    }

    /**
     * Unformats a formatted number.
     *
     * @param  float $number
     * @param  object $currency
     * @return float
     */
    public static function unformat($number, $currency = null)
    {
        $currency = ($currency) ?: config('fi.currency');

        $number = str_replace($currency->decimal, 'D', $number);
        $number = str_replace($currency->thousands, '', $number);
        $number = str_replace('D', '.', $number);

        return $number;
    }
}