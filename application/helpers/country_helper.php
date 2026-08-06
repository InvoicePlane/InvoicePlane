<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Returns an array list of cldr => country, translated in the language $cldr.
 * If there is no translated country list, return the english one.
 *
 * @param $cldr
 *
 * @return mixed
 */
function get_country_list(string $cldr)
{
    // Security: Only allow locale-shaped directory names to prevent path traversal
    if (preg_match('/^[a-z]{2}(_[A-Z]{2})?$/', $cldr) === 1
        && file_exists(APPPATH . 'helpers/country-list/' . $cldr . '/country.php')) {
        return include APPPATH . 'helpers/country-list/' . $cldr . '/country.php';
    }

    return include APPPATH . 'helpers/country-list/en/country.php';
}

/**
 * Returns the countryname of a given $countrycode, translated in the language $cldr.
 *
 * @param $cldr
 * @param $countrycode
 *
 * @return mixed
 */
function get_country_name($cldr, $countrycode)
{
    $countries = get_country_list($cldr);

    return $countries[$countrycode] ?? $countrycode;
}
