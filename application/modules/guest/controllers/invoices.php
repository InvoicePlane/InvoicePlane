<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Invoices extends Guest_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('invoices/mdl_invoices');
    }

    public function index()
    {
        // Display open invoices by default
        redirect('guest/invoices/status/open');
    }

    public function status($status = 'open', $page = 0)
    {
        // Determine which group of invoices to load
        switch ($status) {
            case 'paid':
                $this->mdl_invoices->is_paid()->where_in('ip_invoices.client_id', $this->user_clients);
                break;
            default:
                $this->mdl_invoices->is_open()->where_in('ip_invoices.client_id', $this->user_clients);
                break;

        }

        $this->mdl_invoices->paginate(site_url('guest/invoices/status/' . $status), $page);
        $invoices = $this->mdl_invoices->result();

        $this->layout->set(
            array(
                'invoices' => $invoices,
                'status' => $status
            )
        );

        $this->layout->buffer('content', 'guest/invoices_index');
        $this->layout->render('layout_guest');
    }

    public function view($invoice_id)
    {
        $this->load->model('invoices/mdl_items');
        $this->load->model('invoices/mdl_invoice_tax_rates');

        $invoice = $this->mdl_invoices->where('ip_invoices.invoice_id', $invoice_id)->where_in('ip_invoices.client_id', $this->user_clients)->get()->row();

        if (!$invoice) {
            show_404();
        }

        $this->mdl_invoices->mark_viewed($invoice->invoice_id);

        $this->layout->set(
            array(
                'invoice' => $invoice,
                'items' => $this->mdl_items->where('invoice_id', $invoice_id)->get()->result(),
                'invoice_tax_rates' => $this->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
                'invoice_id' => $invoice_id
            )
        );

        $this->layout->buffer(
            array(
                array('content', 'guest/invoices_view')
            )
        );

        $this->layout->render('layout_guest');
    }

    public function generate_pdf($invoice_id, $stream = TRUE, $invoice_template = NULL)
    {
        $this->load->helper('pdf');

        $this->mdl_invoices->mark_viewed($invoice_id);

        generate_invoice_pdf($invoice_id, $stream, $invoice_template, 1);
    }

}
