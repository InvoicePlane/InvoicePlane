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

class Mdl_Item_Lookups extends MY_Model
{
    public $table = 'ip_item_lookups';
    public $primary_key = 'ip_item_lookups.item_lookup_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_item_lookups.item_name');
    }

    public function validation_rules()
    {
        return array(
            'item_name' => array(
                'field' => 'item_name',
                'label' => trans('item_name'),
                'rules' => 'required'
            ),
            'item_description' => array(
                'field' => 'item_description',
                'label' => trans('description')
            ),
            'item_price' => array(
                'field' => 'item_price',
                'label' => trans('price'),
                'rules' => 'required'
            )
        );
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['item_price'] = standardize_amount($db_array['item_price']);

        return $db_array;
    }

    public function prep_form($id = null)
    {
        $return = false;

        if ($id) {
            $return = parent::prep_form($id);

            $this->set_form_value('item_price', format_amount($this->form_value('item_price')));
        }
        return $return;
    }

}
