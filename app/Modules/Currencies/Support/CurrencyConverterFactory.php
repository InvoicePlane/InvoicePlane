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

namespace IP\Modules\Currencies\Support;

class CurrencyConverterFactory
{
    public static function create()
    {
        $fallback_driver = config('fi.currencyConversionDriver');
        if (!isset($fallback_driver) || $fallback_driver === null) {
            $fallback_driver = 'FixerIOCurrencyConverter';
        }

        $class = 'IP\Modules\Currencies\Support\Drivers\\' . $fallback_driver;

        return new $class;
    }
}
