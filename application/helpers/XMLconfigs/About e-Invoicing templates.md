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
