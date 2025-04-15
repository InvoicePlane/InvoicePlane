<?php

// CIUS-RO UBL Invoice 1.0.9 : https://ecosio.com/en/peppol-and-xml-document-validator/
// https://facturis-online.ro/e-factura/biblioteca-cu-informatii-oficiale-despre-formatul-xml-pentru-e-factura.html
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'CIUS-RO UBL Invoice 1.0',
    'countrycode' => 'RO',
    'embedXML'    => false,
    'XMLname'     => 'e-invoice.xml',
    'generator'   => 'Ublv24',
    'options'     => [
        // RO-CIUS-ID [old]https://i0.1616.ro/media/2/2621/33243/20445047/2/anexaro-cius-converted.pdf)
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.1',
        'BuyerReference'      => true,
        'EndpointID'          => 'tax_code',
        'PartyIdentification' => '',
        'PartyLegalEntity'    => ['CompanyID' => 'tax_code', 'SchemeID' => false],
    ],
];
