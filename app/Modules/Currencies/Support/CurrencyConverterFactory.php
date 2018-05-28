<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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