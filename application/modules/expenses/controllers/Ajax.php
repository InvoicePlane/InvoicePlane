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
class Ajax extends Admin_Controller
{
    public $ajax_controller = TRUE;
    public function add()
    {
		
		
		
        $this->load->model('expenses/mdl_expenses');
        if ($this->mdl_expenses->run_validation()) {
            $this->mdl_expenses->save();
			/*still need to implement the save function*/
            $response = array(
                'success' => 1
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'validation_errors' => json_errors()
            );
        }
        echo json_encode($response);
    }
    public function modal_add_expense()
    {
		
        $this->load->module('layout');
        $this->load->model('expenses/mdl_expenses');
        $this->load->model('payment_methods/mdl_payment_methods');
        $data = array(
            //'payment_methods' => $this->mdl_payment_methods->get()->result(),
            'expense_id' => $this->input->post('expense_id'),
            'expense_date' => $this->input->post('expense_date'),
            'expense_amount' => $this->input->post('expense_amount'),
			'payment_method_id' => $this->input->post('payment_method_id'),
			'expense_note' => $this->input->post('expense_note'),
			'tax_rate_id' => $this->input->post('tax_rate_id'),
			'client_id' => $this->input->post('client_id')
			
        );
        $this->layout->load_view('expenses/modal_add_expense', $data);
    }
}