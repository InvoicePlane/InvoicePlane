<?php

if (! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
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
        $this->mdl_custom_values->grouped()->paginate(site_url('custom_values/index'), $page);
        $custom_values = $this->mdl_custom_values->result();

        $this->layout->set(
            [
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_custom_values'),
                'filter_method'      => 'filter_custom_values',
                'custom_values'      => $custom_values,
            ]
        );
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

        $this->layout->set(
            [
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_custom_values'),
                'filter_method'      => 'filter_custom_values_field',
                'id'                 => $id,
                'field'              => $field,
                'elements'           => $result,
                'custom_field_usage' => $this->mdl_custom_fields->used($id),
            ]
        );
        $this->layout->buffer('content', 'custom_values/field');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function edit($id = null)
    {
        $value = $this->mdl_custom_values->get_by_id($id)->row();
        $fid = $value->custom_field_id;

        if ($this->input->post('btn_cancel')) {
            redirect('custom_values/field/' . $fid);
        }

        if ($this->mdl_custom_values->run_validation()) {
            $this->mdl_custom_values->save($id);
            redirect('custom_values/field/' . $fid);
        }

        $this->layout->set(
           [
              'id'                 => $id,
              'fid'                => $fid,
              'value'              => $value,
              'custom_field_usage' => $this->mdl_custom_values->used($id),
           ]
        );
        $this->layout->buffer('content', 'custom_values/edit');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function create($id = null)
    {
        if (! $id) {
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
        $this->layout->set(['id' => $id, 'field' => $field]);
        $this->layout->buffer('content', 'custom_values/new');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        if ( ! $this->mdl_custom_values->delete($id))
        {
            $this->session->set_flashdata('alert_info', trans('id') . " \"{$id}\" " . trans('custom_values_used_not_deletable'));
        }

        $fid = $this->input->post('custom_field_id');
        redirect('custom_values' . ($fid ? '/field/' . $fid : ''));
    }

}
