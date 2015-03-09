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
 * returns an array list of cldr => country, translated in the language $cldr.
 */
function get_country_list($cldr)
{
    return (include APPPATH . 'helpers/country-list/' . $cldr . '/country.php');
}