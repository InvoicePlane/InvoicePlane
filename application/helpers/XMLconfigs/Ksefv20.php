<?php

defined('BASEPATH') || exit('No direct script access allowed');
/*
 * KSeF 2.0 — FA(3) logical structure
 *
 * Documentation: https://ksef.podatki.gov.pl/
 * Test app: https://ap-test.ksef.mf.gov.pl/
 * Production app: https://ap.ksef.mf.gov.pl/
 */
$xml_setting = [
    'full-name'   => 'KSeF 2.0 - FA(3)',
    'countrycode' => 'PL',
    'embedXML'    => false,
    'XMLname'     => '', // Must be empty when not embedded in PDF
    'generator'   => 'Ksefv20', // Use libraries/XMLtemplates/Ksefv20Xml.php
    'options'     => [
        // All the custom fields below are optional. Create them in the respective models (User, Client, Invoice) and populate them as needed.
        // The dot notation is used to specify the model and field name (model.field_name).
        'custom_fields' => [
            // 'currency_code' => 'client.Currency code',
            // 'pln_exchange_rate' => 'invoice.PLN exchange rate',
            // 'seller_krs' => 'user.KRS',
            // 'seller_regon' => 'user.REGON',
            // 'seller_bdo' => 'user.BDO',
        ],
    ],
];
