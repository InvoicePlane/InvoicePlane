<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Invoice_Groups
 */
class Invoice_Groups extends Admin_Controller
{
    /**
     * Invoice_Groups constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_invoice_groups');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_invoice_groups->paginate(site_url('invoice_groups/index'), $page);
        $invoice_groups = $this->mdl_invoice_groups->result();

        $this->layout->set('invoice_groups', $invoice_groups);
        $this->layout->buffer('content', 'invoice_groups/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
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

        $this->layout->buffer('content', 'invoice_groups/form');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_invoice_groups->delete($id);
        redirect('invoice_groups');
    }

}
