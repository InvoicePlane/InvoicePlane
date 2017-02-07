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

class Mdl_Payment_Custom extends Validator
{
    public $table = 'ip_payment_custom';
    public $primary_key = 'ip_payment_custom.payment_custom_id';

    public function save_custom($payment_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $db_array = $this->_formdata;
            $payment_custom_id = null;

            $db_array['payment_id'] = $payment_id;

            $payment_custom = $this->where('payment_id', $payment_id)->get();

            if ($payment_custom->num_rows()) {
                $payment_custom_id = $payment_custom->row()->payment_custom_id;
            }

            parent::save($payment_custom_id, $db_array);
            return true;
        }
        return $result;
    }
}
