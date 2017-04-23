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
 * Class Mdl_Quote_Custom
 */
class Mdl_Quote_Custom extends Validator
{
    public static $positions = array(
        'custom_fields',
        'properties'
    );
    public $table = 'ip_quote_custom';
    public $primary_key = 'ip_quote_custom.quote_custom_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_quote_custom.*, ip_custom_fields.*', false);
    }

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_quote_custom.quote_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }


    /**
     * @param $quote_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($quote_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $form_data = isset($this->_formdata) ? $this->_formdata : null;

            if (is_null($form_data)) {
                return true;
            }

            $quote_custom_id = null;

            foreach ($form_data as $key => $value) {
                $db_array = array(
                    'quote_id' => $quote_id,
                    'quote_custom_fieldid' => $key,
                    'quote_custom_fieldvalue' => $value
                );

                $quote_custom = $this->where('quote_id', $quote_id)->where('quote_custom_fieldid', $key)->get();

                if ($quote_custom->num_rows()) {
                    $quote_custom_id = $quote_custom->row()->quote_custom_id;
                }

                parent::save($quote_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    public function by_id($quote_id)
    {
        $this->db->where('ip_quote_custom.quote_id', $quote_id);
        return $this;
    }

}
