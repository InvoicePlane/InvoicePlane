<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * SI-UBL 2.0 : https://test.peppolautoriteit.nl/validate & https://ecosio.com/en/peppol-and-xml-document-validator/ (UBL Invoice 2.4)
 * https://github.com/peppolautoriteit-nl/validation/blob/80e2a1e17d13698a68392575b675eb75c3d82288/schematron/si-ubl-2.0.sch
 *
 * * [BR-CL-25]-Endpoint identifier scheme identifier MUST belong to the CEF EAS code list* : 0002 0007 0009 0037 0060 0088 0096 0097 0106 0130 0135 0142 0147 0151 0170 0183 0184 0188 0190 0191 0192 0193 0194 0195 0196 0198 0199 0200 0201 0202 0203 0204 0205 0208 0209 0210 0211 0212 0213 0215 0216 9901 9906 9907 9910 9913 9914 9915 9918 9919 9920 9922 9923 9924 9925 9926 9927 9928 9929 9930 9931 9932 9933 9934 9935 9936 9937 9938 9939 9940 9941 9942 9943 9944 9945 9946 9947 9948 9949 9950 9951 9952 9953 9955 9957 AN AQ AS AU EM
 * * CEF (Connecting Europe Facility) EAS (Electronic Address Scheme) code for EndpointID > schemeID : https://ec.europa.eu/digital-building-blocks/sites/display/DIGITAL/Code+lists
 * [BR-NL-1] For suppliers in the Netherlands the supplier MUST provide either a KVK or OIN number for its legal entity identifier
 * EAS Code with schemeID 0106 or 0190 (Electronic Address Scheme) Scope seller PartyLegalEntity CompanyID/@schemeID
 * 0106 ICD     Vereniging van Kamers van Koophandel en Fabrieken in Nederland (Association of Chambers of Commerce and Industry in the Netherlands), Scheme (EDIRA compliant);
 * 0190 ICD     Dutch Originator's Identification Number (Replaces 9954)
 * 9944 PEPPOL  Netherlands VAT number;
 */
$xml_setting = [
    'full-name'   => 'SI UBL Invoice 2.0 - ' . trans('vat_id'), // Adjust like : 'SI UBL Invoice 2.0 - EAS 9944' (if you need)
    'countrycode' => 'NL',
    'embedXML'    => false,
    'XMLname'     => '', // Must be empty when not embedded in PDF
    'generator'   => 'Ublv24', // Use the libraries/XMLtemplates/Ublv24Xml.php
    // Options in Ublv24 generator
    'options'     => [
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:nen.nl:nlcius:v1.0', // #conformant#urn:fdc:nen.nl:gaccount:v1.0
//      'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', // Default
        'BuyerReference'      => true,
        'Delivery'            => true,
        // [BR-CL-25]-Endpoint identifier scheme identifier MUST belong to the CEF EAS code list
        // [BR-NL-1] For suppliers in the Netherlands the supplier MUST provide either a KVK or OIN number for its legal entity identifier
        // /ubl:Invoice[1]/cac:AccountingCustomerParty[1]/cac:Party[1]/cbc:EndpointID[1] schemeID="`client_eas_code`"
        'client_eas_code'     => '9944', // *EAS code for EndpointID > schemeID : Adjust with what you need
        // /ubl:Invoice[1]/cac:AccountingSupplierParty[1]/cac:Party[1]/cbc:EndpointID[1] schemeID="`user_eas_code`"
        'user_eas_code'       => '9944', // *EAS code for EndpointID > schemeID : Adjust with what you need
        // Adjust with what you need (vat_id or tax_code) : Note same for user & client
        'EndpointID'          => 'vat_id',
        'PartyLegalEntity'    => ['CompanyID' => 'vat_id', 'SchemeID' => true],
        'PartyIdentification' => false, // or '' or 0 or null
        'InvoiceLineTaxTotal' => false,
        // [BR-NL-29] The use of a payment means text (cac:PaymentMeans/cbc:PaymentMeansCode/@name) is not recommended
        'NoPaymentMeansName'  => true,
        // [BR-NL-32] The use of an allowance reason code (cac:AllowanceCharge/cbc:AllowanceChargeReasonCode) is not recommended
        'NoReasonCode'        => true,
    ],
];
