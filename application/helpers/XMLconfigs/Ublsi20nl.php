<?php

/*
 * SI-UBL 2.0 : https://test.peppolautoriteit.nl/validate & https://ecosio.com/en/peppol-and-xml-document-validator/
 * https://github.com/peppolautoriteit-nl/validation/blob/80e2a1e17d13698a68392575b675eb75c3d82288/schematron/si-ubl-2.0.sch
 *
 * [BR-NL-1] For suppliers in the Netherlands the supplier MUST provide either a KVK or OIN number for its legal entity identifier
 * EAS Code with schemeID 0106 or 0190 (Electronic Address Scheme) Scope seller PartyLegalEntity CompanyID/@schemeID
 */
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'SI UBL Invoice 2.0',
    'countrycode' => 'NL',
    'embedXML'    => false,
    'XMLname'     => 'e-invoice.xml',
    'generator'   => 'Ublv24',
    'options'     => [
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:nen.nl:nlcius:v1.0', // #conformant#urn:fdc:nen.nl:gaccount:v1.0
//      'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', // Default
        'BuyerReference'      => true,
        'Delivery'            => true,
        'EndpointID'          => 'tax_code',
        'PartyIdentification' => '',
        'InvoiceLineTaxTotal' => false,
        'PartyLegalEntity'    => ['CompanyID' => 'tax_code', 'SchemeID' => true],
        // [BR-NL-29] The use of a payment means text (cac:PaymentMeans/cbc:PaymentMeansCode/@name) is not recommended
        'NoPaymentMeansName'  => true,
        // [BR-NL-32] The use of an allowance reason code (cac:AllowanceCharge/cbc:AllowanceChargeReasonCode) is not recommended
        'NoReasonCode'        => true,
    ],
];
