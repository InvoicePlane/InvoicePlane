## How to: Add e-Invoice configuration and template generator

> You can find and download more e-invoice examples in [InvoicePlane-e-invoices repository](https://github.com/InvoicePlane/InvoicePlane-e-invoices).

---

<details>

<summary>

#### File name rules

</summary>

_To implement a new e-invoicing xml system, there are 2 files that need to be placed in their respective folder._

> Add the configuration file (`Shortidv10.php`) in `helpers/XMLconfigs/` folder (here)

> and the generator file (`Shortidv10Xml.php`) in the [`libraries/XMLtemplates/`](../../libraries/XMLtemplates/) folder.

**Configuration filename (XML helper)**

```
Filename: 'Shortidv10.php'  -> "Shortid" + "version" + ".php" : max 25 characters (without ".php")
                            (preferably without spaces " ", dots ".", hyphen "-", underscore "_" or special characters)
```

**Generator filename (XML template)**

```
Filename: 'Shortidv10Xml.php'  -> Same name of configurator file with "Xml"
                               -> Don't respect this rule if the "generator" option is set. (Use an other template to make XML).
```

_It is important to make the file names as short as possible and preferably use only numbers and letters._

_Each **country** has its format **specifications** and version on which it is best to base the shortened name._

---

</details>

<details>

<summary>

#### Configuration rules

</summary>

_**Required**_

> The Variable `$xml_setting` name is mandatory and it's an array and contain at minima this 4 keys

```
'full-name'   => 'E-Invoice v1.0', // String : CII / UBL version name. Visible in the client edit form (drop-down selector in e-invoice panel)
'countrycode' => 'EX',             // String : Associated countrycode (if available in your native language country list)
'embedXML'    => false,            // Bool   : To embed the Xml file in Pdf set to true (for 'ZUGFeRD' and similar)
'XMLname'     => '',               // String : Name of the embedded in a CII Pdf Xml file (if not, leave empty)
```

_Optional_

```
'generator'   => 'Einvoicev10',       // String : Name of the Xml file generator without 'Xml' and '.php' extension (optional)
'options'     => ['Opt1' => 'param'], // Mixed (String|Array|Object) : If you need variables or specific codes transmit to generator (Optional)
```

<details>

<summary>

###### Dynamic file name and mime Options (Need for more specific electronic invoice)

</summary>

> _Change XML file **name** dynamically:_

_You have two ways for that:_

- 1: Set the `$_SERVER['CIIname']` with what you need in your [XML generator](#XML_template). It's look something like this:

```php
$_SERVER['CIIname'] = 'CII' . $this->invoice->user_vat_id . '_' . $this->invoice->invoice_number . '.xml';
```

- 2: In your configuration file add `CIIname` in `options` with someting like this:

```php
    'options' => ['CIIname' => 'CII{{{user_vat_id}}}_{{{invoice_number}}}.xml'],
```

- 2.1: _**Only work for not embed in PDF (Email attach file name)**_:
  - InvoicePlane (_in mailer_helper_) replace the `{{{tag}}}` by value (_Same system of [Email templates](https://wiki.invoiceplane.com/en/1.6/settings/email-templates)_).
  - Automatically change the attached file by this name (_in phpmailer_helper_).
  - _If you use `CIIname` in configuration `options` and `$_SERVER['CIIname']` in generator, the `CIIname` `options` replaced by the `$_SERVER['CIIname']` value._


> _Change file **mime** type (**Only for embed in PDF**):_

- Set `$_SERVER['CIImime']` in your [XML generator](#XML_template) with someting like this:

```php
$_SERVER['CIImime'] = 'application/json';
```

</details>

</details>


##### Examples of configuration file

> **Note:** For this example (and the following ones) we will name the [XML generator](#XML_template) `Einvoicev10Xml.php`.

###### Basic

_Filename: `Einvoicev10.php`_

- Name in selector (client form): _E-Facture v1.0 - French_
- Embed in PDF with `e-facture.xml` file
- Use `Einvoicev10Xml.php` to generate XML
  - _Same file base name of generator (`Einvoicev10`)_

```php
<?php
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
  'full-name'   => 'E-Facture v1.0',
  'countrycode' => 'FR',
  'embedXML'    => true,
  'XMLname'     => 'e-facture.xml',
];
```

<details>

<summary>

##### Show more examples of configuration files

</summary>

###### For German country

- Name in selector (client form): _E-Rechnung v1.0 - German_
- Embed in PDF with `e-rechnung.xml` file
- Use `Einvoicev10Xml.php` to generate XML

_Example filename: `Einvoicev10de.php`_

```php
<?php
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'E-Rechnung v1.0',
    'countrycode' => 'DE',
    'embedXML'    => true,
    'XMLname'     => 'e-rechnung.xml',
    'generator'   => 'Einvoicev10',
];
```

###### For Italian country

- Name in selector (client form): _E-Fattura v1.0 - Italian_
- Not embed in PDF
- Use `Einvoicev10Xml.php` to generate XML
- Use a dynamic name of electronic invoice when send a mail
  - (XML attachement file look like `IT98765432109876_321.xml`)

_Example filename: `Einvoicev10it.php`_

```php
<?php
defined('BASEPATH') || exit('No direct script access allowed');

$xml_setting = [
    'full-name'   => 'E-Fattura v1.0',
    'countrycode' => 'IT',
    'embedXML'    => false,
    'XMLname'     => '',
    'generator'   => 'Einvoicev10',
    'options'     => ['CIIname' => '{{{user_country}}}{{{user_vat_id}}}_{{{invoice_number}}}.xml'],
];
```

</details>

---

<a id="XML_template"></a>
#### XML Generator template

_Note: Need to be placed in [`application/libraries/XMLtemplates/`](../../libraries/XMLtemplates) folder_

##### A Minimal Example provided in [the README of XMLtemplates folder](../../libraries/XMLtemplates/README.md).
