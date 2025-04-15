<?php

// Xrechnung CII 3.0.2 : https://github.com/ConnectingEurope/eInvoicing-EN16931/blob/master/cii/examples/CII_example5.xml
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'Xrechnung CII 3.0',
    'countrycode' => 'DE',
    'embedXML'    => false,
    'XMLname'     => 'e-invoice.xml',
    'generator'   => 'Facturxv10',
    'options'     => [
        // XRechnung-CII-validation
        'BusinessProcessSpecifiedDocumentContextParameterID' => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
        'GuidelineSpecifiedDocumentContextParameterID'       => 'urn:cen.eu:en16931:2017#compliant#urn:xeinkauf.de:kosit:xrechnung_3.0',
        'CII'                                                => true,
    ],
];
