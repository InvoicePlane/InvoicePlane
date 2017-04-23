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
 * Class Mdl_Units
 */
class Mdl_Units extends Response_Model
{
    public $table = 'ip_units';
    public $primary_key = 'ip_units.unit_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_units.unit_name');
    }

    /**
     * Return either the singular unit name or the plural unit name,
     * depending on the quantity
     *
     * @param $unit_id
     * @param $quantity
     * @return mixed
     */
    public function get_name($unit_id, $quantity)
    {
        if ($unit_id) {
            $units = $this->get()->result();
            foreach ($units as $unit) {
                if ($unit->unit_id == $unit_id) {
                    if ($quantity > 1)
                        return $unit->unit_name_plrl;
                    else
                        return $unit->unit_name;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'unit_name' => array(
                'field' => 'unit_name',
                'label' => trans('unit_name'),
                'rules' => 'required'
            ),
            'unit_name_plrl' => array(
                'field' => 'unit_name_plrl',
                'label' => trans('unit_name_plrl'),
                'rules' => 'required'
            )
        );
    }

}
