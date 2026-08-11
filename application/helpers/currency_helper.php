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
 * Convert a major-unit amount to an integer minor-unit amount.
 */
function amount_to_minor_units(int|float|string $amount, int $minor_unit_multiplier): int
{
    if ($minor_unit_multiplier < 1) {
        throw new InvalidArgumentException('The minor-unit multiplier must be greater than zero.');
    }

    return (int) round((float) $amount * $minor_unit_multiplier);
}

/**
 * Convert a minor-unit amount to its major-unit representation.
 */
function amount_from_minor_units(int|float|string $amount, int $minor_unit_multiplier): float
{
    if ($minor_unit_multiplier < 1) {
        throw new InvalidArgumentException('The minor-unit multiplier must be greater than zero.');
    }

    return (float) $amount / $minor_unit_multiplier;
}
