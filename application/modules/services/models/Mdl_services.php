<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright		Copyright (c) 2012 - 2024 InvoicePlane.com
 * @license             https://invoiceplane.com/license.txt
 * @link                https://invoiceplane.com
 */

/**
 * Class Mdl_Services
 */
class Mdl_Services extends Response_Model
{
    public $table = 'ip_services';
    public $primary_key = 'ip_services.service_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_services.service_name');
    }

    public function get_names_by_ids(array $serviceIds)
    {
        $services = $this->db
                         ->select('service_id, service_name')
                         ->where_in('service_id', $serviceIds)
                         ->order_by('service_name')
                         ->get($this->table)
                         ->result_array();

        return array_column($services, 'service_name', 'service_id');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'service_name' => array(
                'field' => 'service_name',
                'label' => trans('service_name'),
                'rules' => 'required'
            ),
        );
    }

}
