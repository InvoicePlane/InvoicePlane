<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

class Validator extends MY_Model
{
    public function validate_type($type, $value, $key)
    {
        $nicename = $this->mdl_custom_fields->get_nicename(
          $type
        );
        $validation_rule = 'validate_'.$nicename;
        return $this->{$validation_rule}($value, $key);
    }

    public function validate_text()
    {
      return true;
    }

    public function validate_date($value)
    {
        if (!is_date($value)) {
            $this->form_validation->set_message('validate_date', 'Invalid date');
            return false;
        }
        return true;
    }

    public function validate_boolean($value)
    {
      if($value == "0" || $value == "1")
      {
        return true;
      }
      return false;
    }

    public function validate_singlechoice($value, $key)
    {
        $this->load->model('custom_values/mdl_custom_values', 'custom_value');
        $result = $this->custom_value->column_has_value($key, $value);
        return $result;
    }

    public function validate_multiplechoice($value, $key)
    {
        $this->load->model('custom_values/mdl_custom_values', 'custom_value');
        $this->custom_value->where('custom_field_column', $key);
        $dbvals = $this->custom_value->where_in('custom_values_id', $value)->get();
        if($dbvals->num_rows() == sizeof($value))
        {
          return true;
        }
        return false;
    }

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
            )
        );
    }

    public function get_field_type($column)
    {
        $this->load->model('custom_values/mdl_custom_fields', 'cf');
        $el = $this->cf->get_by_column($column)->row();
        if ($el == null) {
            return null;
        }
        return $el->custom_field_type;
    }

    public function fixinput()
    {
        foreach ($this->_formdata as $key => $value) {
            $model = $this->mdl_custom_fields->where('custom_field_column', $key)->get();
            if ($model->num_rows()) {
                $model = $model->row();
                $ftype = $model->custom_field_type;

                switch($ftype){
                  case "DATE":
                    $this->_formdata[$key] = date_to_mysql($value);
                  break;

                  case "MULTIPLE-CHOICE":
                    $this->_formdata[$key] = implode(",", $value);
                  break;

                  case "TEXT":
                    if($value == ""){
                      $this->_formdata[$key] = null;
                    }
                  break;
                }
            }
        }
    }

    public function create_error_text($errors)
    {
        $string = [];
        foreach ($errors as $error) {
            $string[] = sprintf(lang('validator_fail'), $error['label'], $error['error_msg']);
        }
        return nl2br(implode("\n", $string));
    }

    public function validate($array)
    {
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');

        $db_array = $array;
        $errors = [];
        foreach ($db_array as $key => $value) {
            $model = $this->mdl_custom_fields->where('custom_field_column', $key)->get();
            if ($model->num_rows()) {
                $model = $model->row();
                if (@$model->custom_field_required == "1") {
                    if ($value == "") {
                        $errors[] = array(
                        "field" => $model->custom_field_column,
                        "label" => $model->custom_field_label,
                        "error_msg" => "missing field required"
                      );
                        continue;
                    }
                }

                $result = $this->validate_type($model->custom_field_type, $value, $key);

                if ($result == false) {
                    $errors[] = array(
                  "field" => $model->custom_field_column,
                  "label" => $model->custom_field_label,
                  "error_msg" => "invalid input"
                );
                }
            }
        }
        if (sizeof($errors) == 0) {
            $this->_formdata = $db_array;
            $this->fixinput();
            return true;
        }
        return $this->create_error_text($errors);
    }
}
