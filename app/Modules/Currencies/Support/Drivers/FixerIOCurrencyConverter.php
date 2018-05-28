<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Modules\Currencies\Support\Drivers;

class FixerIOCurrencyConverter
{
    /**
     * Returns the currency conversion rate.
     *
     * @param  string $from
     * @param  string $to
     * @return decimal
     */
    public function convert($from, $to)
    {
        try
        {
            $result = json_decode(file_get_contents('https://api.fixer.io/latest?base=' . $from . '&symbols=' . $to), true);

            return $result['rates'][strtoupper($to)];
        }
        catch (\Exception $e)
        {
            return '1.0000000';
        }

    }
}