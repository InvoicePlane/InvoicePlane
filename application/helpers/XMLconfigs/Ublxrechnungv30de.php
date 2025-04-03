<?php
// Xrechnung UBL Invoice 3.0
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'Xrechnung UBL Invoice 3.0',
    'countrycode' => 'DE',
    'embedXML'    => false,
    'XMLname'     => 'e-invoice.xml',
    'generator'   => 'Ublv24',
    'options'     => [
        // https://github.com/itplr-kosit/xrechnung-schematron/blob/1e7ae3ba0ff806c7e0098a442b8c940d15429d14/src/validation/schematron/ubl/XRechnung-UBL-validation.sch#L226
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:xeinkauf.de:kosit:xrechnung_3.0',
//      'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', // Default
        'BuyerReference'      => true,
        'EndpointID'          => 'tax_code',
        'PartyIdentification' => '',
        'PartyLegalEntity'    => ['CompanyID' => 'tax_code', 'SchemeID' => false],
    ],
];
