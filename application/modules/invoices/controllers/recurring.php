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

class Recurring extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_invoices_recurring');
    }

    public function index($page = 0)
    {
        $this->mdl_invoices_recurring->paginate(site_url('invoices/recurring'), $page);
        $recurring_invoices = $this->mdl_invoices_recurring->result();

        $this->layout->set('recur_frequencies', $this->mdl_invoices_recurring->recur_frequencies);
        $this->layout->set('recurring_invoices', $recurring_invoices);
        $this->layout->buffer('content', 'invoices/index_recurring');
        $this->layout->render();
    }

    public function stop($invoice_recurring_id)
    {
        $this->mdl_invoices_recurring->stop($invoice_recurring_id);
        redirect('invoices/recurring/index');
    }

    public function delete($invoice_recurring_id)
    {
        $this->mdl_invoices_recurring->delete($invoice_recurring_id);
        redirect('invoices/recurring/index');
    }

}
