<?php

if (! defined('BASEPATH')) {
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
class Mdl_Custom_Values extends MY_Model
{
    public $table = 'ip_custom_values';

    public $primary_key = 'ip_custom_values.custom_values_id';

    /**
     * @return array
     */
    public static function custom_types()
    {
        return array_merge(self::user_input_types(), self::custom_value_fields());
    }

    /**
     * @return array
     */
    public static function user_input_types()
    {
        return [
            'TEXT',
            'DATE',
            'BOOLEAN'
        ];
    }

    /**
     * @return array
     */
    public static function custom_value_fields()
    {
        return [
            'SINGLE-CHOICE',
            'MULTIPLE-CHOICE'
        ];
    }

    /**
     * @param $fid
     */
    public function save_custom($fid)
    {
        $this->load->module('custom_fields');
        $field_custom = $this->mdl_custom_fields->get_by_id($fid);

        if (! $field_custom) {
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
        return [
            'custom_values_value' => [
                'field' => 'custom_values_value',
                'label' => 'Value',
                'rules' => 'required'
            ]
        ];
    }

    /**
     * @return array
     */
    public function custom_tables()
    {
        return [
            'ip_client_custom'  => 'client',
            'ip_invoice_custom' => 'invoice',
            'ip_payment_custom' => 'payment',
            'ip_quote_custom'   => 'quote',
            'ip_user_custom'    => 'user'
        ];
    }

    /**
     * @param int $id
     * @param bool $get
     * @return null|object
     */
    public function used($id = null, $get = true)
    {
        if (! $id) {
            return null;
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $cv = $this->get_by_id($id)->row();
        $cf = $this->mdl_custom_fields->get_by_id($cv->custom_values_field);
        unset($cv);
        $base = strtr($cf->custom_field_table, ['ip_' => '']) . '_fieldvalue';

        // Get values [SINGLE|MULTIPLE]-CHOICE
        $this->db->from($cf->custom_field_table);
        if ('SINGLE-CHOICE' == $cf->custom_field_type) {
            $this->db->where($base, $id);
        } else {
            $this->db->or_like($base, $id . ',')
                     ->or_like($base, ',' . $id)
                     ->or_where($base, $id);
        }

        return $get ? $this->db->get()->result() : $this->db;
    }

    /**
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        if (! $this->used($id)) {
            parent::delete($id);
            return true;
        }

        return false;
    }

    /**
     * @param $id
     */
    public function delete_all_fid($id)
    {
        $this->db->where('custom_values_field', $id)->delete($this->table);
    }

    /**
     * @param $id
     * @return $this
     */
    public function get_by_fid($id)
    {
        return $this->where('custom_values_field', $id)->get();
    }

    /**
     * @param $id
     * @return $this
     */
    public function get_by_column($id)
    {
        return $this->where('custom_field_id', $id)->get();
    }

    /**
     * @param $id
     * @return $this
     */
    public function get_by_id($id)
    {
        return $this->where('custom_values_id', $id)->get();
    }

    /**
     * @param $ids
     * @return null|object
     */
    public function get_by_ids($ids)
    {
        if (empty($ids)) {
            return null;
        }

        $ids = is_array($ids) ? $ids : explode(',', $ids);
        return $this->where_in('custom_values_id', $ids)->get();
    }

    /**
     * @param $fid
     * @param $id
     * @return bool
     */
    public function column_has_value($fid, $id)
    {
        $this->where('custom_field_id', $fid);
        $this->where('custom_values_id', $id);
        $this->get();
        return boolval($this->num_rows());
    }

    /**
     * @return $this
     */
    public function grouped()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_custom_fields.*,ip_custom_values.*', false);
        $this->db->select('count(custom_field_label) as count');
        $this->db->group_by('ip_custom_fields.custom_field_id');
        return $this;
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
        $this->db->order_by('ip_custom_values.custom_values_value');
    }

    public function default_group_by()
    {
        //$this->db->group_by('ip_custom_values.custom_values_field');
    }
}
