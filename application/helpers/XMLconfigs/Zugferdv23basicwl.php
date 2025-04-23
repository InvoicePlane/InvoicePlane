<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * ZUGFeRD v2.3.2 (BASIC WL) : https://ecosio.com/en/peppol-and-xml-document-validator/
 */
 $xml_setting = [
    'full-name'   => 'ZUGFeRD v2.3 - basic wl', // Adjust the name (if you need)
    'countrycode' => 'DE',
    'embedXML'    => true,
    'XMLname'     => 'factur-x.xml', // The name of file embedded in PDF (xrechnung.xml or zugferd-invoice.xml ...)
    'generator'   => 'Facturxv10', // Use the libraries/XMLtemplates/Facturxv10Xml.php
    'options'     => [
        'GuidelineSpecifiedDocumentContextParameterID' => 'urn:factur-x.eu:1p0:basicwl',
    ],
];
