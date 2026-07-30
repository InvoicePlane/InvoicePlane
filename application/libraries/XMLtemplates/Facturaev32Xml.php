<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
 * InvoicePlane Facturae Bootstrap proposal
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 * @Need        josemmo/facturae-php from the root Composer dependencies
 */

if ( ! class_exists(\josemmo\Facturae\Facturae::class)) {
    require_once FCPATH . 'vendor/autoload.php';
}

use josemmo\Facturae\Facturae;
use josemmo\Facturae\FacturaeParty;

/**
 * Class Facturaev32Xml
 */

class Facturaev32Xml extends stdClass
{
    public $invoice;

    public $items;

    public $filename;

    public $options = [];

    // public $currencyCode;
    // public $item_decimals = 2;
    // public $decimal_places = 2;
    // public $legacy_calculation = false;
    // public $options = []; // CustomizationID, Endpoint, ...
    // public $doc;
    // public $root;
    // public $notax;
    // public $itemsSubtotalGroupedByTaxPercent = [];

    public function __construct(array $params)
    {
        $this->invoice  = $params['invoice'];
        $this->items    = $params['items'];
        $this->filename = $params['filename'];
        $this->options  = $params['options'] ?? [];
        // $this->options            = $params['options'];
        // $this->currencyCode       = get_setting('currency_code');
        // $this->item_decimals      = get_setting('default_item_decimals');
        // $this->decimal_places     = get_setting('tax_rate_decimal_places');
        // $this->legacy_calculation = config_item('legacy_calculation');
    }

    public function xml(): void
    {
        // Creamos la factura
        $fac = new Facturae();

        // Asignamos el número EMP2017120003 a la factura
        // Nótese que Facturae debe recibir el lote y el
        // número separados

        // We assign the number EMP2017120003 to the invoice
        // Note that Facturae must receive the batch and the
        // number separately.

        // Nous attribuons le numéro EMP2017120003 à la facture
        // Notez que Facturae doit recevoir le lot et le numéro
        // séparément.

        // $fac->setNumber('EMP201712', '0003');
        $fac->setNumber($this->options['series'] ?? 'IP' . date('Ym'), $this->invoice->invoice_number);

        // Asignamos el 01/12/2017 como fecha de la factura
        $fac->setIssueDate($this->invoice->invoice_date_created); // '2017-12-01'

        // Incluimos los datos del vendedor y del comprador (ver ejemplo sencillo)

        // Incluimos los datos del vendedor
        $fac->setSeller(new FacturaeParty([
            'taxNumber' => $this->invoice->user_vat_id, // 'A00000000',
            'name'      => $this->invoice->user_company, // 'Perico de los Palotes S.A.',
            'address'   => $this->invoice->user_address_1 . ($this->invoice->user_address_2 ? PHP_EOL . $this->invoice->user_address_2 : ''), // 'C/ Falsa, 123',
            'postCode'  => $this->invoice->user_zip, // '12345',
            'town'      => $this->invoice->user_city, // 'Madrid',
            'province'  => $this->invoice->user_state ?: $this->invoice->user_city, // 'Madrid'
        ]));

        // Incluimos los datos del comprador,
        // con finos demostrativos el comprador será
        // una persona física en vez de una empresa
        $fac->setBuyer(new FacturaeParty([
            'isLegalEntity' => ! empty($this->invoice->client_company),
            'taxNumber'     => $this->invoice->client_vat_id,  // '00000000A',
            'name'          => $this->invoice->client_company, // 'Antonio',
            'firstSurname'  => $this->invoice->client_name,    // 'García',
            'lastSurname'   => $this->invoice->client_surname, // 'Pérez',
            'address'       => $this->invoice->client_address_1 . ($this->invoice->client_address_2 ? PHP_EOL . $this->invoice->client_address_2 : ''), // 'Avda. Mayor, 7',
            'postCode'      => $this->invoice->client_zip,  // '54321',
            'town'          => $this->invoice->client_city, // 'Madrid',
            'province'      => $this->invoice->client_state ?: $this->invoice->client_city, // 'Madrid'
        ]));

        // Add product(s)
        foreach ($this->items as $item) {
            $fac->addItem(
                $item->item_name,
                $this->formattedFloat(($item->item_subtotal - $item->item_discount) / $item->item_quantity),
                $item->item_quantity,
                Facturae::TAX_IVA,
                $item->item_tax_rate_percent,
            );
        }

        // Ya solo queda firmar la factura ...
        // All that remains is to sign the invoice ...
        // Il ne reste plus qu'à signer la facture ...
        // Todo?
/*
        $fac->sign(
          'ruta/hacia/banco-de-certificados.p12',
          null,
          'passphrase'
        );
*/
        if (IP_DEBUG) {
            $doc = new DOMDocument();
            $doc->formatOutput = true;     // Human readable
            $doc->loadXML($fac->export()); // Get
            $doc->save(UPLOADS_TEMP_FOLDER . $this->filename . '.xml');
        } else {
            $fac->export(UPLOADS_TEMP_FOLDER . $this->filename . '.xml');
        }
    }

    public function formattedFloat($amount, $nb_decimals = 2): string
    {
        return number_format(floatval($amount), $nb_decimals, '.', '');
    }
}
