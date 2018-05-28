<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support;

class Frequency
{
    /**
     * Returns a list of frequencies for recurring invoices.
     *
     * @return array
     */
    public static function lists()
    {
        return [
            '1' => trans('fi.days'),
            '2' => trans('fi.weeks'),
            '3' => trans('fi.months'),
            '4' => trans('fi.years')
        ];
    }
}