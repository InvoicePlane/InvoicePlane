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
 * Class Mdl_Families
 */
class Mdl_Families extends Response_Model
{
    public $table = 'ip_families';
    public $primary_key = 'ip_families.family_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_families.family_name');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'family_name' => array(
                'field' => 'family_name',
                'label' => trans('family_name'),
                'rules' => 'required'
            )
        );
    }

}
