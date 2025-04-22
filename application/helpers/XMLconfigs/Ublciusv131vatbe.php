<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * UBL.BE Invoice 1.31 : https://www.ubl.be/validator/ & https://ecosio.com/en/peppol-and-xml-document-validator/
 * * [BR-CL-25]-Endpoint identifier scheme identifier MUST belong to the CEF EAS code list* : 0002 0007 0009 0037 0060 0088 0096 0097 0106 0130 0135 0142 0147 0151 0170 0183 0184 0188 0190 0191 0192 0193 0194 0195 0196 0198 0199 0200 0201 0202 0203 0204 0205 0208 0209 0210 0211 0212 0213 0215 0216 9901 9906 9907 9910 9913 9914 9915 9918 9919 9920 9922 9923 9924 9925 9926 9927 9928 9929 9930 9931 9932 9933 9934 9935 9936 9937 9938 9939 9940 9941 9942 9943 9944 9945 9946 9947 9948 9949 9950 9951 9952 9953 9955 9957 AN AQ AS AU EM
 * * CEF (Connecting Europe Facility) EAS (Electronic Address Scheme) code for EndpointID > schemeID : https://ec.europa.eu/digital-building-blocks/sites/display/DIGITAL/Code+lists
 * EAS CODES:
 * 0193 ICD                 UBL.BE party identifier
 * 0208 ICD                 Numero d'entreprise / ondernemingsnummer / Unternehmensnummer (Replaces 9956)
 * 9925 PEPPOL              Belgium VAT number
 * 9956 PEPPOL (DEPRECATED) Belgian Crossroad Bank of Enterprises (Replaced by 0208)
 *
 * /!\ Veuillez utiliser les taxes officielles pour eviter l'erreur `Document MUST not contain empty elements`*
 * NOTE : Pour une totale validité avec les taxes (%) : utiliser 21, 12, 6 or 0 (ou adjuster l'option TaxName)
 * NOTE : To be fully valid with tax (%) : use 21, 12, 6 or 0 (or adjust/update the TaxName option array)
 */
$xml_setting = [
    'full-name'   => 'UBL.BE Invoice 1.31 -  ' . trans('vat_id'), // Adjust like : 'UBL.BE Invoice 1.31 - N°TVA' (if you need)
    'countrycode' => 'BE',
    'embedXML'    => false,
    'XMLname'     => '', // Must be empty when not embedded in PDF
    'generator'   => 'Ublv24', // Use the libraries/XMLtemplates/Ublv24Xml.php
    // Options in Ublv24 generator
    'options'     => [
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#conformant#urn:UBL.BE:1.0.0.20180214',
//      'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', // Default
        'BuyerReference'      => true,
        'Delivery'            => true,
        // /ubl:Invoice[1]/cac:AccountingCustomerParty[1]/cac:Party[1]/cbc:EndpointID[1] schemeID="`client_eas_code`"
        'client_eas_code'     => '9925', // *EAS code for EndpointID > schemeID : Adjust with what you need (0204 for tax_code)
        // /ubl:Invoice[1]/cac:AccountingSupplierParty[1]/cac:Party[1]/cbc:EndpointID[1] schemeID="`user_eas_code`"
        'user_eas_code'       => '9925', // *EAS code for EndpointID > schemeID : Adjust with what you need (0204 for tax_code)
        // Adjust with what you need (vat_id or tax_code) : Note same for user & client
        'EndpointID'          => 'vat_id',
        'PartyLegalEntity'    => ['CompanyID' => 'vat_id', 'SchemeID' => false],
        'InvoiceLineTaxTotal' => true,
        // [ubl-BE-01]-At least two AdditionalDocumentReference elements must be present.
        'DocumentReference'   => [
            // Need 2 cac:AdditionalDocumentReference
            ['UBL.BE', 'UBL.BE Compatible software Version 5.21'], // 1st: ID, DocumentDescription
            ['url', ['CommercialInvoice', 'CreditNote']]           // 2nd: [ubl-BE-02]- cbc:DocumentType : CommercialInvoice or CreditNote must be specified
        ],
        'PartyIdentification' => false, // or '' or 0 or null
        //  %  => [Name, TaxExemptionReasonCode] : [ubl-BE-15]-cac:ClassifiedTaxCategory/cbc:Name must be present. + [ubl-BE-10]-cac:cac:TaxTotal/cac:TaxSubtotal/cac:TaxCategory/cbc:Name must be in BTCC list
        'TaxName'             => [
            21 => ['03'],            // R03 pour le taux standard de 21 %
            12 => ['02'],            // R02 pour le taux intermédiaire de 12 %
            6  => ['01'],            // R01 pour le taux réduit de 6 %
            0  => ['NS', 'BETE-NS'], // R00 pour le taux zéro de 0 % (code ID O)
        ],
    ],
];
