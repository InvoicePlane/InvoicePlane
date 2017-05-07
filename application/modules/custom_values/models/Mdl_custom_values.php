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
 * Class Mdl_Custom_Values
 */
class Mdl_Custom_Values extends MY_Model
{
    public $table = 'ip_custom_values';
    public $primary_key = 'ip_custom_values.custom_values_id';

    /**
     * @return string[]
     */
    public static function custom_types()
    {
        return array_merge(Mdl_Custom_Values::user_input_types(), Mdl_Custom_Values::custom_value_fields());
    }

    /**
     * @return string[]
     */
    public static function user_input_types()
    {
        return array(
            'TEXT',
            'DATE',
            'BOOLEAN'
        );
    }

    /**
     * @return string[]
     */
    public static function custom_value_fields()
    {
        return array(
            'SINGLE-CHOICE',
            'MULTIPLE-CHOICE'
        );
    }

    /**
     * @param $fid
     */
    public function save_custom($fid)
    {
        $field_id = null;

        $this->load->module('custom_fields');
        $field_custom = $this->mdl_custom_fields->get_by_id($fid);

        if (!$field_custom) {
            return;
        }

        $db_array = $this->db_array();
        $db_array['custom_values_field'] = $fid;

        parent::save(null, $db_array);
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'custom_values_value' => array(
                'field' => 'custom_values_value',
                'label' => 'Value',
                'rules' => 'required'
            )
        );
    }

    /**
     * @return array
     */
    public function custom_tables()
    {
        return array(
            'ip_client_custom' => 'client',
            'ip_invoice_custom' => 'invoice',
            'ip_payment_custom' => 'payment',
            'ip_quote_custom' => 'quote',
            'ip_user_custom' => 'user'
        );
    }

    /**
     * @param $id
     * @return $this
     */
    public function get_by_fid($id)
    {
        $this->where('custom_values_field', $id);
        return $this->get();
    }

    /**
     * @param $column
     * @return $this
     */
    public function get_by_column($id)
    {
        $this->where('custom_field_id', $id);
        return $this->get();
    }

    /**
     * @param $id
     * @return mixed
     */
    public function get_by_id($id)
    {
        return $this->where('custom_values_id', $id)->get();
    }

    /**
     * @param $column
     * @param $id
     * @return bool
     */
    public function column_has_value($fid, $id)
    {
        $this->where('custom_field_id', $fid);
        $this->where('custom_values_id', $id);
        $this->get();
        if ($this->num_rows()) {
            return true;
        }
        return false;
    }

    /**
     * @return $this
     */
    public function get_grouped()
    {
        $this->db->select('count(custom_field_label) as count');
        $this->db->group_by('ip_custom_fields.custom_field_id');
        return $this->get();
    }

    public function default_select()
    {
        $this->db->select('ip_custom_fields.*,ip_custom_values.*', false);
    }

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_custom_values.custom_values_field = ip_custom_fields.custom_field_id', 'inner');
    }

    public function default_order_by()
    {
        //$this->db->group_by('ip_custom_fields.custom_field_label');
    }

    public function default_group_by()
    {
        //$this->db->group_by('ip_custom_values.custom_values_field');
    }
}
