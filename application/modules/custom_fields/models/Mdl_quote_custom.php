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
    public $table = 'ip_quote_custom';
    public $primary_key = 'ip_quote_custom.quote_custom_id';

    /**
     * @param $quote_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($quote_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $fData = $this->_formdata;
            $quote_custom_id = null;

            foreach($fData as $key=>$value){
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

    public function get_by_quoid($quote_id){
        $result = $this->where('ip_quote_custom.quote_id', $quote_id)->get()->result();
        return $result;
    }

}
