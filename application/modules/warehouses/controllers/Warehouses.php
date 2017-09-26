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
 * Class Projects
 */
class Warehouses extends Admin_Controller
{
    /**
     * Projects constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_warehouses');
    }

    /**
     * @param int $page
     */
    public function index($page = 0)
    {
        $this->mdl_warehouses->paginate(site_url('warehouses/index'), $page);
        $warehouses = $this->mdl_warehouses->result();
        
        for ($i = 0; $i < count($warehouses); $i++) {
            $warehouses[$i]->total_products = $this->mdl_warehouses->get_products_warehouse($warehouses[$i]->warehouse_id);
        }

        $this->layout->set('warehouses', $warehouses);
        $this->layout->buffer('content', 'warehouses/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('warehouses');
        }

        if ($this->mdl_warehouses->run_validation()) {
            $this->mdl_warehouses->save($id);
            redirect('warehouses');
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_warehouses->prep_form($id)) {
                show_404();
            }
        }

        $this->layout->buffer('content', 'warehouses/form');
        $this->layout->render();
    }

    /**
     * @param null $warehouse_id
     */
    public function view($warehouse_id)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('warehouses');
        }

        $this->load->model('warehouses/mdl_warehouses');
        $warehouse = $this->mdl_warehouses->get_by_id($warehouse_id);

        if (!$warehouse) {
            show_404();
        }

        $this->layout->set(array(
            'warehouse' => $warehouse,
            'products' => $this->mdl_warehouses->get_products_warehouse($warehouse->warehouse_id)
        ));
        $this->layout->buffer('content', 'warehouses/view');
        $this->layout->render();
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        $this->load->model('tasks/mdl_tasks');
        $this->mdl_tasks->update_on_project_delete($id);

        $this->mdl_projects->delete($id);
        redirect('projects');
    }

}
