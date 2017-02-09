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
 * Class Mdl_Invoice_Custom
 */
class Mdl_Invoice_Custom extends Validator
{
    public $table = 'ip_invoice_custom';
    public $primary_key = 'ip_invoice_custom.invoice_custom_id';

    /**
     * @param $invoice_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($invoice_id, $db_array)
    {
        $result = $this->validate($db_array);
        if ($result === true) {
            $db_array = $this->_formdata;
            $invoice_custom_id = null;
            $db_array['invoice_id'] = $invoice_id;
            $invoice_custom = $this->where('invoice_id', $invoice_id)->get();

            if ($invoice_custom->num_rows()) {
                $invoice_custom_id = $invoice_custom->row()->invoice_custom_id;
            }

            parent::save($invoice_custom_id, $db_array);

            return true;
        }

        return $result;
    }

}
