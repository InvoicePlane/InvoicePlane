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
 * Class Clients
 */
class Clients extends Admin_Controller
{
    /**
     * Clients constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_clients');
    }

    public function index()
    {
        // Display active clients by default
        redirect('clients/status/active');
    }

    /**
     * @param string $status
     * @param int $page
     */
    public function status($status = 'active', $page = 0)
    {
        $status_result = array();
        if ($status == 'all')
            $status_result = [0,1];
        elseif ($status == 'active')
            $status_result = [1];
        else
            $status_result = [0];

        $client_results = $this->mdl_clients->with_total_balance_with_currency($status_result);

        //Create an array of number of clients omitting duplicated client in client results
        //and assigned empty string for client invoice balance to process further.
        $clients = array();

        foreach ($client_results as $client) {
            $clients[$client->client_id] = clone $client;
            $clients[$client->client_id]->client_invoice_balance = '';
        }

        //Fill the array created above with invoice balance concatenated with total amounts
        //for a client in different currencies.
        foreach ($client_results as $client){
            if (!empty($client->invoice_currency)) {
                $amount = $client->client_invoice_balance;
                $symbol = get_currency_symbol($client->invoice_currency);

                if ($amount != 0) {
                    if ($clients[$client->client_id]->client_invoice_balance)
                        $clients[$client->client_id]->client_invoice_balance = format_string_type_currency($amount, $symbol, $clients[$client->client_id]->client_invoice_balance);
                    else
                        $clients[$client->client_id]->client_invoice_balance = format_string_type_currency($amount, $symbol);
                }
            }
        }

        $this->layout->set(
            array(
                'records' => $clients,
                'filter_display' => true,
                'filter_placeholder' => trans('filter_clients'),
                'filter_method' => 'filter_clients'
            )
        );

        $this->layout->buffer('content', 'clients/index');
        $this->layout->render();
    }

    /**
     * @param null $id
     */
    public function form($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('clients');
        }

        // Set validation rule based on is_update
        if ($this->input->post('is_update') == 0 && $this->input->post('client_name') != '') {
            $check = $this->db->get_where('ip_clients', array(
                'client_name' => $this->input->post('client_name'),
                'client_surname' => $this->input->post('client_surname')
            ))->result();

            if (!empty($check)) {
                $this->session->set_flashdata('alert_error', trans('client_already_exists'));
                redirect('clients/form');
            }
        }

        if ($this->mdl_clients->run_validation()) {
            $id = $this->mdl_clients->save($id);

            $this->load->model('custom_fields/mdl_client_custom');
            $result = $this->mdl_client_custom->save_custom($id, $this->input->post('custom'));

            if ($result !== true) {
                $this->session->set_flashdata('alert_error', $result);
                $this->session->set_flashdata('alert_success', null);
                redirect('clients/form/' . $id);
                return;
            } else {
                redirect('clients/view/' . $id);
            }
        }

        if ($id and !$this->input->post('btn_submit')) {
            if (!$this->mdl_clients->prep_form($id)) {
                show_404();
            }

            $this->load->model('custom_fields/mdl_client_custom');
            $this->mdl_clients->set_form_value('is_update', true);

            $client_custom = $this->mdl_client_custom->where('client_id', $id)->get();

            if ($client_custom->num_rows()) {
                $client_custom = $client_custom->row();

                unset($client_custom->client_id, $client_custom->client_custom_id);

                foreach ($client_custom as $key => $val) {
                    $this->mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        } elseif ($this->input->post('btn_submit')) {
            if ($this->input->post('custom')) {
                foreach ($this->input->post('custom') as $key => $val) {
                    $this->mdl_clients->set_form_value('custom[' . $key . ']', $val);
                }
            }
        }

        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_values/mdl_custom_values');
        $this->load->model('custom_fields/mdl_client_custom');

        $custom_fields = $this->mdl_custom_fields->by_table('ip_client_custom')->get()->result();
        $custom_values = [];
        foreach ($custom_fields as $custom_field) {
            if (in_array($custom_field->custom_field_type, $this->mdl_custom_values->custom_value_fields())) {
                $values = $this->mdl_custom_values->get_by_fid($custom_field->custom_field_id)->result();
                $custom_values[$custom_field->custom_field_id] = $values;
            }
        }

        $fields = $this->mdl_client_custom->get_by_clid($id);

        foreach ($custom_fields as $cfield) {
            foreach ($fields as $fvalue) {
                if ($fvalue->client_custom_fieldid == $cfield->custom_field_id) {
                    // TODO: Hackish, may need a better optimization
                    $this->mdl_clients->set_form_value(
                        'custom[' . $cfield->custom_field_id . ']',
                        $fvalue->client_custom_fieldvalue
                    );
                    break;
                }
            }
        }

        $this->load->helper('country');
        $this->load->helper('custom_values');

        $this->layout->set(
            array(
                'custom_fields' => $custom_fields,
                'custom_values' => $custom_values,
                'countries' => get_country_list(trans('cldr')),
                'selected_country' => $this->mdl_clients->form_value('client_country') ?: get_setting('default_country'),
                'languages' => get_available_languages(),
            )
        );

        $this->layout->buffer('content', 'clients/form');
        $this->layout->render();
    }

    /**
     * @param int $client_id
     */
    public function view($client_id)
    {
        $this->load->model('clients/mdl_client_notes');
        $this->load->model('invoices/mdl_invoices');
        $this->load->model('quotes/mdl_quotes');
        $this->load->model('payments/mdl_payments');
        $this->load->model('custom_fields/mdl_custom_fields');
        $this->load->model('custom_fields/mdl_client_custom');

        $client_with_currencies = $this->mdl_clients->get_invoice_amounts_for_client($client_id);
        //$client_with_currencies resultset consist of rows containing clients grouped by currencies.

        //Get result amounts (balance/paid/total) concatenated with amounts in multiple currencies and return
        //these amounts to first element of array.
        if(!empty($client_with_currencies)){
            $balance = $total = $paid = '';

            foreach ($client_with_currencies as $client) {
                if (!empty($client->invoice_currency)){
                    $client_invoice_balance = $client->client_invoice_balance;
                    $client_invoice_paid = $client->client_invoice_paid;
                    $client_invoice_total = $client->client_invoice_total;
                    $symbol = get_currency_symbol($client->invoice_currency);

                    if (!empty($client_invoice_balance) && $client_invoice_balance!='0') {
                        if ($balance)
                            $balance = format_string_type_currency($client_invoice_balance, $symbol, $balance);
                        else
                            $balance = format_string_type_currency($client_invoice_balance, $symbol);
                    }

                    if (!empty($client_invoice_paid) && $client_invoice_paid!='0') {
                        if ($paid)
                            $paid = format_string_type_currency($client_invoice_paid, $symbol, $paid);
                        else
                            $paid = format_string_type_currency($client_invoice_paid, $symbol);
                    }

                    if (!empty($client_invoice_total) && $client_invoice_total!='0') {
                        if ($total)
                            $total = format_string_type_currency($client_invoice_total, $symbol, $total);
                        else
                            $total = format_string_type_currency($client_invoice_total, $symbol);

                    }
                }
            }

            $client_with_currencies[0]->client_invoice_balance = $balance;
            $client_with_currencies[0]->client_invoice_paid = $paid;
            $client_with_currencies[0]->client_invoice_total = $total;

            $client = $client_with_currencies[0];
        }

        $custom_fields = $this->mdl_client_custom->get_by_client($client_id)->result();

        $this->mdl_client_custom->prep_form($client_id);

        if (empty($client)) {
            show_404();
        }

        $this->layout->set(
            array(
                'client' => $client,
                'client_notes' => $this->mdl_client_notes->where('client_id', $client_id)->get()->result(),
                'invoices' => $this->mdl_invoices->by_client($client_id)->limit(20)->get()->result(),
                'quotes' => $this->mdl_quotes->by_client($client_id)->limit(20)->get()->result(),
                'payments' => $this->mdl_payments->by_client($client_id)->limit(20)->get()->result(),
                'custom_fields' => $custom_fields,
                'quote_statuses' => $this->mdl_quotes->statuses(),
                'invoice_statuses' => $this->mdl_invoices->statuses()
            )
        );

        $this->layout->buffer(
            array(
                array(
                    'invoice_table',
                    'invoices/partial_invoice_table'
                ),
                array(
                    'quote_table',
                    'quotes/partial_quote_table'
                ),
                array(
                    'payment_table',
                    'payments/partial_payment_table'
                ),
                array(
                    'partial_notes',
                    'clients/partial_notes'
                ),
                array(
                    'content',
                    'clients/view'
                )
            )
        );

        $this->layout->render();
    }

    /**
     * @param int $client_id
     */
    public function delete($client_id)
    {
        $this->mdl_clients->delete($client_id);
        redirect('clients');
    }

}
