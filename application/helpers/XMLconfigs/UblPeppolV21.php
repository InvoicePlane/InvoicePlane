<?php

defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'Peppol BIS Billing 3.0 (UBL 2.1)',
    'countrycode' => 'BE',
    'embedXML'    => false,
    'XMLname'     => '',
    'generator'   => 'Ublv24',
    'options'     => [
        'CustomizationID' => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:peppol.eu:2017:poacc:billing:3.0',
        'ProfileID'       => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
        'EndpointID'      => [
            'user'   => 'einvoice_identifier',
            'client' => 'peppol_id',
        ],
        'user_eas_code'       => '0088',
        'client_eas_code'     => '0088',
        'PartyIdentification' => true,
        'PartyLegalEntity'    => [
            'CompanyID' => 'vat_id',
            'SchemeID'  => false,
        ],
    ],
];
