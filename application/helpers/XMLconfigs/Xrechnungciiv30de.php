<?php
defined('BASEPATH') || exit('No direct script access allowed');
/*
 * This File is just for learn, experiment and test purpose. Because you need an external (API) service to send
 * But it work & fully valid with `Xrechnung CII 3.0.2` rule on https://ecosio.com/en/peppol-and-xml-document-validator/
 *
 * https://github.com/ConnectingEurope/eInvoicing-EN16931/blob/master/cii/examples/CII_example5.xml
 * XML Schema : CrossIndustryInvoice_100pD16B.xsd
 * Schematron : 1.3.12/cii/EN16931-CII-validation.xslt
 * Schematron : 3.0.2/XRechnung-CII-validation.xslt
 *
 * [user|client]_eas_code * [BR-CL-11]-Any registration identifier identification scheme identifier MUST be coded using one of the ISO 6523 ICD list* : 0002 0003 0004 0005 0006 0007 0008 0009 0010 0011 0012 0013 0014 0015 0016 0017 0018 0019 0020 0021 0022 0023 0024 0025 0026 0027 0028 0029 0030 0031 0032 0033 0034 0035 0036 0037 0038 0039 0040 0041 0042 0043 0044 0045 0046 0047 0048 0049 0050 0051 0052 0053 0054 0055 0056 0057 0058 0059 0060 0061 0062 0063 0064 0065 0066 0067 0068 0069 0070 0071 0072 0073 0074 0075 0076 0077 0078 0079 0080 0081 0082 0083 0084 0085 0086 0087 0088 0089 0090 0091 0093 0094 0095 0096 0097 0098 0099 0100 0101 0102 0104 0105 0106 0107 0108 0109 0110 0111 0112 0113 0114 0115 0116 0117 0118 0119 0120 0121 0122 0123 0124 0125 0126 0127 0128 0129 0130 0131 0132 0133 0134 0135 0136 0137 0138 0139 0140 0141 0142 0143 0144 0145 0146 0147 0148 0149 0150 0151 0152 0153 0154 0155 0156 0157 0158 0159 0160 0161 0162 0163 0164 0165 0166 0167 0168 0169 0170 0171 0172 0173 0174 0175 0176 0177 0178 0179 0180 0183 0184 0185 0186 0187 0188 0189 0190 0191 0192 0193 0194 0195 0196 0197 0198 0199 0200 0201 0202 0203 0204 0205 0206 0207 0208 0209 0210 0211 0212 0213 0214 0215 0216 0217 0218 0219 0220 0221 0222 0223 0224 0225 0226 0227 0228 0229 0230
 * 0204 = EAS CODE (ICD) for Leitweg-ID (Replaces 9958)
 * 0088 = GLN identifier Electronic address value
 *
 * OPTIONS for * [URIUniversalCommunication => [user|client => schemeID => 'EAS(CEF) code']]
 * * [BR-CL-25]-Endpoint identifier scheme identifier MUST belong to the CEF EAS code list* : 0002 0007 0009 0037 0060 0088 0096 0097 0106 0130 0135 0142 0147 0151 0170 0183 0184 0188 0190 0191 0192 0193 0194 0195 0196 0198 0199 0200 0201 0202 0203 0204 0205 0208 0209 0210 0211 0212 0213 0215 0216 9901 9906 9907 9910 9913 9914 9915 9918 9919 9920 9922 9923 9924 9925 9926 9927 9928 9929 9930 9931 9932 9933 9934 9935 9936 9937 9938 9939 9940 9941 9942 9943 9944 9945 9946 9947 9948 9949 9950 9951 9952 9953 9955 9957 AN AQ AS AU EM
 * * CEF (Connecting Europe Facility) EAS (Electronic Address Scheme) code for EndpointID > schemeID : https://ec.europa.eu/digital-building-blocks/sites/display/DIGITAL/Code+lists
 * EM (by default) or
 * 9930 (PEPPOL) For Germany VAT number OR
 * 0204 (ICD)    For Leitweg-ID (Replaces 9958)
 * Or what you need
 */
$xml_setting = [
    'full-name'   => 'Xrechnung CII 3.0', // Adjust like : 'Xrechnung CII 3.0 - eas 0204' (if you need)
    'countrycode' => 'DE',
    'embedXML'    => false,
    'XMLname'     => '', // Must be empty when not embedded in PDF
    'generator'   => 'Facturxv10', // Use the libraries/XMLtemplates/Facturxv10Xml.php
    'options'     => [
        // XRechnung-CII-validation
        'BusinessProcessSpecifiedDocumentContextParameterID' => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0',
        'GuidelineSpecifiedDocumentContextParameterID'       => 'urn:cen.eu:en16931:2017#compliant#urn:xeinkauf.de:kosit:xrechnung_3.0',
        'CII'             => true,
        // [BR-CL-11]-Any registration identifier identification scheme identifier MUST be coded using one of the ISO 6523 ICD list
        // * EAS code for (seller|buyer)TradeParty > schemeID (Electronic Address Scheme) : https://github.com/ConnectingEurope/eInvoicing-EN16931/blob/master/codelist/iso6523/ICD-list.pdf
        // /rsm:CrossIndustryInvoice[1]/rsm:SupplyChainTradeTransaction[1]/ram:ApplicableHeaderTradeAgreement[1]/ram:BuyerTradeParty[1]/ram:SpecifiedLegalOrganization[1]/ram:ID[1] schemeID="`client_eas_code`"
        'client_eas_code' => '0204', // EAS code for client_tax_code schemeID
        // /rsm:CrossIndustryInvoice[1]/rsm:SupplyChainTradeTransaction[1]/ram:ApplicableHeaderTradeAgreement[1]/ram:SellerTradeParty[1]/ram:SpecifiedLegalOrganization[1]/ram:ID[1] schemeID="`user_eas_code`"
        'user_eas_code'   => '0204', // EAS code for user_tax_code schemeID
        // XRechnung-CII-validation (just set to `true` if you need client & user EM (email) id & scheme)
        'URIUniversalCommunication' => [
            // client_email & EM by default if not provided or empty/false
            'client' => [
                // From db: client_[email|vat_id|tax_code] (Idea: from client custom fields text. Q: Is reported in invoice object? R: NO!)
                'URIID'    => 'client_vat_id', // Related to schemeID (client_email by default if not provided or empty/false)
                // [BR-CL-25]-Endpoint identifier scheme identifier MUST belong to the CEF EAS code list
                'schemeID' => '9930', // 0204 for client_tax_code (EM by default if not provided or empty/false)
            ],
            // user_email & EM by default if not provided or empty/false
            'user' => [
                // From db: user_[email|vat_id|tax_code] (Idea: from user custom fields text. Q: Is reported in invoice object?)
                'URIID'    => 'user_vat_id', // Related to schemeID (user_email by default if not provided or empty/false)
                // [BR-CL-25]-Endpoint identifier scheme identifier MUST belong to the CEF EAS code list
                'schemeID' => '9930', // 0204 for user_tax_code (EM by default if not provided or empty/false)
            ],
        ],
    ],
];
