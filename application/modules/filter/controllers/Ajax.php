<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function filter_invoices()
    {
        $this->load->model('invoices/mdl_invoices');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_invoices->like("CONCAT_WS('^',LOWER(invoice_number),invoice_date_created,invoice_date_due,LOWER(client_name),invoice_total,invoice_balance)", $keyword);
            }
        }

	$invoices = $this->mdl_invoices->get()->result();

	foreach ($invoices as $invoice) {
	    $service = $this->db->query('SELECT service_name FROM ip_services WHERE service_id = ?', $invoice->service_id)->result_array();
            if ($service && $service[0] && $service[0]['service_name'])
               $invoice->service_name = $service[0]['service_name'];
	}

        //     'invoices' => $this->mdl_invoices->get()->result(),
        $data = array(
            'invoices' => $invoices,
            'invoice_statuses' => $this->mdl_invoices->statuses()
        );

        $this->layout->load_view('invoices/partial_invoice_table', $data);
    }

    public function filter_quotes()
    {
        $this->load->model('quotes/mdl_quotes');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_quotes->like("CONCAT_WS('^',LOWER(quote_number),quote_date_created,quote_date_expires,LOWER(client_name),quote_total)", $keyword);
            }
        }

	$quotes = $this->mdl_quotes->get()->result();

	foreach ($quotes as $quote) {
	    $service = $this->db->query('SELECT service_name FROM ip_services WHERE service_id = ?', $quote->service_id)->result_array();
            if ($service && $service[0] && $service[0]['service_name'])
               $quote->service_name = $service[0]['service_name'];
	}

        //    'quotes' => $this->mdl_quotes->get()->result(),
        $data = array(
            'quotes' => $quotes,
            'quote_statuses' => $this->mdl_quotes->statuses()
        );

        $this->layout->load_view('quotes/partial_quote_table', $data);
    }

    public function filter_clients()
    {
        $this->load->model('clients/mdl_clients');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = trim(strtolower($keyword));
                $this->mdl_clients->like("CONCAT_WS('^',LOWER(client_name),LOWER(client_surname),LOWER(client_email),client_phone,client_active)", $keyword);
            }
        }

        $data = array(
            'records' => $this->mdl_clients->with_total_balance()->get()->result()
        );

        $this->layout->load_view('clients/partial_client_table', $data);
    }

    public function filter_payments()
    {
        $this->load->model('payments/mdl_payments');

        $query = $this->input->post('filter_query');
        $keywords = explode(' ', $query);

        foreach ($keywords as $keyword) {
            if ($keyword) {
                $keyword = strtolower($keyword);
                $this->mdl_payments->like("CONCAT_WS('^',payment_date,LOWER(invoice_number),LOWER(client_name),payment_amount,LOWER(payment_method_name),LOWER(payment_note))", $keyword);
            }
        }

        $data = array(
            'payments' => $this->mdl_payments->get()->result()
        );

        $this->layout->load_view('payments/partial_payment_table', $data);
    }

}
