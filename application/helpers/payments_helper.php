<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

use Money\Currencies\ISOCurrencies;


/**
 * @return array
 */
function get_currencies(): array
 {
    //retrive the available currencies
    $currencies = new ISOCurrencies();
    $ISOCurrencies = [];
    foreach ($currencies as $currency) {
        $ISOCurrencies[$currency->getCode()] = $currency->getCode();
    }

    return $ISOCurrencies;
 }