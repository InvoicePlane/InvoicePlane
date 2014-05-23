<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

class Mdl_Payment_Custom extends MY_Model {
    
    public $table = 'fi_payment_custom';
    public $primary_key = 'fi_payment_custom.payment_custom_id';
    
    public function save_custom($payment_id, $db_array)
    {
        $payment_custom_id = NULL;
        
        $db_array['payment_id'] = $payment_id;
        
        $payment_custom = $this->where('payment_id', $payment_id)->get();
        
        if ($payment_custom->num_rows())
        {
            $payment_custom_id = $payment_custom->row()->payment_custom_id;
        }

        parent::save($payment_custom_id, $db_array);
    }
    
}

?>