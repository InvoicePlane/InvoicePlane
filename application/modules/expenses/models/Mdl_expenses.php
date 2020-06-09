<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Aeolun (www.serial-experiments.com)
 * @copyright	Copyright (c) 2012 - 2016 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */
class Mdl_Expenses extends Response_Model
{
    public $table = 'ip_expenses';
    public $primary_key = 'ip_expenses.expense_id';
    public $validation_rules = 'validation_rules';
    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_expense_custom.*,
            ip_payment_methods.*,
            ip_clients.client_name,
            ip_clients.client_id,
            ip_tax_rates.tax_rate_name,
            ip_tax_rates.tax_rate_percent,
            ip_expenses.*", FALSE);
    }
    public function default_order_by()
    {
        $this->db->order_by('ip_expenses.expense_date DESC');
    }
    public function default_join()
    {
        $this->db->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_expenses.payment_method_id', 'left');
        $this->db->join('ip_expense_custom', 'ip_expense_custom.expense_id = ip_expenses.expense_id', 'left');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_expenses.client_id', 'left');
        $this->db->join('ip_tax_rates', 'ip_tax_rates.tax_rate_id = ip_expenses.tax_rate_id', 'left');
    }
    public function validation_rules()
    {
        return array(
            'expense_date' => array(
                'field' => 'expense_date',
                'label' => lang('date'),
                'rules' => 'required'
            ),
            'expense_amount' => array(
                'field' => 'expense_amount',
                'label' => lang('payment'),
                'rules' => 'required'
            ),
            'tax_rate_id' => array(
                'field' => 'tax_rate_id',
                'label' => lang('tax_rate')
            ),
            'payment_method_id' => array(
                'field' => 'payment_method_id',
                'label' => lang('payment_method')
            ),
            'expense_note' => array(
                'field' => 'expense_note',
                'label' => lang('note')
            ),
            'client_id' => array(
                'field' => 'client_id',
                'label' => lang('client')
            )
        );
    }
    public function save($id = NULL, $db_array = NULL)
    {
        $db_array = ($db_array) ? $db_array : $this->db_array();
        // Save the payment
        $id = parent::save($id, $db_array);
        return $id;
    }
    	
	
	public function get_extension($file) 
	{
	 $extension = end(explode(".", $file));
	 return $extension ? $extension : false;
	}
	
    public function db_array()
    {
        $db_array = parent::db_array();
        $db_array['expense_date'] = date_to_mysql($db_array['expense_date']);
        $db_array['expense_amount'] = standardize_amount($db_array['expense_amount']);
		
		
		$targetDir = "uploads/";
		$fileName = basename($_FILES["expense_file"]["name"]);
		$targetFilePath = $targetDir . $fileName;
		$fileType = pathinfo($targetFilePath,PATHINFO_EXTENSION);
		
		
		if(!empty($_FILES["expense_file"]["name"]))
		{
			// Allow certain file formats
			$allowTypes = array('jpg','png','jpeg','gif','pdf');
			
			$db_array['expense_file'] = base64_encode(file_get_contents($_FILES["expense_file"]["tmp_name"]));
			$db_array['expense_file_name'] = $fileName;
			
		}
		else
		{
			$db_array['expense_file'] = "";
			$db_array['expense_file_name'] = "";
		}
		

		
		
        return $db_array;
    }
    public function prep_form($id = NULL)
    {
        if (!parent::prep_form($id)) {
            return FALSE;
        }
        if (!$id) {
            parent::set_form_value('expense_date', date('Y-m-d'));
        }
        return TRUE;
    }
}