<?php
/*
* CIUS-PT UBL Invoice 2.1.1 :
* https://ecosio.com/en/peppol-and-xml-document-validator/
* https://svc.feap.gov.pt/Doc.Client/public/CIUSvalidation/PT?language=pt
* [explain](https://helpcenter.phcgo.net/PT/sug/ptxview.aspx?stamp=dg5eeb7efg4f6e6b16758g)
* Note:
*  Need in user 'Bank' field to prefix the value with the following '#REFERENCE@ATMPAYMENT#' or '#REFERENCE@DUCPAYMENT#' and suffice with '#'.
*  like : #REFERENCE@ATMPAYMENT#!#123456789#
*  OR empty to be valid
*/
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'CIUS-PT eSPap-UBL 2.1.1',
    'countrycode' => 'PT',
    'embedXML'    => false,
    'XMLname'     => 'e-invoice.xml',
    'generator'   => 'Ublv24',
    'options'     => [
        // [DT-CIUS-PT-022]-The BT-24 only allows the following value:
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#compliant#urn:feap.gov.pt:CIUS-PT:2.1.1',
        'ProfileID'           => 'urn:www:espap:pt:profiles:profile1:ver1.0',
        'BuyerReference'      => true,
        'Delivery'            => true,
        'EndpointID'          => 'tax_code',
        'PartyIdentification' => '',
        'PartyLegalEntity'    => ['CompanyID' => 'tax_code', 'SchemeID' => false],
    ],
];
