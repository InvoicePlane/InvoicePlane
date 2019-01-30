<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Invoices
 */
class Invoices extends Admin_Controller
{

    /**
     * Invoices constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_invoices');
    }

    public function index()
    {
        // Display all invoices by default
        redirect('invoices/status/all');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'all', $page = 0)
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'draft':
                $this->mdl_invoices->is_draft();
                break;
            case 'sent':
                $this->mdl_invoices->is_sent();
                break;
            case 'viewed':
                $this->mdl_invoices->is_viewed();
                break;
            case 'paid':
                $this->mdl_invoices->is_paid();
                break;
            case 'overdue':
                $this->mdl_invoices->is_overdue();
                break;
        }

        $this->mdl_invoices->paginate(site_url('invoices/status/' . $status), $page);
        $invoices = $this->mdl_invoices->result();

        $this->layout->set(
            [
                'invoices' => $invoices,
                'status' => $status,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_invoices'),
                'filter_method' => 'filter_invoices',
                'invoice_statuses' => $this->mdl_invoices->statuses(),
            ]
        );

        $this->layout->buffer('content', 'invoices/index');
        $this->layout->render();
    }

    public function archive()
    {
        $invoice_array = [];

        if (isset($_POST['invoice_number'])) {
            $invoiceNumber = $_POST['invoice_number'];
            $invoice_array = glob(UPLOADS_ARCHIVE_FOLDER . '*' . '_' . $invoiceNumber . '.pdf');
            $this->layout->set(
                [
                    'invoices_archive' => $invoice_array,
                ]);
            $this->layout->buffer('content', 'invoices/archive');
            $this->layout->render();

        } else {
            foreach (glob(UPLOADS_ARCHIVE_FOLDER . '*.pdf') as $file) {
                array_push($invoice_array, $file);
            }

            rsort($invoice_array);
            $this->layout->set(
                [
                    'invoices_archive' => $invoice_array,
                ]);
            $this->layout->buffer('content', 'invoices/archive');
            $this->layout->render();
        }
    }

    /**
     * @param $invoice
     */
    public function download($invoice)
    {
        header('Content-type: application/pdf');
        header('Content-Disposition: attachment; filename="' . urldecode($invoice) . '"');
        readfile(UPLOADS_ARCHIVE_FOLDER . urldecode($invoice));
    }

    /**
     * @param $invoice_id
     */
    public function view($invoice_id)
    {
        $this->load->model(
            [
                'mdl_items',
                'tax_rates/mdl_tax_rates',
                'payment_methods/mdl_payment_methods',
                'mdl_invoice_tax_rates',
                'custom_fields/mdl_custom_fields',
            ]
        );

        $this->load->helper("custom_values");
        $this->load->helper("client");
        $this->load->model('units/mdl_units');
        $this->load->module('payments');

        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_invoice_custom');

        $this->db->reset_query();

        /*$invoice_custom = $this->mdl_invoice_custom->where('invoice_id', $invoice_id)->get();

        if ($invoice_custom->num_rows()) {
            $invoice_custom = $invoice_custom->row();

            unset($invoice_custom->invoice_id, $invoice_custom->invoice_custom_id);

            foreach ($invoice_custom as $key => $val) {
                $this->mdl_invoices->set_form_value('custom[' . $key . ']', $val);
            }
        }*/

        $fields = $this->mdl_invoice_custom->by_id($invoice_id)->get()->result();
        $invoice = $this->mdl_invoices->get_by_id($invoice_id);

        if (!$invoice) {
            show_404();
        }

        $custom_fields = $this->mdl_custom_fields->by_table('ip_invoice_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->invoice_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_invoices->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->invoice_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->layout->set(
            [
                'invoice' => $invoice,
                'items' => $this->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
                'invoice_id' => $invoice_id,
                'tax_rates' => $this->mdl_tax_rates->get()->result(),
                'invoice_tax_rates' => $this->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
                'units' => $this->mdl_units->get()->result(),
                'payment_methods' => $this->mdl_payment_methods->get()->result(),
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'custom_js_vars' => [
                    'currency_symbol' => get_setting('currency_symbol'),
                    'currency_symbol_placement' => get_setting('currency_symbol_placement'),
                    'decimal_point' => get_setting('decimal_point'),
                ],
                'invoice_statuses' => $this->mdl_invoices->statuses(),
            ]
        );

        if ($invoice->sumex_id != null) {
            $this->layout->buffer(
                [
                    ['modal_delete_invoice', 'invoices/modal_delete_invoice'],
                    ['modal_add_invoice_tax', 'invoices/modal_add_invoice_tax'],
                    ['modal_add_payment', 'payments/modal_add_payment'],
                    ['content', 'invoices/view_sumex'],
                ]
            );
        } else {
            $this->layout->buffer(
                [
                    ['modal_delete_invoice', 'invoices/modal_delete_invoice'],
                    ['modal_add_invoice_tax', 'invoices/modal_add_invoice_tax'],
                    ['modal_add_payment', 'payments/modal_add_payment'],
                    ['content', 'invoices/view'],
                ]
            );
        }

        $this->layout->render();
    }

    /**
     * @param $invoice_id
     */
    public function delete($invoice_id)
    {
        // Get the status of the invoice
        $invoice = $this->mdl_invoices->get_by_id($invoice_id);
        $invoice_status = $invoice->invoice_status_id;

        if ($invoice_status == 1 || $this->config->item('enable_invoice_deletion') === true) {
            // If invoice refers to tasks, mark those tasks back to 'Complete'
            $this->load->model('tasks/mdl_tasks');
            $tasks = $this->mdl_tasks->update_on_invoice_delete($invoice_id);

            // Delete the invoice
            $this->mdl_invoices->delete($invoice_id);
        } else {
            // Add alert that invoices can't be deleted
            $this->session->set_flashdata('alert_error', trans('invoice_deletion_forbidden'));
        }

        // Redirect to invoice index
        redirect('invoices/index');
    }

    /**
     * @param $invoice_id
     * @param bool $stream
     * @param null $invoice_template
     */
    public function generate_pdf($invoice_id, $stream = true, $invoice_template = null)
    {
        $this->load->helper('pdf');

        if (get_setting('mark_invoices_sent_pdf') == 1) {
            $this->mdl_invoices->mark_sent($invoice_id);
        }

        generate_invoice_pdf($invoice_id, $stream, $invoice_template, null);
    }
	
    //---it---inizio
    /**
     * General XML Fattura Eletronica e lancia il download dell'XML
     * @param int $invoice_id
     */
    public function generate_xml($invoice_id)
    {
    	$invoice = $this->mdl_invoices->get_by_id($invoice_id);
    	$user_id = $invoice->user_id;
    	$this->load->model('users/mdl_users');
    	$user = $this->mdl_users->get_by_id($user_id);
    	
    	// Preleva campi custom
    	$customs = [];
    	// utente: regime fiscale
    	foreach ([
    	'IT_UTENTE_REGIMEFISC_ID' => 'utente_regimefisc',
    	'IT_UTENTE_NATURA_IVA0_ID' => 'utente_natura_iva0',
    	'IT_UTENTE_PROGR_XML_ID' => 'utente_progr_xml',
    	] as $env => $key)
    	{
    		$customs[$key] = NULL;
    		if (env($env))
    		{
    			$this->load->model('custom_fields/mdl_user_custom');
    			$user_custom = $this->mdl_user_custom->by_id($user_id)->where('user_custom_fieldid', env($env))->get()->row();
    			if ($user_custom)
    			{
    				$customs[$key] = $user_custom->user_custom_fieldvalue ? $user_custom->user_custom_fieldvalue : NULL;
    			}
    		}
    	}
    	// cliente: formato xml, codice e/o pec sdi
    	$client_id = $invoice->client_id;
    	foreach ([
    	'IT_CLIENTE_FORMATO_XML_ID' => 'cliente_formato_xml',
    	'IT_CLIENTE_SDI_CODICE_ID' => 'cliente_sdi_codice',
    	'IT_CLIENTE_SDI_PEC_ID' => 'cliente_sdi_pec',
    	] as $env => $key)
    	{
    		$customs[$key] = NULL;
    		if (env($env))
    		{
    			$this->load->model('custom_fields/mdl_client_custom');
    			$client_custom = $this->mdl_client_custom->by_id($client_id)->where('client_custom_fieldid', env($env))->get()->row();
    			if ($client_custom)
    			{
    				$customs[$key] = $client_custom->client_custom_fieldvalue ? $client_custom->client_custom_fieldvalue : NULL;
    			}
    		}
    	}
    	
    	// Genera XML fattura elettronica
    	// https://github.com/s2software/fatturapa
    	require_once(APPPATH.'libraries/fatturapa/fatturapa.php');
    	$formato = $customs['cliente_formato_xml'] ? $customs['cliente_formato_xml'] : 'FPR12';
    	$fatturapa = new FatturaPA($formato);	// privato vs pa
    	
    	// Mittente
    	$fatturapa->set_mittente([
    			// Dati azienda emittente fattura
    			'ragsoc' => $invoice->user_name,
    			'indirizzo' => $invoice->user_address_1,
    			'cap' => $invoice->user_zip,
    			'comune' => $invoice->user_city,
    			'prov' => $invoice->user_state,
    			'paese' => $invoice->user_country ? $invoice->user_country : 'IT',
    			'piva' => $invoice->user_vat_id ? $invoice->user_vat_id : NULL,
    			'codfisc' => $invoice->user_tax_code ? $invoice->user_tax_code : NULL,
    			// Regime fiscale - https://git.io/fhmMd
    			'regimefisc' => $customs['utente_regimefisc'],
    	]);
    	
    	// Destinatario
    	$fatturapa->set_destinatario([
    			// Dati cliente destinatario fattura
    			'ragsoc' => $invoice->client_name,
    			'indirizzo' => $invoice->client_address_1,
    			'cap' => $invoice->client_zip,
    			'comune' => $invoice->client_city,
    			'prov' => $invoice->client_state,
    			'paese' => $invoice->client_country ? $invoice->client_country : 'IT',
    			'piva' => $invoice->client_vat_id ? $invoice->client_vat_id : NULL,
    			'codfisc' => $invoice->client_tax_code ? $invoice->client_tax_code : NULL,
    			// Dati SdI (Sistema di Interscambio) del destinatario/cliente
    			// - Codice destinatario - da impostare in alternativa alla PEC
    			'sdi_codice' => $customs['cliente_sdi_codice'],
    			// - PEC destinatario - da impostare in alternativa al Codice
    			'sdi_pec' => $customs['cliente_sdi_pec'],
    	]);
    	
    	// Dati fattura
    	$fatturapa->set_intestazione([
    			// Tipo documento - https://git.io/fhmMb (default = TD01 = fattura)
    			'tipodoc' => "TD01",
    			// Valuta (default = EUR)
    			'valuta' => 'EUR',
    			// Data e numero fattura
    			'data' => $invoice->invoice_date_created,
    			'numero' => $invoice->invoice_number,
    	]);
    	
    	$this->load->model('invoices/mdl_items');
    	$items = $this->mdl_items->where('invoice_id', $invoice_id)->get()->result();
    	
    	// Righe dettagli
    	foreach ($items as $i => $item)
    	{
    		$description = trim($item->item_name) ? $item->item_name : '';
    		if ($item->item_description)
    		{
    			if ($description)
    				$description .= ' - ';
    			$description .= $item->item_description;
    		}
    		$fatturapa->add_riga([
    				// Numero progressivo riga dettaglio
    				'num' => $i+1,
    				// Descrizione prodotto/servizio
    				'descrizione' => $description,
    				// Prezzo unitario del prodotto/servizio
    				'prezzo' => FatturaPA::dec($item->item_price),
    				// Quantità
    				'qta' => FatturaPA::dec($item->item_quantity),
    				// Prezzo totale (prezzo x qta)
    				'importo' => FatturaPA::dec($item->item_subtotal), // imponibile riga
    				// % aliquota IVA
    				'perciva' => FatturaPA::dec($item->item_tax_rate_percent),	// NOTA: quindi per funzionare, l'iva va messa sulle righe
    				// (natura IVA non indicata, se l'iva è a 0)
    				'natura_iva0' => $item->item_tax_rate_percent == 0 ? $customs['utente_natura_iva0'] : NULL
    		]);
    	}
    	
    	// Totale
    	$merge = [];
    	$merge['esigiva'] = 'I';	// esigibilità IVA (scritta nell'XML solo se viene applicata l'IVA)
    	$totale = $fatturapa->set_auto_totali($merge);
    	
    	// Pagamento
    	if ($invoice->payment_method)
    	{
    		$payment_method_id = $invoice->payment_method;
    		$env = "IT_METODO_PAGAMENTO_ID_{$payment_method_id}_CODICE";
    		if (env($env))
    		{
    			// Modalità (possibile più di una) https://git.io/fhmDu
    			$modalita = [
    					'modalita' => env($env),	// codice pagamento corrispondente
    					'totale' => FatturaPA::dec($totale),	// totale iva inclusa
    					'scadenza' => $invoice->invoice_date_due,
    			];
    			
    			if (env($env) == "MP05" && !empty($user->user_iban))	// bonifico: scrive anche l'iban
    			{
    				$modalita['iban'] = $user->user_iban;
    			}
    			
    			$fatturapa->set_pagamento([
    					// Condizioni pagamento - https://git.io/fhmD8 (default: TP02 = completo)
    					'condizioni' => "TP02"
    			], $modalita);
    		}
    	}
    	
    	// XML:
    	// Progressivo: se campo Prossimo progressivo impostato, usa quello, altrimenti
    	// calcola il progressivo tramite il numero fattura, togliendo i catatteri non numerici
    	$progr = '';
    	if (isset($customs['utente_progr_xml']))
    	{
    		$progr = empty($customs['utente_progr_xml']) ? 1 : $customs['utente_progr_xml'];
    		// incrementa
    		//$this->mdl_user_custom->by_id($user_id)->where('user_custom_fieldid', env($env))->get()->row();
    		$this->mdl_user_custom->save_custom($user_id, [env('IT_UTENTE_PROGR_XML_ID') => $progr+1]);
    	}
    	else	// converte il numero (toglie cioè che non è un numero) in base36 (il massimo per il progressivo nel nome file è 5 caratteri)
    	{
    		$num = preg_replace("/[^0-9]/", '', $invoice->invoice_number);
    		$progr = strtoupper(base_convert($num, 10, 36));
    	}
    	
    	// Genera XML
    	$filename = $fatturapa->filename($progr);
    	$xml = $fatturapa->get_xml();
    	
    	// Scarica XML
    	$this->load->helper('download');
    	force_download($filename, $xml);
    }
    //---it---fine
    
    /**
     * @param $invoice_id
     */
    public function generate_zugferd_xml($invoice_id)
    {
        $this->load->model('invoices/mdl_items');
        $this->load->library('ZugferdXml', [
            'invoice' => $this->mdl_invoices->get_by_id($invoice_id),
            'items' => $this->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
        ]);

        $this->output->set_content_type('text/xml');
        $this->output->set_output($this->zugferdxml->xml());
    }

    public function generate_sumex_pdf($invoice_id)
    {
        $this->load->helper('pdf');

        generate_invoice_sumex($invoice_id);
    }

    public function generate_sumex_copy($invoice_id)
    {


        $this->load->model('invoices/mdl_items');
        $this->load->library('Sumex', [
            'invoice' => $this->mdl_invoices->get_by_id($invoice_id),
            'items' => $this->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
            'options' => [
                'copy' => "1",
                'storno' => "0",
            ],
        ]);

        $this->output->set_content_type('application/pdf');
        $this->output->set_output($this->sumex->pdf());
    }
	
    //---it---inizio
    public function preview_pdf($invoice_id, $stream = TRUE, $invoice_template = NULL)
    {
    	$this->load->helper('pdf');
    	
    	generate_invoice_pdf($invoice_id, $stream, $invoice_template, NULL, TRUE);
    }
    //---it---fine
    
    /**
     * @param $invoice_id
     * @param $invoice_tax_rate_id
     */
    public function delete_invoice_tax($invoice_id, $invoice_tax_rate_id)
    {
        $this->load->model('mdl_invoice_tax_rates');
        $this->mdl_invoice_tax_rates->delete($invoice_tax_rate_id);

        $this->load->model('mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        redirect('invoices/view/' . $invoice_id);
    }

    public function recalculate_all_invoices()
    {
        $this->db->select('invoice_id');
        $invoice_ids = $this->db->get('ip_invoices')->result();

        $this->load->model('mdl_invoice_amounts');

        foreach ($invoice_ids as $invoice_id) {
            $this->mdl_invoice_amounts->calculate($invoice_id->invoice_id);
        }
    }

}
