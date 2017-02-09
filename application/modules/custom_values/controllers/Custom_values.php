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
 * Class Custom_Values
 */
class Custom_Values extends Admin_Controller
{
    /**
     * Custom_Values constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_custom_values');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_custom_values->paginate(site_url('custom_values/index'), $page);
        $custom_values = $this->mdl_custom_values->get_grouped()->result();

        $this->layout->set('custom_values', $custom_values);
        $this->layout->buffer('content', 'custom_values/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function field($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('custom_values');
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $field = $this->mdl_custom_fields->get_by_id($id);
        $result = $this->mdl_custom_values->get_by_fid($id)->result();

        $custom_field_types = $this->mdl_custom_fields->custom_types();

        $this->layout->set('elements', $result);
        $this->layout->set('field', $field);
        $this->layout->set('id', $id);
        $this->layout->set('custom_values_types', $custom_field_types);
        $this->layout->buffer('content', 'custom_values/field');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function edit($id = null)
    {
        $this->layout->set('id', $id);

        $value = $this->mdl_custom_values->get_by_id($id)->row();
        $fid = $value->custom_field_id;

        if ($this->input->post('btn_cancel')) {
            redirect('custom_values/field/' . $fid);
        }

        if ($this->mdl_custom_values->run_validation()) {
            $this->mdl_custom_values->save($id);
            redirect('custom_values/field/' . $fid);
        }

        $this->layout->set('fid', $fid);
        $this->layout->set('value', $value);
        $this->layout->buffer('content', 'custom_values/edit');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function create($id = null)
    {
        if (!$id) {
            redirect('custom_values');
        }

        $fid = $id;

        if ($this->input->post('btn_cancel')) {
            redirect('custom_values/field/' . $fid);
        }

        if ($this->mdl_custom_values->run_validation()) {
            $this->mdl_custom_values->save_custom($fid);
            redirect('custom_values/field/' . $fid);
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $field = $this->mdl_custom_fields->get_by_id($id);
        $this->layout->set('id', $id);
        $this->layout->set('field', $field);
        $this->layout->buffer('content', 'custom_values/new');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->mdl_custom_values->delete($id);
        redirect('custom_values');
    }

}
