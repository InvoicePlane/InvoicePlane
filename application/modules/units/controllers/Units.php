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
 * Class Units
 */
class Units extends Admin_Controller
{
    /**
     * Units constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_units');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_units->paginate(site_url('units/index'), $page);
        $units = $this->mdl_units->result();

        $this->layout->set('units', $units);
        $this->layout->buffer('content', 'units/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('units');
        }

        if ($this->input->post('is_update') == 0
            && $this->input->post('unit_name') != ''
            && $this->input->post('unit_name_plrl') != ''
        ) {
            $check = $this->db->get_where('ip_units', array('unit_name' => $this->input->post('unit_name')))->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('unit_already_exists'));
                redirect('units/form');
            }
        }

        if ($this->mdl_units->run_validation()) {
            $this->mdl_units->save($id);
            redirect('units');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_units->prep_form($id)) {
                show_404();
            }

            $this->mdl_units->set_form_value('is_update', true);
        }

        $this->layout->buffer('content', 'units/form');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_units->delete($id);
        redirect('units');
    }

}
