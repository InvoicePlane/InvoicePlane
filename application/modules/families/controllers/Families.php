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

class Families extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_families');
    }

    public function index($page = 0)
    {
        $this->mdl_families->paginate(site_url('families/index'), $page);
        $families = $this->mdl_families->result();

        $this->layout->set('families', $families);
        $this->layout->buffer('content', 'families/index');
        $this->layout->render();
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('families');
        }

        if ($this->input->post('is_update') == 0 && $this->input->post('family_name') != '') {
            $check = $this->db->get_where('ip_families', array('family_name' => $this->input->post('family_name')))->result();
            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('family_already_exists'));
                redirect('families/form');
            }
        }

        if ($this->mdl_families->run_validation()) {
            $this->mdl_families->save($id);
            redirect('families');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_families->prep_form($id)) {
                show_404();
            }
            $this->mdl_families->set_form_value('is_update', true);
        }

        $this->layout->buffer('content', 'families/form');
        $this->layout->render();
    }

    public function delete($id)
    {
        $this->mdl_families->delete($id);
        redirect('families');
    }

}
