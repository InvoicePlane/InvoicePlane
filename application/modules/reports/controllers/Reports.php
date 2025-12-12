<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Reports extends Admin_Controller
{
    /**
     * Reports constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_reports');
    }

    public function customer_statement($client_id = null)
    {
        // Handle Direct Link (GET) or Form Submit (POST)
        if ($this->input->post('btn_submit') || $client_id) {
            
            // Determine Client ID
            $target_client_id = $client_id ? $client_id : $this->input->post('client_id');

            // Determine Dates (Default to This Year if direct link)
            if ($client_id) {
                $from_date = date_from_mysql(date('Y-01-01'));
                $to_date = date_from_mysql(date('Y-12-31'));
            } else {
                $from_date = $this->input->post('from_date');
                $to_date = $this->input->post('to_date');
            }

            $this->load->model('clients/mdl_clients');
            $this->load->model('users/mdl_users');

            $client = $this->mdl_clients->get_by_id($target_client_id);
            // Fetch current user for "Company" details on PDF
            $user = $this->mdl_users->get_by_id($this->session->userdata('user_id'));

            if (!$client) {
                show_404();
            }

            $data = $this->mdl_reports->get_customer_statement($target_client_id, $from_date, $to_date);
            
            $data['client'] = $client;
            $data['user'] = $user;
            $data['from_date'] = $from_date;
            $data['to_date'] = $to_date;

            $html = $this->load->view('reports/customer_statement', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('customer_statement') . '_' . $client->client_name, true);
            return;
        }

        $this->load->model('clients/mdl_clients');
        $data = [
            'clients' => $this->mdl_clients->get()->result()
        ];
        
        $this->layout->set($data);
        $this->layout->buffer('content', 'reports/customer_statement_index')->render();
    }

    public function sales_by_client()
    {
        if ($this->input->post('btn_submit')) {
            $data = [
                'results'   => $this->mdl_reports->sales_by_client($this->input->post('from_date'), $this->input->post('to_date')),
                'from_date' => $this->input->post('from_date'),
                'to_date'   => $this->input->post('to_date'),
            ];

            $html = $this->load->view('reports/sales_by_client', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('sales_by_client'), true);
        }

        $this->layout->buffer('content', 'reports/sales_by_client_index')->render();
    }

    public function invoices_per_client()
    {
        if ($this->input->post('btn_submit')) {
            $data = [
                'results'   => $this->mdl_reports->invoices_per_client($this->input->post('from_date'), $this->input->post('to_date')),
                'from_date' => $this->input->post('from_date'),
                'to_date'   => $this->input->post('to_date'),
            ];

            $html = $this->load->view('reports/invoices_per_client', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('invoices_per_client'), true);
        }

        $this->layout->buffer('content', 'reports/invoices_per_client_index')->render();
    }

    public function payment_history()
    {
        if ($this->input->post('btn_submit')) {
            $data = [
                'results'   => $this->mdl_reports->payment_history($this->input->post('from_date'), $this->input->post('to_date')),
                'from_date' => $this->input->post('from_date'),
                'to_date'   => $this->input->post('to_date'),
            ];

            $html = $this->load->view('reports/payment_history', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('payment_history'), true);
        }

        $this->layout->buffer('content', 'reports/payment_history_index')->render();
    }

    public function invoice_aging()
    {
        if ($this->input->post('btn_submit')) {
            $data = [
                'results' => $this->mdl_reports->invoice_aging(),
            ];

            $html = $this->load->view('reports/invoice_aging', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('invoice_aging'), true);
        }

        $this->layout->buffer('content', 'reports/invoice_aging_index')->render();
    }

    public function sales_by_year()
    {
        if ($this->input->post('btn_submit')) {
            $data = [
                'results'   => $this->mdl_reports->sales_by_year($this->input->post('from_date'), $this->input->post('to_date'), $this->input->post('minQuantity'), $this->input->post('maxQuantity'), $this->input->post('checkboxTax')),
                'from_date' => $this->input->post('from_date'),
                'to_date'   => $this->input->post('to_date'),
            ];

            $html = $this->load->view('reports/sales_by_year', $data, true);

            $this->load->helper('mpdf');

            pdf_create($html, trans('sales_by_date'), true);
        }

        $this->layout->buffer('content', 'reports/sales_by_year_index')->render();
    }
}