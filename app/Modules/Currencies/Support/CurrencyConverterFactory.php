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

namespace FI\Modules\Currencies\Support;

class CurrencyConverterFactory
{
    public static function create()
    {
        $class = 'FI\Modules\Currencies\Support\Drivers\\' . config('fi.currencyConversionDriver');

        return new $class;
    }
}