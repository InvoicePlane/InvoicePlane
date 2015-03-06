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

class Custom_Fields extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_custom_fields');
    }

    public function index($page = 0)
    {
        $this->mdl_custom_fields->paginate(site_url('custom_fields/index'), $page);
        $custom_fields = $this->mdl_custom_fields->result();

        $this->layout->set('custom_fields', $custom_fields);
        $this->layout->buffer('content', 'custom_fields/index');
        $this->layout->render();
    }

    public function form($id = NULL)
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

        $this->layout->set('custom_field_tables', $this->mdl_custom_fields->custom_tables());
        $this->layout->buffer('content', 'custom_fields/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_custom_fields->delete($id);
        redirect('custom_fields');
    }

}
