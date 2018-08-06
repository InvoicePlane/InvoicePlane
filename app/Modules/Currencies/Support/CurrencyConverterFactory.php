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

$test = config('fi.currencyConversionDriver');
if(!isset($test) || $test === null)
{
$test = 'FixerIOCurrencyConverter';
}


        $class = 'IP\Modules\Currencies\Support\Drivers\\' . $test;

        return new $class;
    }
}