# How to: Adding e-Invoicing XML-templates

To implement a new e-invoicing xml template, there are 2 files that need to be placed in their respective folder.
Add the configuration file ("Shortidv10.php") in the folder "helpers/XMLconfigs/" and the xml-template file ("Shortidv10Xml.php") in the folder "libraries/XMLtemplates".
It is important to make the file names as short as possible and preferably use only numbers and letters.
Each country has its format specifications and version on which it is best to base the shortened name.

## The configuration file explanations to set items.

Filename      => 'Einvoicev10.php'    -> "Shortid" + "version" + ".php" : max 25 characters (without ".php")
                                         (preferably without spaces " ", dots ".", hyphen "-", underscore "_" or special characters)

'full-name'   => 'E-Invoice v1.0',    -> UBL version name visible in the clients drop-down menu
'countrycode' => 'EX',                -> associated countrycode (if available in your native language country list)
'embedXML'    => false,               -> for 'ZUGFeRD' (and similar = Xml embedded in Pdf) set to true
'XMLname'     => 'e-invoice.xml',     -> name of the Xml file (if embedded in a CII Pdf)
'generator'   => 'Ublv24',            -> name of the Xml file generator without 'Xml' and '.php' extension (optional)
'options'     => Array                -> Some specific derivation to be validated in country (Optional)

### Example of config file

#### Factur-X v1.0 (for French country)

```php
<?php

defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = array(
  'full-name'   => 'Factur-X v1.0',
  'countrycode' => 'FR',
  'embedXML'    => true,
  'XMLname'     => 'factur-x.xml',
);
```

#### ZUGFeRD v2.3 use Factur-X generator (for German country)

```php
<?php

defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'ZUGFeRD v2.3',
    'countrycode' => 'DE',
    'embedXML'    => true,
    'XMLname'     => 'factur-x.xml',
    'generator'   => 'Facturxv10',
];
```

### options
See in helpers/XMLconfigs folder
Nlciusciiv10 (Options for libraries/XMLtemplates/Facturxv10Xml)
Ublciusv131be or Ublsi20nl (Options for libraries/XMLtemplates/Ublv24Xml)

#### CII options

```php
    'options'     => [
        // CII-validation supplierIsNL is_NLCIUS
        'GuidelineSpecifiedDocumentContextParameterID'       => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:nen.nl:nlcius:v1.0',
        // CII-validation supplierIsNL is_NLCIUS-ext-gaccount
//      'GuidelineSpecifiedDocumentContextParameterID'       => 'urn:cen.eu:en16931:2017#compliant#urn:fdc:nen.nl:nlcius:v1.0#conformant#urn:fdc:nen.nl:gaccount:v1.0',
        // [BR-O-10]-A VAT Breakdown (BG-23) with VAT Category code (BT-118) " Not subject to VAT" shall have a VAT exemption reason code (BT-121), meaning " Not subject to VAT" or a VAT exemption reason text (BT-120) " Not subject to VAT" (or the equivalent standard text in another language).
        'ExemptionReason'                                    => 'Not subject to VAT',
        // [BR-NL-32] / [BR-NL-34] The use of an allowance reason code or charge reason code (ram:SpecifiedTradeAllowanceCharge/ram:ReasonCode) are not recommended, both on document level and on line level.
        // [BR-NL-35] The use of a tax exemption reason code (/*/ram:ApplicableHeaderTradeSettlement/ram:ApplicableTradeTax/ram:ExemptionReasonCode) is not recommended
        'NoReasonCode'                                       => true,
        'CII'                                                => true,
    ],
```

#### UBL options

```php
    'options'     => [
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#conformant#urn:UBL.BE:1.0.0.20180214',
        'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', // Default
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
        // [BR-NL-29] The use of a payment means text (cac:PaymentMeans/cbc:PaymentMeansCode/@name) is not recommended
        'NoPaymentMeansName'  => true,
        // [BR-NL-32] The use of an allowance reason code (cac:AllowanceCharge/cbc:AllowanceChargeReasonCode) is not recommended
        'NoReasonCode'        => true,
    ],
```
