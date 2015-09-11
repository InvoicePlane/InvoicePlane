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

class Mdl_Payments extends Response_Model
{
    public $table = 'ip_payments';
    public $primary_key = 'ip_payments.payment_id';
    public $validation_rules = 'validation_rules';

    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_payment_custom.*,
            ip_payment_methods.*,
            ip_invoice_amounts.*,
            ip_clients.client_name,
        	ip_clients.client_id,
            ip_invoices.invoice_number,
            ip_invoices.invoice_date_created,
            ip_payments.*", false);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_payments.payment_date DESC');
    }

    public function default_join()
    {
        $this->db->join('ip_invoices', 'ip_invoices.invoice_id = ip_payments.invoice_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_invoices.client_id');
        $this->db->join('ip_invoice_amounts', 'ip_invoice_amounts.invoice_id = ip_invoices.invoice_id');
        $this->db->join('ip_payment_methods', 'ip_payment_methods.payment_method_id = ip_payments.payment_method_id',
            'left');
        $this->db->join('ip_payment_custom', 'ip_payment_custom.payment_id = ip_payments.payment_id', 'left');
    }

    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ),
            'payment_date' => array(
                'field' => 'payment_date',
                'label' => lang('date'),
                'rules' => 'required'
            ),
            'payment_amount' => array(
                'field' => 'payment_amount',
                'label' => lang('payment'),
                'rules' => 'required|callback_validate_payment_amount'
            ),
            'payment_method_id' => array(
                'field' => 'payment_method_id',
                'label' => lang('payment_method')
            ),
            'payment_note' => array(
                'field' => 'payment_note',
                'label' => lang('note')
            )
        );
    }

    public function validate_payment_amount($amount)
    {
        $invoice_id = $this->input->post('invoice_id');
        $payment_id = $this->input->post('payment_id');

        $invoice_balance = $this->db->where('invoice_id',
            $invoice_id)->get('ip_invoice_amounts')->row()->invoice_balance;

        if ($payment_id) {
            $payment = $this->db->where('payment_id', $payment_id)->get('ip_payments')->row();

            $invoice_balance = $invoice_balance + $payment->payment_amount;
        }

        if ($amount > $invoice_balance) {
            $this->form_validation->set_message('validate_payment_amount', lang('payment_cannot_exceed_balance'));
            return false;
        }

        return true;
    }

    public function save($id = null, $db_array = null)
    {
        $db_array = ($db_array) ? $db_array : $this->db_array();

        // Save the payment
        $id = parent::save($id, $db_array);

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($db_array['invoice_id']);

        return $id;
    }

    public function delete($id = null)
    {
        // Get the invoice id before deleting payment
        $this->db->select('invoice_id');
        $this->db->where('payment_id', $id);
        $invoice_id = $this->db->get('ip_payments')->row()->invoice_id;

        // Delete the payment
        parent::delete($id);

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        // Change invoice status back to sent
        $this->db->select('invoice_status_id');
        $this->db->where('invoice_id', $invoice_id);
        $invoice = $this->db->get('ip_invoices')->row();

        if ($invoice->invoice_status_id == 4) {
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('invoice_status_id', 2);
            $this->db->update('ip_invoices');
        }

        $this->load->helper('orphan');
        delete_orphans();
    }

    public function db_array()
    {
        $db_array = parent::db_array();

        $db_array['payment_date'] = date_to_mysql($db_array['payment_date']);
        $db_array['payment_amount'] = standardize_amount($db_array['payment_amount']);

        return $db_array;
    }

    public function prep_form($id = null)
    {
        if (!parent::prep_form($id)) {
            return false;
        }

        if (!$id) {
            parent::set_form_value('payment_date', date('Y-m-d'));
        }

        return true;
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_clients.client_id', $client_id);
        return $this;
    }

}
