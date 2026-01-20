<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane - Clicksend Module
 *
 * @author		Matthias Schaffer
 * @copyright	Copyright (c) 2020 matthiasschaffer.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class View
 */
class View extends Base_Controller
{   
    private $clicksend_configured;
    private $clicksend_auth;

    /**
     * Clicksend constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('clicksend');

        $this->clicksend_configured = clicksend_configured();

        if ($this->clicksend_configured == false) {
            http_response_code(401);
            exit();
        }
    }

    /**
     * @param $clicksend_key
     * @param $invoice_url_key
     * @param bool $stream
     * @param null $invoice_template
     */
    public function generate_invoice_pdf($clicksend_key, $invoice_url_key, $stream = true, $invoice_template = null)
    {
        if($clicksend_key == md5(clicksend_getAuth())){
            $this->load->model('invoices/mdl_invoices');

            $invoice = $this->mdl_invoices->where('invoice_url_key', $invoice_url_key)->get();

            if ($invoice->num_rows() == 1) {
                $invoice = $invoice->row();

                if (!$invoice_template) {
                    $invoice_template = get_setting('pdf_invoice_template');
                }

                $this->load->helper('pdf');

                generate_invoice_pdf($invoice->invoice_id, $stream, $invoice_template, 1);
            }
        }
    }
}