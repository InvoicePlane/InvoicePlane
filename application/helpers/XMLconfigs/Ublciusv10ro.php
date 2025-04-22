<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * CIUS-RO UBL Invoice 1.0.9 : https://ecosio.com/en/peppol-and-xml-document-validator/
 * https://facturis-online.ro/e-factura/biblioteca-cu-informatii-oficiale-despre-formatul-xml-pentru-e-factura.html
 * NOTE: Only valid with Currency Code (Cod Monedă) : RON
 * [BR-RO-030]-Daca Codul monedei facturii (BT-5) este altul decat RON, atunci Codul monedei de contabilizare a TVA (BT-6) trebuie sa fie RON. #If the Invoice currency code (BT-5) is other than RON, then the VAT accounting currency code(BT-6) must be RON.
 * Schematron ROeFactura-UBL-validation-Invoice_v1.0.9.xslt
 * * [BR-CL-25]-Endpoint identifier scheme identifier MUST belong to the CEF EAS code list* : 0002 0007 0009 0037 0060 0088 0096 0097 0106 0130 0135 0142 0147 0151 0170 0183 0184 0188 0190 0191 0192 0193 0194 0195 0196 0198 0199 0200 0201 0202 0203 0204 0205 0208 0209 0210 0211 0212 0213 0215 0216 9901 9906 9907 9910 9913 9914 9915 9918 9919 9920 9922 9923 9924 9925 9926 9927 9928 9929 9930 9931 9932 9933 9934 9935 9936 9937 9938 9939 9940 9941 9942 9943 9944 9945 9946 9947 9948 9949 9950 9951 9952 9953 9955 9957 AN AQ AS AU EM
 * * CEF (Connecting Europe Facility) EAS (Electronic Address Scheme) code for EndpointID > schemeID : https://ec.europa.eu/digital-building-blocks/sites/display/DIGITAL/Code+lists
 * 9947 PEPPOL EAS CODE FOR Romania VAT number
 */
$xml_setting = [
    'full-name'   => 'CIUS-RO UBL Invoice 1.0', // Adjust like : 'CIUS-RO UBL Invoice 1.0 - EAS 9947' (if you need)
    'countrycode' => 'RO',
    'embedXML'    => false,
    'XMLname'     => '', // Must be empty when not embedded in PDF
    'generator'   => 'Ublv24', // Use the libraries/XMLtemplates/Ublv24Xml.php
    // Options in Ublv24 generator
    'options'     => [
        // RO-CIUS-ID [old](https://i0.1616.ro/media/2/2621/33243/20445047/2/anexaro-cius-converted.pdf)
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:efactura.mfinante.ro:CIUS-RO:1.0.1',
        'BuyerReference'      => true,
        // /ubl:Invoice[1]/cac:AccountingCustomerParty[1]/cac:Party[1]/cbc:EndpointID[1] schemeID="`client_eas_code`"
        'client_eas_code'     => '9947', // *EAS code for EndpointID > schemeID : Adjust with what you need
        // /ubl:Invoice[1]/cac:AccountingSupplierParty[1]/cac:Party[1]/cbc:EndpointID[1] schemeID="`user_eas_code`"
        'user_eas_code'       => '9947', // *EAS code for EndpointID > schemeID : Adjust with what you need
        // Adjust with what you need (vat_id or tax_code) : Note same for user & client
        'EndpointID'          => 'vat_id',
        'PartyIdentification' => false, // or '' or 0 or null
        'PartyLegalEntity'    => ['CompanyID' => 'tax_code', 'SchemeID' => false],
    ],
];
