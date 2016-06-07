<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

class Mdl_Expense_Custom extends MY_Model
{
    public $table = 'ip_expense_custom';
    public $primary_key = 'ip_expense_custom.expense_custom_id';

    public function save_custom($expense_id, $db_array)
    {
        $expense_custom_id = NULL;

        $db_array['expense_id'] = $expense_id;

        $expense_custom = $this->where('expense_id', $expense_id)->get();

        if ($expense_custom->num_rows()) {
            $expense_custom_id = $expense_custom->row()->expense_custom_id;
        }

        parent::save($expense_custom_id, $db_array);
    }

}
