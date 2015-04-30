<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

/**
 * Returns an array list of cldr => country, translated in the language $cldr.
 * If there is no translated country list, return the english one
 * @param $cldr
 * @return mixed
 */
function get_country_list($cldr)
{
    if (file_exists(APPPATH . 'helpers/country-list/' . $cldr . '/country.php')) {
        return (include APPPATH . 'helpers/country-list/' . $cldr . '/country.php');
    } else {
        return (include APPPATH . 'helpers/country-list/en/country.php');
    }

}

/**
 * Returns the countryname of a given $countrycode, , translated in the language $cldr
 * @param $cldr
 * @return mixed
 */
function get_country_name($cldr, $countrycode)
{
    $countries = get_country_list($cldr);
    return (isset($countries[$countrycode]) ? $countries[$countrycode] : $countrycode);
}