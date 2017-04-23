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
 * Class Custom_Fields
 */
class Custom_Fields extends Admin_Controller
{
    /**
     * Custom_Fields constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_custom_fields');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_custom_fields->paginate(site_url('custom_fields/index'), $page);
        $custom_fields = $this->mdl_custom_fields->result();

        $this->load->model('custom_values/mdl_custom_values');
        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->set('custom_tables', $this->mdl_custom_fields->custom_tables());
        $this->layout->set('custom_value_fields', $this->mdl_custom_values->custom_value_fields());
        $this->layout->buffer('content', 'custom_fields/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('custom_fields');
        }

        if ($this->mdl_custom_fields->run_validation()) {
            $this->mdl_custom_fields->save($id);
            redirect('custom_fields');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_custom_fields->prep_form($id)) {
                show_404();
            }
        }

        $this->load->model('mdl_client_custom');
        $this->load->model('mdl_invoice_custom');
        $this->load->model('mdl_payment_custom');
        $this->load->model('mdl_quote_custom');
        $this->load->model('mdl_user_custom');

        $this->layout->set('custom_field_tables', $this->mdl_custom_fields->custom_tables());
        $this->layout->set('custom_field_types', $this->mdl_custom_fields->custom_types());
        $this->layout->buffer('content', 'custom_fields/form');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_custom_fields->delete($id);
        redirect('custom_fields');
    }

}
