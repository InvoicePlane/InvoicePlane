# How to: Adding e-Invoicing XML-templates

To implement a new e-invoicing xml template, there are 2 files that need to be placed in their respective folder.


> Add the configuration file (`Shortidv10.php`) in the folder `helpers/XMLconfigs/`

> and the xml-template file (`Shortidv10Xml.php`) in the folder `libraries/XMLtemplates`.


It is important to make the file names as short as possible and preferably use only numbers and letters.

Each country has its format specifications and version on which it is best to base the shortened name.

## The configuration file explanations to set items.

### The name of Configuration file (in `application/helpers/XMLconfigs/`)

```
Filename: 'Einvoicev10.php'    -> "Shortid" + "version" + ".php" : max 25 characters (without ".php")
                               (preferably without spaces " ", dots ".", hyphen "-", underscore "_" or special characters)
```

### Required in Configuration file

The Variable name is mandatory `$xml_setting` and it's an array and contain at minima this 4 keys

```
'full-name'   => 'E-Invoice v1.0',    -> String : CII / UBL version name. Visible in the client edit form (drop-down selector in e-invoice panel)
'countrycode' => 'EX',                -> String : Associated countrycode (if available in your native language country list)
'embedXML'    => false,               -> Bool   : To embed the Xml file in Pdf set to true (for 'ZUGFeRD' and similar)
'XMLname'     => 'e-invoice.xml',     -> String : Name of the embedded in a CII Pdf Xml file (if not, leave empty)
```

### Optional in Configuration `$xml_setting`

```
'generator'   => 'Einvoicev10',       -> String : Name of the Xml file generator without 'Xml' and '.php' extension (optional)
'options'     => String|Array|Object  -> Mixed  : If you need variables or specific codes in your generator (Optional)
```

### The name of XML template (generator) file (in `application/libraries/XMLtemplates/`)

```
Filename: 'Einvoicev10Xml.php'    -> Same name of config file with "Xml" (Don't need if you set the `generator` option)
```

#### Example of config files for the generator file `Einvoicev10.php` (in `application/libraries/XMLtemplates/`)

```php
<?php
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
  'full-name'   => 'E-invoice v1.0',
  'countrycode' => 'FR',
  'embedXML'    => true,
  'XMLname'     => 'e-invoice.xml',
];
```

<details>

<summary>

#### Example of config file use the same (`Einvoicev10.php`) generator file but for German country

</summary>

```php
<?php
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'E-invoice v1.0',
    'countrycode' => 'DE',
    'embedXML'    => true,
    'XMLname'     => 'e-rechnung.xml',
    'generator'   => 'Einvoicev10',
];
```
> Note: it would be named `Einvoicev10de.php` (or other)

</details>

> Note: Need to be placed in `application/helpers/XMLconfigs/`

---

### options
See in helpers/XMLconfigs folder
Nlciusciiv10 (Options for libraries/XMLtemplates/Facturxv10Xml)
Ublciusv131be or Ublsi20nl (Options for libraries/XMLtemplates/Ublv24Xml)

<details>

<summary>

#### Experimental XRechnung-CII-validation options

</summary>

```php
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
```


</details>

<details>

<summary>

#### UBL options

</summary>

```php
    'options'     => [
        'CustomizationID'     => 'urn:cen.eu:en16931:2017#conformant#urn:UBL.BE:1.0.0.20180214',
        'ProfileID'           => 'urn:fdc:peppol.eu:2017:poacc:billing:01:1.0', // Default
        'BuyerReference'      => true,
        'Delivery'            => true,
        'client_eas_code'     => '0208', // EAS code for EndpointID > schemeID : Adjust with what you need
        'user_eas_code'       => '0208', // EAS code for EndpointID > schemeID : Adjust with what you need
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

</details>

---

<details>

<summary>

## Example of generator (Minimal)

</summary>

```php
<?php
defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Class Einvoicev10Xml
 */
class Einvoicev10Xml extends stdClass
{
    public $invoice;
    public $items;
    public $doc;
    public $filename;
    public $currencyCode;
    public $options = []; // CustomizationID, Endpoint, ...
    public $item_decimals = 2;
    public $decimal_places = 2;
    public $legacy_calculation = false;

    public function __construct($params)
    {
        $this->invoice            = $params['invoice'];
        $this->items              = $params['items'];
        $this->filename           = $params['filename'];
        $this->options            = $params['options'];
        $this->currencyCode       = get_setting('currency_code');
        $this->item_decimals      = get_setting('default_item_decimals');
        $this->decimal_places     = get_setting('tax_rate_decimal_places');
        $this->legacy_calculation = config_item('legacy_calculation');
    }

    // IP call xml() function when send invoice (or print pdf if embedXML is true on config)
    public function xml()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = IP_DEBUG;
        // your code ...
        $this->doc->save(UPLOADS_TEMP_FOLDER . $this->filename . '.xml');
    }
}

```

</details>
