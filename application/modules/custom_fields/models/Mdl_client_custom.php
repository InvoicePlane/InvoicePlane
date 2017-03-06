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
 * Class Mdl_Client_Custom
 */
class Mdl_Client_Custom extends Validator
{
    public $table = 'ip_client_custom';
    public $primary_key = 'ip_client_custom.client_custom_id';

    /**
     * @param $client_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($client_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $db_array = isset($this->_formdata) ? $this->_formdata : null;
            $client_custom_id = null;
            $db_array['client_id'] = $client_id;
            $client_custom = $this->where('client_id', $client_id)->get()->row();
            $id = parent::save($client_custom->client_custom_id, $db_array);
            return true;
        }

        return $result;
    }

    /**
     * @param null $id
     * @return void
     */
    public function prep_form($id = null)
    {
        if ($id) {
            $values = $this->get_by_client($id)->row();
            $this->load->helper('custom_values_helper');
            $this->load->module('custom_fields/mdl_custom_fields', 'cf');

            if($values != null){
              foreach ($values as $key => $value) {
                  $type = $this->get_field_type($key);
                  if ($type != null) {
                      $nicename = $this->cf->get_nicename(
                          $type
                      );
                      $formatted = call_user_func("format_" . $nicename, $value);
                      $this->set_form_value($key, $formatted);
                  }
              }
            }

            parent::prep_form($id);
        }
    }

    /**
     * @param $client_id
     * @return $this
     */
    public function get_by_client($client_id)
    {
        $this->where('client_id', $client_id);
        return $this->get();
    }

    /**
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();
        $this->load->module('custom_fields/mdl_custom_fields');
        $fields = $this->mdl_custom_fields->result();

        foreach ($fields as $field) {
            if ($field->custom_field_type == "DATE") {
                $db_array[$field->custom_field_column] = date_to_mysql(
                    $db_array[$field->custom_field_column]
                );
            } elseif ($field->custom_field_type == "MULTIPLE-CHOICE") {
                $db_array[$field->custom_field_column] = implode(",", $db_array[$field->custom_field_column]);
            }
        }

        return $db_array;
    }

}
