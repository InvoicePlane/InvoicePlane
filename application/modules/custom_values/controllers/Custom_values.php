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

class Custom_Values extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_custom_values');
    }

    public function index($page = 0)
    {
        $this->mdl_custom_values->paginate(site_url('custom_values/index'), $page);
        $custom_values = $this->mdl_custom_values->get_grouped()->result();

        $this->layout->set('custom_values', $custom_values);
        $this->layout->buffer('content', 'custom_values/index');
        $this->layout->render();
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('custom_values');
        }

        if ($this->mdl_custom_values->run_validation()) {
            $this->mdl_custom_values->save($id);
            redirect('custom_values');
        }

        /*if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_custom_values->prep_form($id)) {
                show_404();
            }
        }*/

        $this->load->model('custom_fields/mdl_custom_fields');
        $field = $this->mdl_custom_fields->get_by_id($id);
        $result = $this->mdl_custom_values->get_by_fid($id)->result();

        $custom_field_types = $this->mdl_custom_fields->custom_types();

        $this->layout->set('elements', $result);
        $this->layout->set('field', $field);
        $this->layout->set('id', $id);
        $this->layout->set('custom_values_types', $custom_field_types);
        $this->layout->buffer('content', 'custom_values/form');
        $this->layout->render();
    }

    public function new($id = null){
      $this->layout->set('id', $id);
      $this->layout->buffer('content', 'custom_values/new');
      $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_custom_values->delete($id);
        redirect('custom_values');
    }

}
