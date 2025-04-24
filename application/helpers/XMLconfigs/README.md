# How to: Add e-Invoice configuration and template generator

To implement a new e-invoicing xml system, there are 2 files that need to be placed in their respective folder.

> Add the configuration file (`Shortidv10.php`) in this folder (`helpers/XMLconfigs/`)

> and the xml-template file (`Shortidv10Xml.php`) in the [`libraries/XMLtemplates/`](../../libraries/XMLtemplates) folder.

_It is important to make the file names as short as possible and preferably use only numbers and letters._

_Each **country** has its format **specifications** and version on which it is best to base the shortened name._

**You can find and download more e-invoice examples in [InvoicePlane-e-invoices](https://github.com/InvoicePlane/InvoicePlane-e-invoices) repository.**

---

### The name of files explanations

#### The name of Configuration file in `application/helpers/XMLconfigs/` folder (here)

```
Filename: 'Einvoicev10.php'    -> "Shortid" + "version" + ".php" : max 25 characters (without ".php")
                               (preferably without spaces " ", dots ".", hyphen "-", underscore "_" or special characters)
```

#### The name of XML template file ([generator](#XML_template)) in `application/libraries/XMLtemplates/` folder

```
Filename: 'Einvoicev10Xml.php'    -> Same name of config file with "Xml"
                                  (Don't need if you set the "generator" in configuration "$xml_setting" option (use other existing XMLtemplate))
```

---

### The configuration file explanations to set items

#### Required in Configuration file

The Variable name is mandatory `$xml_setting` and it's an array and contain at minima this 4 keys

```
'full-name'   => 'E-Invoice v1.0',    -> String : CII / UBL version name. Visible in the client edit form (drop-down selector in e-invoice panel)
'countrycode' => 'EX',                -> String : Associated countrycode (if available in your native language country list)
'embedXML'    => false,               -> Bool   : To embed the Xml file in Pdf set to true (for 'ZUGFeRD' and similar)
'XMLname'     => 'e-invoice.xml',     -> String : Name of the embedded in a CII Pdf Xml file (if not, leave empty)
```

**Optional in Configuration file for the `$xml_setting` array**

```
'generator'   => 'Einvoicev10',       -> String : Name of the Xml file generator without 'Xml' and '.php' extension (optional)
'options'     => ['Opt1' => 'param'], -> Mixed (String|Array|Object) : If you need variables or specific codes transmit to generator (Optional)
```

##### Example of config files for the [XML generator](#XML_template) file `Einvoicev10.php` to this folder (`application/helpers/XMLconfigs/`)

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

##### Example of config file use the same (`Einvoicev10.php`) XML template generator file but for German country

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

> Note: it would be named `Einvoicev10de.php` (or with you want, because the `generator` option are used)

</details>

> **_Note: Need to be placed in `application/helpers/XMLconfigs/`_**

---

<a id="XML_template"></a>
### XML template

**_Note: Need to be placed in [`application/libraries/XMLtemplates/`](../../libraries/XMLtemplates) folder_**


> A Minimal Example provided in [the README of XMLtemplates folder](../../libraries/XMLtemplates/README.md).
