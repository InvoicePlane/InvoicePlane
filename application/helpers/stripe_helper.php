<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2026 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Return the multiplier Stripe uses to express an amount in a currency's minor unit.
 *
 * Stripe treats these currencies as zero-decimal currencies for charges:
 * https://docs.stripe.com/currencies#zero-decimal
 */
function stripe_minor_unit_multiplier(string $currency): int
{
    static $zero_decimal_currencies = [
        'BIF',
        'CLP',
        'DJF',
        'GNF',
        'JPY',
        'KMF',
        'KRW',
        'MGA',
        'PYG',
        'RWF',
        'VND',
        'VUV',
        'XAF',
        'XOF',
        'XPF',
    ];

    return in_array(strtoupper($currency), $zero_decimal_currencies, true) ? 1 : 100;
}
