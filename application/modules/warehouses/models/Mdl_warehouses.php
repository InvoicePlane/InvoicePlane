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
 * Class Mdl_Projects
 */
class Mdl_Warehouses extends Response_Model
{
    public $table = 'ip_warehouses';
    public $primary_key = 'ip_warehouses.warehouse_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_warehouses.warehouse_id');
    }

    public function get_latest()
    {
        $this->db->order_by('ip_warehouses.warehouse_id', 'DESC');
        return $this;
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'warehouse_name' => array(
                'field' => 'warehouse_name',
                'label' => trans('warehouse_name'),
                'rules' => 'required'
            ),
            'warehouse_location' => array(
                'field' => 'warehouse_location',
                'label' => trans('warehouse_location')
            ),
        );
    }

    public function get_products_warehouse($warehouse_id)
    {
        $result = array();

        if (!$warehouse_id) {
            return $result;
        }

        $this->load->model('products/mdl_products');
        $query = $this->mdl_products
            ->where('ip_products.warehouse_id', $warehouse_id)
            ->get();

        foreach ($query->result() as $row) {
            $result[] = $row;
        }

        return $result;
    }

}
