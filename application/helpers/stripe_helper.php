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
 * Three buckets, per https://docs.stripe.com/currencies:
 * - Zero-decimal currencies (https://docs.stripe.com/currencies#zero-decimal): the charge and
 *   the amount are the same, so the multiplier is 1.
 * - Three-decimal currencies (https://docs.stripe.com/currencies#three-decimal): the minor unit
 *   is a thousandth, so the multiplier is 1000. The resulting amount must additionally be a
 *   multiple of 10 — see stripe_minor_unit_rounding_step().
 * - Everything else, including the two special-case currencies (UGX, ISK) that Stripe treats as
 *   zero-decimal but requires expressed as a two-decimal value whose decimal part is always "00"
 *   (https://docs.stripe.com/currencies#special-cases): multiplier 100. For UGX/ISK the resulting
 *   amount must additionally be a multiple of 100 — see stripe_minor_unit_rounding_step().
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

    static $three_decimal_currencies = [
        'BHD',
        'JOD',
        'KWD',
        'OMR',
        'TND',
    ];

    $code = strtoupper($currency);

    if (in_array($code, $zero_decimal_currencies, true)) {
        return 1;
    }

    if (in_array($code, $three_decimal_currencies, true)) {
        return 1000;
    }

    return 100;
}

/**
 * The step a currency's minor-unit amount must be a multiple of, on top of
 * stripe_minor_unit_multiplier() — 1 for every currency except Stripe's documented special cases
 * (https://docs.stripe.com/currencies#special-cases, https://docs.stripe.com/currencies#three-decimal):
 * - UGX and ISK: backward-compatible zero-decimal currencies expressed with a decimal part that
 *   is always "00", i.e. the minor-unit amount must be a multiple of 100.
 * - Three-decimal currencies (BHD, JOD, KWD, OMR, TND): the minor-unit amount must be a multiple
 *   of 10.
 */
function stripe_minor_unit_rounding_step(string $currency): int
{
    static $round_to_nearest_100 = ['UGX', 'ISK'];

    static $three_decimal_currencies = [
        'BHD',
        'JOD',
        'KWD',
        'OMR',
        'TND',
    ];

    $code = strtoupper($currency);

    if (in_array($code, $round_to_nearest_100, true)) {
        return 100;
    }

    if (in_array($code, $three_decimal_currencies, true)) {
        return 10;
    }

    return 1;
}

/**
 * How far a captured amount may differ from the invoice balance for $currency and still be
 * accepted as a full payment, in major units.
 *
 * For every currency with no rounding step this is the plain float-precision slack (0.0001).
 * For UGX, ISK and the three-decimal currencies, stripe_amount_to_minor_units() rounds the
 * invoice balance to the nearest step before sending it to Stripe, so the amount Stripe actually
 * captures can differ from the balance by up to half that step — in either direction. A capture
 * check (or Mdl_Payments::record_external_payment()'s own balance guard) that still used the
 * plain 0.0001 tolerance here would reject a legitimately captured payment as a mismatch, or
 * refuse to record it as an "oversized" overpayment, purely because of Stripe's own rounding
 * rule — not because anything was actually wrong with the payment.
 */
function stripe_minor_unit_rounding_tolerance(string $currency): float
{
    $step = stripe_minor_unit_rounding_step($currency);

    if ($step <= 1) {
        return 0.0001;
    }

    return ($step / 2) / stripe_minor_unit_multiplier($currency);
}

/**
 * Convert a major-unit invoice amount to the integer Stripe expects for this currency: the plain
 * minor-unit conversion (amount_to_minor_units(), from currency_helper.php), then rounded to the
 * step stripe_minor_unit_rounding_step() requires, for the currencies where that step is more
 * than 1. Stripe rejects an amount that isn't a multiple of the required step outright, so this
 * is the single conversion the checkout-session line item should use.
 */
function stripe_amount_to_minor_units(int|float|string $amount, string $currency): int
{
    $minor_units = amount_to_minor_units($amount, stripe_minor_unit_multiplier($currency));
    $step        = stripe_minor_unit_rounding_step($currency);

    if ($step <= 1) {
        return $minor_units;
    }

    return (int) (round($minor_units / $step) * $step);
}
