<?php

if ( ! defined('BASEPATH')) {
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

    public function index(): void
    {
        // Display all custom_fields tables by default
        redirect('custom_fields/table/all');
    }

    /**
     * @param string $name of table (simple) NAME (more comprehensive) & why not a filter by type??? like I/Q payment & todo for product ;)
     * @param int    $page
     */
    public function table($name = 'all', $page = 0): void
    {
        // Determine which name of table custom field to load
        $custom_tables = $this->mdl_custom_fields->custom_tables();
        if ($name != 'all' && in_array($name, $custom_tables)) {
            $this->mdl_custom_fields->by_table_name($name);
        }

        // Paginate before result
        $this->mdl_custom_fields->paginate(site_url('custom_fields/name/' . $name), $page);
        $custom_fields = $this->mdl_custom_fields->result();

        $this->load->model('custom_values/mdl_custom_values');
        $this->layout->set(
            [
                'filter_display'     => true,
                'filter_placeholder' => trans('filter_custom_fields'),
                'filter_method'      => 'filter_custom_fields',

                'custom_fields'       => $custom_fields,
                'custom_tables'       => $custom_tables,
                'custom_value_fields' => $this->mdl_custom_values->custom_value_fields(),
                'positions'           => $this->mdl_custom_fields->get_positions(true),
            ]
        );
        $this->layout->buffer('content', 'custom_fields/index');
        $this->layout->render();
    }

    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('custom_fields');
        }

        $this->filter_input();  // <<<--- filters _POST array for nastiness

        if ($this->mdl_custom_fields->run_validation()) {
            $this->mdl_custom_fields->save($id);
            redirect('custom_fields');
        }

        if ($id && ! $this->input->post('btn_submit') && ! $this->mdl_custom_fields->prep_form($id)) {
            show_404();
        }

        $this->layout->set(
            [
                'custom_field_id'       => $id,
                'custom_field_tables'   => $this->mdl_custom_fields->custom_tables(),
                'custom_field_types'    => $this->mdl_custom_fields->custom_types(),
                'custom_field_usage'    => $this->mdl_custom_fields->used($id),
                'custom_field_location' => $this->mdl_custom_fields->form_value('custom_field_location'),
                'positions'             => $this->mdl_custom_fields->get_positions(),
            ]
        );
        $this->layout->buffer('content', 'custom_fields/form');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        if ( ! $this->mdl_custom_fields->delete($id)) {
            $this->session->set_flashdata('alert_info', trans('id') . sprintf(' "%s" ', $id) . trans('custom_fields_used_not_deletable'));
        }

        // Return to page number of custom values or fields
        $r = empty($_SERVER['HTTP_REFERER']) ? 'custom_fields' : $_SERVER['HTTP_REFERER'];
        redirect($r);
    }
}
