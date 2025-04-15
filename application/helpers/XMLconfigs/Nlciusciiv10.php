<?php

/*
 * NLCIUS CII 1.0.3.9 : https://ecosio.com/en/peppol-and-xml-document-validator/
 * https://github.com/peppolautoriteit-nl/validation/blob/80e2a1e17d13698a68392575b675eb75c3d82288/schematron/nlcius-cii/NLCIUS-CII-validation.sch
 *
 * [BR-NL-1] For suppliers in the Netherlands the supplier MUST provide either a KVK or OIN number for its legal entity identifier
 * EAS Code with schemeID 0106 or 0190 (Electronic Address Scheme) Scope ram:SellerTradeParty/ram:SpecifiedLegalOrganization/ram:ID/@schemeID
 */
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'NLCIUS CII 1.0.3.9',
    'countrycode' => 'NL',
    'embedXML'    => false,
    'XMLname'     => 'e-invoice.xml',
    'generator'   => 'Facturxv10',
    'options'     => [
        // CII-validation supplierIsNL is_NLCIUS                                                                             is_NLCIUS-ext-gaccount
        'GuidelineSpecifiedDocumentContextParameterID' => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:nen.nl:nlcius:v1.0', // + #conformant#urn:fdc:nen.nl:gaccount:v1.0
        // [BR-O-10]-A VAT Breakdown (BG-23) with VAT Category code (BT-118) " Not subject to VAT" shall have a VAT exemption reason code (BT-121), meaning " Not subject to VAT" or a VAT exemption reason text (BT-120) " Not subject to VAT" (or the equivalent standard text in another language).
        'ExemptionReason'                              => 'Not subject to VAT',
        // [BR-NL-32] / [BR-NL-34] The use of an allowance reason code or charge reason code (ram:SpecifiedTradeAllowanceCharge/ram:ReasonCode) are not recommended, both on document level and on line level.
        // [BR-NL-35] The use of a tax exemption reason code (/*/ram:ApplicableHeaderTradeSettlement/ram:ApplicableTradeTax/ram:ExemptionReasonCode) is not recommended
        'NoReasonCode'                                 => true,
        'CII'                                          => true,
    ],
];
