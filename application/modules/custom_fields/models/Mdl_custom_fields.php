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
 * Class Mdl_Custom_Fields
 */
class Mdl_Custom_Fields extends MY_Model
{
    public $table = 'ip_custom_fields';
    public $primary_key = 'ip_custom_fields.custom_field_id';

    /**
     * @param $element
     * @return string
     */
    public static function get_nicename($element)
    {
        if (in_array($element, Mdl_Custom_Fields::custom_types())) {
            return strtolower(str_replace('-', '', $element));
        }
        return 'fallback';
    }

    /**
     * @return array
     */
    public static function custom_types()
    {
        $CI = &get_instance();
        $CI->load->module("custom_values/mdl_custom_values");
        return Mdl_Custom_Values::custom_types();
    }

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_custom_fields.*', false);
    }

    public function default_order_by()
    {
        $this->db->order_by('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'custom_field_table' => array(
                'field' => 'custom_field_table',
                'label' => trans('table'),
                'rules' => 'required'
            ),
            'custom_field_label' => array(
                'field' => 'custom_field_label',
                'label' => trans('label'),
                'rules' => 'required|max_length[50]'
            ),
            'custom_field_type' => array(
                'field' => 'custom_field_type',
                'label' => trans('type'),
                'rules' => 'required'
            ),
            'custom_field_order' => array(
                'field' => 'custom_field_order',
                'label' => trans('order'),
                'rules' => 'is_natural'
            ),
            'custom_field_location' => array(
                'field' => 'custom_field_location',
                'label' => trans('position'),
                'rules' => 'is_natural'
            )
        );
    }

    /**
     * @param $table
     * @return $this
     */
    public function get_by_table($table)
    {
        $this->where('custom_field_table', $table);
        return $this->get()->result();
    }

    /**
     * @param null $id
     * @param null $db_array
     * @return null
     */
    public function save($id = null, $db_array = null)
    {
        if ($id) {
            // Get the original record before saving
            $original_record = $this->get_by_id($id);
        }

        // Create the record
        $db_array = ($db_array) ? $db_array : $this->db_array();

        // Save the record to ip_custom_fields
        $id = parent::save($id, $db_array);
        return $id;
    }

    /**
     * @param $column
     * @return $this
     */
    public function get_by_id($column)
    {
        $this->where('custom_field_id', $column);
        return $this->get()->row();
    }

    /**
     * @return array
     */
    public function db_array()
    {
        // Get the default db array
        $db_array = parent::db_array();

        // Get the array of custom tables
        $custom_tables = $this->custom_tables();

        // Check if the user wants to add 'id' as custom field
        if (strtolower($db_array['custom_field_label']) == 'id') {
            // Replace 'id' with 'field_id' to avoid problems with the primary key
            $custom_field_label = 'field_id';
        } else {
            $custom_field_label = strtolower(str_replace(' ', '_', $db_array['custom_field_label']));
        }

        if (in_array($db_array['custom_field_type'], $this->custom_types())) {
            $type = $db_array['custom_field_type'];
        } else {
            $type = $this->custom_types()[0];
        }

        // Create the name for the custom field column
        $this->load->helper('diacritics');

        $clean_name = preg_replace('/[^a-z0-9_\s]/', '', strtolower(diacritics_remove_diacritics($custom_field_label)));

        $db_array['custom_field_type'] = $type;

        // Return the db array
        return $db_array;
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
     */
    public function delete($id)
    {
        $custom_field = $this->get_by_id($id);
        parent::delete($id);
    }

    /**
     * @param $table
     * @return $this
     */
    public function by_table($table)
    {
        $this->filter_where('custom_field_table', $table);
        return $this;
    }

    /**
     * @param integer $field_id
     * @param string $custom_field_model
     * @param integer $model_id
     * @return string
     */
    public function get_value_for_field($field_id, $custom_field_model, $model_id)
    {
        $this->load->model('custom_fields/' . $custom_field_model);

        $cf_table = str_replace('mdl_', '', $custom_field_model);
        $cf_model_name = str_replace('_custom', '', $cf_table);

        $value = $this->$custom_field_model
            ->where($cf_table . '_fieldid', $field_id)
            ->where($cf_model_name . '_id', $model_id)
            ->get()->result();

        if (empty($value)) {
            return '';
        }

        $value_key = $cf_table . '_fieldvalue';
        return isset($value->$value_key) ? $value->$value_key : '';
    }

    /**
     * @param string $custom_field_model
     * @param integer $model_id
     * @return array
     */
    public function get_values_for_fields($custom_field_model, $model_id)
    {
        $this->load->model('custom_fields/' . $custom_field_model);

        $fields = $this->$custom_field_model->by_id($model_id)->get()->result();

        if (empty($fields)) {
            return array();
        }

        $this->load->model('custom_values/mdl_custom_values');

        $values = array();
        $custom_field = str_replace('mdl_', '', $custom_field_model);

        foreach ($fields as $field) {
            // Get the custom field value
            $field_id_fieldid = $custom_field . '_fieldid';
            $field_id_fieldlabel = $custom_field . '_fieldvalue';
            $custom_value = $this->mdl_custom_values->get_by_id($field->$field_id_fieldid);

            if ($custom_value->num_rows() > 0) {
                $custom_value = $custom_value->result()[0];
                $field->custom_value_label = $custom_value->custom_values_value;
            }

            $values[$field->custom_field_label] = $field->$field_id_fieldlabel;
        }

        return $values;
    }

    /**
     * @param string $table_name
     * @param string $old_column_name
     * @param string $new_column_name
     */
    private function rename_column($table_name, $old_column_name, $new_column_name)
    {
        $this->load->dbforge();

        $column = array(
            $old_column_name => array(
                'name' => $new_column_name,
                'type' => 'VARCHAR',
                'constraint' => 50
            )
        );

        $this->dbforge->modify_column($table_name, $column);
    }

    /**
     * @param string $table_name
     * @param string $column_name
     */
    private function add_column($table_name, $column_name)
    {
        $this->load->dbforge();

        $column = array(
            $column_name => array(
                'type' => 'VARCHAR',
                'constraint' => 256
            )
        );

        $this->dbforge->add_column($table_name, $column);
    }

}
