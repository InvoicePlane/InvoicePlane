<?php

defined('BASEPATH') || exit('No direct script access allowed');
/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 * https://www.fatturapa.gov.it/it/norme-e-regole/documentazione-fattura-elettronica/formato-fatturapa/index.html
 * fatturaPA validazione / Validation tools:
 * https://www.amministrazionicomunali.it/fatturexml/
 * https://www.fatturacheck.it/
 *
 * Uses s2software/fatturapa from the root Composer dependencies.
 *
 */

/**
 * Class Fatturapav12Xml.
 */
#[\AllowDynamicProperties]
class Fatturapav12Xml
{
    public $invoice;

    public $items;

    public $filename;

    public $currencyCode;

    public $legacy_calculation = false;

    /**
     * @var bool
     */
    public $notax;

    public $options = [];

    /**
     * @var mixed[]
     */
    public $itemsSubtotalGroupedByTaxPercent = [];

    public function __construct(array $params)
    {
        if ( ! class_exists('FatturaPA')) {
            require_once FCPATH . 'vendor/autoload.php';
            require_once FCPATH . 'vendor/s2software/fatturapa/fatturapa.php';
        }

        $CI = & get_instance();
        $this->invoice = $params['invoice'];
        $this->items = $params['items'];
        $this->filename = $params['filename'];
        $this->options = $params['options'] ?? [];
        $this->currencyCode = $CI->mdl_settings->setting('currency_code');
        $this->legacy_calculation = config_item('legacy_calculation');
        $this->itemsSubtotalGroupedByTaxPercent = $this->itemsSubtotalGroupedByTaxPercent();
        $this->notax = $this->itemsSubtotalGroupedByTaxPercent === [];
    }

    public function xml(): void
    {
        $fatturapa = new FatturaPA('FPR12'); // Formato - https://git.io/fhm9g (default: FPR12 = Privati)

        // Imposta trasmittente (opzionale, altrimenti vengono presi i dati dal mittente)
        // Set sender (optional, otherwise data are taken from the sender)
/*
        $fatturapa->set_trasmittente([
            'paese'  => "IT",
            'codice' => "CODFSC12A34H567U", // https://forum.italia.it/t/dati-trasmittente-p-iva-o-cf/6883/14
        ]);
*/
        // Imposta mittente (fornitore) / Sender (supplier Seller) tax
        $mittente = [
            // Dati azienda emittente fattura
            'ragsoc'     => $this->invoice->user_company, // "La Mia Ditta Srl",
            'indirizzo'  => $this->invoice->user_address_1 . ($this->invoice->user_address_2 ? PHP_EOL . $this->invoice->user_address_2 : ''), // "Via Italia 12",
            'cap'        => $this->invoice->user_zip, // "00100",
            'comune'     => $this->invoice->user_city, // "Roma",
            'prov'       => $this->province($this->invoice->user_state),
            'paese'      => $this->invoice->user_country, // "IT",
            'piva'       => $this->nationalVatId($this->invoice->user_vat_id, $this->invoice->user_country),
            // Regime fiscale - https://git.io/fhmMd (default: RF01 = ordinario)
            'regimefisc' => $this->options['regimefisc'] ?? 'RF01', // Todo: from Options (XMLconfigs)
        ];
        if ($this->invoice->user_tax_code) {
            $mittente['codfisc'] = $this->invoice->user_tax_code; // "CODFSC23A45H671U"
        }

        $fatturapa->set_mittente($mittente);

        // Imposta destinatario (cliente) / Recipient (customer) tax
        $destinatario = [
            // Dati cliente destinatario fattura
            'ragsoc'     => $this->invoice->client_company, // "Il Mio Cliente Spa",
            'indirizzo'  => $this->invoice->client_address_1 . ($this->invoice->client_address_2 ? PHP_EOL . $this->invoice->client_address_2 : ''), // "Via Roma 24",
            'cap'        => $this->invoice->client_zip, // "20121",
            'comune'     => $this->invoice->client_city, // "Milano",
            'prov'       => $this->province($this->invoice->client_state),
            'paese'      => $this->invoice->client_country, // "IT",
            'piva'       => $this->nationalVatId($this->invoice->client_vat_id, $this->invoice->client_country),
        ];
        $recipientId = trim((string) ($this->invoice->client_einvoice_identifier ?? ''));
        if (filter_var($recipientId, FILTER_VALIDATE_EMAIL)) {
            $destinatario['sdi_pec'] = $recipientId;
        } else {
            $destinatario['sdi_codice'] = $recipientId !== '' ? $recipientId : '0000000';
        }
        if ($this->invoice->client_tax_code) {
            $destinatario['codfisc'] = $this->invoice->client_tax_code; // "CODFSC23A45H671U"
        }

        $fatturapa->set_destinatario($destinatario);

        // https://github.com/s2software/fatturapa/wiki/Costanti#tipo-documento
        $tipoDocumento = (float) $this->invoice->invoice_sign < 0 ? 'TD04' : 'TD01';
        // Imposta dati intestazione fattura / Set Invoice Header Data
        $fatturapa->set_intestazione([
            // Tipo documento / Document type - https://git.io/fhmMb (default = TD01 = fattura : Imposta TD04 se è una nota di credito)
            'tipodoc' => $tipoDocumento, // "TD01",
            // Valuta / Value (default = EUR)
            'valuta'  => $this->currencyCode, // "EUR",
            // Data e numero fattura / Invoice date & number
            'data'    => $this->invoice->invoice_date_created, // "2019-01-07",
            'numero'  => $this->invoice->invoice_number, // "2019/01",
        ]);

        // Composizione righe dettaglio / Composition lines detail
        foreach ($this->items as $n => $item) {
            $riga = [
                // Numero progressivo riga dettaglio / Progressive line number
                'num'         => ++$n,
                // Descrizione prodotto/servizio / Product/service description
                'descrizione' => $item->item_name . ($item->item_description ? PHP_EOL . $item->item_description : ''), // "Realizzazione sito internet $n",
                // Prezzo unitario del prodotto/servizio / Unit price
                'prezzo'      => FatturaPA::dec(floatval(($item->item_subtotal - $item->item_discount) / $item->item_quantity)),
                // Quantità / Qty
                'qta'         => FatturaPA::dec(floatval($item->item_quantity)), // 1
                // Prezzo totale (prezzo x qta) / Total price
                'importo'     => FatturaPA::dec(floatval($item->item_subtotal - $item->item_discount)),
                // % aliquota IVA / % VAT rate
                'perciva'     => FatturaPA::dec(floatval($item->item_tax_rate_percent)), // 22
                // (Natura IVA non indicata - https://goo.gl/93RW7v)
                'natura'      => $item->item_tax_rate_percent ? 'I' : 'N2.2',
            ];
            $fatturapa->add_riga($riga);
        }

        // Impostazione automatica totali / Automatically set totals
        // In alternativa alla set_totali, possiamo automaticamente generare i totali in base alle righe aggiunte in fattura.
        // As an alternative to set_totali, we can automatically generate totals based on the lines added to the invoice.
        if ($this->notax) {
            $opt['natura'] = 'N2.2';
        } else {
            $opt['esigiva'] = 'I'; // Esigibilità IVA / VAT collectability  - https://git.io/fhmDq
        }

        $totale = $fatturapa->set_auto_totali($opt);

        // todo: Improve by invoice->payments array? (Stripe/Paypal guest payment url?)
        // Imposta dati pagamento (opzionale) / Set payment data (optional)
        if ($this->invoice->invoice_balance != 0 && $this->invoice->user_iban) {
            $fatturapa->set_pagamento([
                    // Condizioni pagamento / Terms of payment - https://git.io/fhmD8 (default: TP02 = completo)
                    'condizioni' => "TP02"
                ],
                [ // Modalità (possibile più di una) https://git.io/fhmDu
                    [
                        'modalita' => "MP05", // bonifico / Bank transfer
                        'totale'   => FatturaPA::dec($this->invoice->invoice_balance), // totale iva inclusa
                        'scadenza' => $this->invoice->invoice_date_due, // Due date
                        'iban'     => $this->noSpace($this->invoice->user_iban), // 'IT88A0123456789012345678901'
                    ],
                    //~ [
                        //~ 'modalita' => "MP08", // carta di pagamento / payment card
                        //~ 'totale'   => FatturaPA::dec($this->invoice->invoice_balance),
                        //~ 'scadenza' => $this->invoice->invoice_date_due, // Due date
                    //~ ],
                ]
            );
        }

        // Impostazione libera nodo singolo / Single node free setup
        // Prestatore Telefono : Uno solo (sempre l'ultimo usato) / Just one (always the last one used)
        $tel = $this->invoice->user_phone ?: ($this->invoice->user_mobile ?: null);
        if ($tel) {
            $fatturapa->set_node('FatturaElettronicaHeader/CedentePrestatore/Contatti/Telefono', $tel);
            // $fatturapa->set_node('FatturaElettronicaHeader/CedentePrestatore/Contatti', ['Telefono' => $tel]);
        }

        if ($this->invoice->user_email) {
            $fatturapa->set_node('FatturaElettronicaHeader/CedentePrestatore/Contatti/Email', $this->invoice->user_email);
        }

        if ($this->invoice->user_fax) {
            $fatturapa->set_node('FatturaElettronicaHeader/CedentePrestatore/Contatti/Fax', $this->invoice->user_fax);
        }

/*
        Aggiunta libera di altri nodi nell'XML FatturaPA
        È possibile impostare/aggiungere ulteriori nodi nell'XML, rispettando le specifiche del formato FatturaPA.

        Free addition of further nodes in the InvoicePA XML
        It is possible to set up/add additional nodes in the XML, respecting the specifications of the InvoicePA format.
*/
        // Aggiunta libera a un elenco (più nodi con lo stesso nome) / Free addition to a list (several nodes with the same name)
        // $fatturapa->add_node('FatturaElettronicaBody/DatiGenerali/DatiDDT', ['NumeroDDT' => '1', 'DataDDT' => '2019-01-07']);
        // $fatturapa->add_node('FatturaElettronicaBody/DatiGenerali/DatiDDT', ['NumeroDDT' => '2', 'DataDDT' => '2019-01-10']);

        // Genera e salva l'XML
        // Shift name // progressivo da applicare al nome file (univoco, alfanumerico, max 5 caratteri)
        $_SERVER['CIIname'] = $fatturapa->filename($this->invoice->invoice_number);
        $xml                = $fatturapa->get_xml();
        if (IP_DEBUG) {
            $file = fopen(UPLOADS_TEMP_FOLDER . $this->filename . '.xml', 'w');
            fwrite($file, $xml);
            fclose($file);
        } else {
            $doc = new DOMDocument();
            $doc->formatOutput = false; // One line
            $doc->loadXML($xml);        // Get
            $doc->save(UPLOADS_TEMP_FOLDER . $this->filename . '.xml');
        }

    }

    // Eliminare gli spazi
    public function noSpace($str): string
    {
        return strtr($str, [' ' => '']);
    }

    private function nationalVatId($vatId, $countryCode): string
    {
        $vatId       = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $vatId));
        $countryCode = strtoupper((string) $countryCode);

        if ($countryCode !== '' && str_starts_with($vatId, $countryCode)) {
            return substr($vatId, strlen($countryCode));
        }

        return $vatId;
    }

    private function province($state): ?string
    {
        $state = strtoupper(trim((string) $state));

        return preg_match('/^[A-Z]{2}$/', $state) === 1 ? $state : null;
    }

    /**
     * @return float[]|int[]
     */
    public function itemsSubtotalGroupedByTaxPercent(): array
    {
        $result = [];
        foreach ($this->items as $item) {
            if ($item->item_tax_rate_percent == 0) {
                continue;
            }

            if ( ! isset($result[$item->item_tax_rate_percent])) {
                $result[$item->item_tax_rate_percent] = 0;
            }

            $result[$item->item_tax_rate_percent] += $item->item_subtotal;
        }

        return $result;
    }

}
