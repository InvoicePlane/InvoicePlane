<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * Factur-X v1.0.7-2 (BASIC) : https://ecosio.com/en/peppol-and-xml-document-validator/
 */
$xml_setting = [
    'full-name'   => 'Factur-X v1.0 - basique', // Adjust like : 'Factur-X v1.0 - basic' (if you wish)
    'countrycode' => 'FR',
    'embedXML'    => true,
    'XMLname'     => 'factur-x.xml', // The name of file embedded in PDF
    'generator'   => 'Facturxv10', // Use the libraries/XMLtemplates/Facturxv10Xml.php
    'options'     => [
        'GuidelineSpecifiedDocumentContextParameterID' => 'urn:cen.eu:en16931:2017#compliant#urn:factur-x.eu:1p0:basic',
    ],
];
