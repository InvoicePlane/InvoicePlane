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
 * Class Validator
 */
class Validator extends MY_Model
{
    public function validate_text()
    {
        return true;
    }

    /**
     * @param $value
     * @return bool|null
     */
    public function validate_date($value)
    {
        if ($value == "") {
            return null;
        }

        if (!is_date($value)) {
            $this->form_validation->set_message('validate_date', 'Invalid date');
            return false;
        }

        return true;
    }

    /**
     * @param $value
     * @return bool|null
     */
    public function validate_boolean($value)
    {
        if ($value == "0" || $value == "1") {
            return true;
        }

        if ($value == "") {
            return null;
        }

        return false;
    }

    /**
     * @param $value
     * @param $key
     * @return null
     */
    public function validate_singlechoice($value, $key)
    {
        if ($value == "") {
            return null;
        }

        $this->load->model('custom_values/mdl_custom_values', 'custom_value');
        $result = $this->custom_value->column_has_value($key, $value);

        return $result;
    }

    /**
     * @param $value
     * @param $key
     * @return bool|null
     */
    public function validate_multiplechoice($value, $id)
    {
        if ($value == "") {
            return null;
        }

        $this->load->model('custom_values/mdl_custom_values', 'custom_value');
        $this->custom_value->where('custom_field_id', $id);
        $dbvals = $this->custom_value->where_in('custom_values_id', $value)->get();

        if ($dbvals->num_rows() == sizeof($value)) {
            return true;
        }

        return false;
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
            )
        );
    }

    /**
     * @param $column
     * @return null
     */
    public function get_field_type($column)
    {
        $this->load->model('custom_values/mdl_custom_fields', 'cf');
        $el = $this->cf->get_by_column($column)->row();

        if ($el == null) {
            return null;
        }

        return $el->custom_field_type;
    }

    /**
     * @param $array
     * @return bool|string
     */
    public function validate($array)
    {
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');

        $db_array = $array;
        $errors = [];

        if (empty($db_array)) {
            // Return true if no fields need to be validated
            return true;
        }

        foreach ($db_array as $key => $value) {
            $model = $this->mdl_custom_fields->where('custom_field_id', $key)->get();
            if ($model->num_rows()) {
                $model = $model->row();
                if (@$model->custom_field_required == "1") {
                    if ($value == "") {
                        $errors[] = array(
                            "field" => $model->custom_field_id,
                            "label" => $model->custom_field_label,
                            "error_msg" => "missing field required"
                        );
                        continue;
                    }
                }
                $result = $this->validate_type($model->custom_field_type, $value, $key);

                if ($result === false) {
                    $errors[] = array(
                        "field" => $model->custom_field_id,
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

    /**
     * @param $type
     * @param $value
     * @param $key
     * @return mixed
     */
    public function validate_type($type, $value, $key)
    {
        $nicename = $this->mdl_custom_fields->get_nicename(
            $type
        );

        $validation_rule = 'validate_' . $nicename;
        return $this->{$validation_rule}($value, $key);
    }

    public function fixinput()
    {
        foreach ($this->_formdata as $key => $value) {
            $model = $this->mdl_custom_fields->where('custom_field_id', $key)->get();

            if ($model->num_rows()) {
                $model = $model->row();
                $ftype = $model->custom_field_type;

                switch ($ftype) {
                    case "DATE":
                        if ($value == "") {
                            $this->_formdata[$key] = null;
                        } else {
                            $this->_formdata[$key] = date_to_mysql($value);
                        }

                        break;

                    case "MULTIPLE-CHOICE":
                        $this->_formdata[$key] = implode(",", $value);
                        break;

                    case "TEXT":
                        if ($value == "") {
                            $this->_formdata[$key] = null;
                        }
                        break;
                }
            }
        }
    }

    /**
     * @param $errors
     * @return string
     */
    public function create_error_text($errors)
    {
        $string = [];

        foreach ($errors as $error) {
            $string[] = sprintf(lang('validator_fail'), $error['label'], $error['error_msg']);
        }

        return nl2br(implode("\n", $string));
    }
}
