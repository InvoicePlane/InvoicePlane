<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

class Invoice_Groups extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_invoice_groups');
    }

    public function index($page = 0)
    {
        $this->mdl_invoice_groups->paginate(site_url('invoice_groups/index'), $page);
        $invoice_groups = $this->mdl_invoice_groups->result();

        $this->layout->set('invoice_groups', $invoice_groups);
        $this->layout->buffer('content', 'invoice_groups/index');
        $this->layout->render();
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('invoice_groups');
        }

        if ($this->mdl_invoice_groups->run_validation()) {
            $this->mdl_invoice_groups->save($id);
            redirect('invoice_groups');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_invoice_groups->prep_form($id)) {
                show_404();
            }
        } elseif (!$id) {
            $this->mdl_invoice_groups->set_form_value('invoice_group_left_pad', 0);
            $this->mdl_invoice_groups->set_form_value('invoice_group_next_id', 1);
        }

        $this->load->model('invoices/mdl_templates');
        $pdf_invoice_templates = $this->mdl_templates->get_invoice_templates('pdf');
        $this->layout->set('pdf_invoice_templates', $pdf_invoice_templates);

        $this->load->model('custom_fields/mdl_custom_fields');
        $client_custom_fields = $this->mdl_custom_fields->by_table('ip_client_custom')->get()->result();
        $this->layout->set('client_custom_fields', $client_custom_fields);

        $this->layout->buffer('content', 'invoice_groups/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_invoice_groups->delete($id);
        redirect('invoice_groups');
    }

}
