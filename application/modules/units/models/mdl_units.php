<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Cornelius Kölbel <cornelius.koelbel@netknights.it>
 * @copyright	Copyright (c) 2017 Cornelius Kölbel
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
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
