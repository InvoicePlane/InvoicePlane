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

namespace IP\Support;

class NumberFormatter
{
    /**
     * Formats a number accordingly.
     *
     * @param  float   $number
     * @param  object  $currency
     * @param  integer $decimalPlaces
     * @return float
     */
    public static function format($number, $currency = null, $decimalPlaces = null)
    {
        $currency = ($currency) ?: config('ip.currency');
        $decimalPlaces = ($decimalPlaces) ?: config('ip.amountDecimals');

        return number_format($number, $decimalPlaces, $currency->decimal, $currency->thousands);
    }

    /**
     * Unformats a formatted number.
     *
     * @param  float  $number
     * @param  object $currency
     * @return float
     */
    public static function unformat($number, $currency = null)
    {
        $currency = ($currency) ?: config('ip.currency');

        $number = str_replace($currency->decimal, 'D', $number);
        $number = str_replace($currency->thousands, '', $number);
        $number = str_replace('D', '.', $number);

        return $number;
    }

    /**
     * Returns a proper calculated file size of an int representing the bytes of
     * a file
     *
     * @param int $bytes
     * @param int $round
     * @return string
     */
    public static function fileSize($bytes, $round = 2): string
    {
        $types = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];

        for ($i = 0; $bytes >= 1024 && $i < (count($types) - 1); $i++) {
            $bytes /= 1024;
        }

        $bytes = $round ? round($bytes, $round) : $bytes;

        return $bytes . ' ' . $types[$i];
    }
}
