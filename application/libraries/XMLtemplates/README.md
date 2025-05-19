# How to: Adding XML-templates to generate e-Invoice file

To Activate the e-invoicing system, there are 2 files that need to be placed in their respective folder.

> Add the xml-template file like `Shortidv10Xml.php` in this folder and add the configuration file `Shortidv10.php` in the `helpers/XMLconfigs/` folder.

> [For more explanation see the README in XMLconfigs folder](../../helpers/XMLconfigs/README.md).

> You find more examples in [InvoicePlane-e-invoices](https://github.com/InvoicePlane/InvoicePlane-e-invoices) repository.

## Minimal Example of generator

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
