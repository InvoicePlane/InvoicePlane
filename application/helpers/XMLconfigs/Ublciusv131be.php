<?php
/*
 * CIUS-BE UBL Invoice 1.31 : https://www.ubl.be/validator/ & https://ecosio.com/en/peppol-and-xml-document-validator/
 */
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'UBL.BE Invoice 1.31',
    'countrycode' => 'BE',
    'embedXML'    => false,
    'XMLname'     => 'e-invoice.xml',
    'generator'   => 'Ublv24',
    'options'     => [
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#conformant#urn:UBL.BE:1.0.0.20180214',
//      'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', // Default
        'BuyerReference'      => true,
        'Delivery'            => true,
        'EndpointID'          => 'tax_code',
        // [ubl-BE-01]-At least two AdditionalDocumentReference elements must be present.
        'DocumentReference'   => [
            // Need 2 cac:AdditionalDocumentReference
            ['UBL.BE', 'UBL.BE Compatible software Version 5.21'], // 1st: ID, DocumentDescription
            ['url', ['CommercialInvoice', 'CreditNote']]           // 2nd: [ubl-BE-02]- cbc:DocumentType : CommercialInvoice or CreditNote must be specified
        ],
        'PartyIdentification' => '',
        //  %  => [Name, TaxExemptionReasonCode] : [ubl-BE-15]-cac:ClassifiedTaxCategory/cbc:Name must be present.
        'TaxName'             => [
            21 => ['03'],            // R03 pour le taux standard de 21 %
            12 => ['02'],            // R02 pour le taux intermédiaire de 12 %
            6  => ['01'],            // R01 pour le taux réduit de 6 %
            0  => ['NS', 'BETE-NS'], // R00 pour le taux zéro de 0 % (code ID O)
        ],
        'InvoiceLineTaxTotal' => true,
        'PartyLegalEntity'    => ['CompanyID' => 'tax_code', 'SchemeID' => false],
    ],
];
