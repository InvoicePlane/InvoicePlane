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
 * Class Mdl_Payment_Custom
 */
class Mdl_Payment_Custom extends Validator
{
    public static $positions = array(
        'custom_fields'
    );
    public $table = 'ip_payment_custom';
    public $primary_key = 'ip_payment_custom.payment_custom_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ip_payment_custom.*, ip_custom_fields.*', false);
    }

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_payment_custom.payment_custom_fieldid = ip_custom_fields.custom_field_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * @param $payment_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($payment_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $form_data = isset($this->_formdata) ? $this->_formdata : null;

            if (is_null($form_data)) {
                return true;
            }

            $payment_custom_id = null;

            foreach ($form_data as $key => $value) {
                $db_array = array(
                    'payment_id' => $payment_id,
                    'payment_custom_fieldid' => $key,
                    'payment_custom_fieldvalue' => $value
                );

                $payment_custom = $this->where('payment_id', $payment_id)->where('payment_custom_fieldid', $key)->get();

                if ($payment_custom->num_rows()) {
                    $payment_custom_id = $payment_custom->row()->payment_custom_id;
                }

                parent::save($payment_custom_id, $db_array);
            }

            return true;
        }

        return $result;
    }

    /**
     * @param integer $payment_id
     * @return $this
     */
    public function by_id($payment_id)
    {
        $this->db->where('ip_payment_custom.payment_id', $payment_id);
        return $this;
    }

    public function get_by_payid($payment_id)
    {
        $result = $this->where('ip_payment_custom.payment_id', $payment_id)->get()->result();
        return $result;
    }

}
