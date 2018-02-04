<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Class Ajax
 */
class Ajax extends Admin_Controller
{

    public $ajax_controller = true;

    public function name_query()
    {
        // Load the model & helper
        $this->load->model('clients/mdl_clients');

        $response = [];

        // Get the post input
        $query = $this->input->get('query');
        $permissiveSearchClients = $this->input->get('permissive_search_clients');

        if (empty($query)) {
            echo json_encode($response);
            exit;
        }

        // Search for chars "in the middle" of clients names
        $permissiveSearchClients ? $moreClientsQuery = '%' : $moreClientsQuery = '';

        // Search for clients
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace("%", "", $escapedQuery);
        $clients = $this->mdl_clients
            ->where('client_active', 1)
            ->having('client_name LIKE \'' . $moreClientsQuery . $escapedQuery . '%\'')
            ->or_having('client_surname LIKE \'' . $moreClientsQuery . $escapedQuery . '%\'')
            ->or_having('client_fullname LIKE \'' . $moreClientsQuery . $escapedQuery . '%\'')
            ->order_by('client_name')
            ->get()
            ->result();

        foreach ($clients as $client) {
            $response[] = [
                'id' => $client->client_id,
                'text' => htmlsc(format_client($client)),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    /**
     * Get the latest clients
     */
    public function get_latest()
    {
        // Load the model & helper
        $this->load->model('clients/mdl_clients');

        $response = [];

        $clients = $this->mdl_clients
            ->where('client_active', 1)
            ->limit(5)
            ->order_by('client_date_created')
            ->get()
            ->result();

        foreach ($clients as $client) {
            $response[] = [
                'id' => $client->client_id,
                'text' => htmlsc(format_client($client)),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    public function save_preference_permissive_search_clients()
    {
        $this->load->model('mdl_settings');
        $permissiveSearchClients = $this->input->get('permissive_search_clients');

        if (!preg_match('!^[0-1]{1}$!', $permissiveSearchClients)) {
            exit;
        }

        $this->mdl_settings->save('enable_permissive_search_clients', $permissiveSearchClients);
    }

    public function save_client_note()
    {
        $this->load->model('clients/mdl_client_notes');

        if ($this->mdl_client_notes->run_validation()) {
            $this->mdl_client_notes->save();

            $response = [
                'success' => 1,
                'new_token' => $this->security->get_csrf_hash(),
            ];
        } else {
            $this->load->helper('json_error');
            $response = [
                'success' => 0,
                'new_token' => $this->security->get_csrf_hash(),
                'validation_errors' => json_errors(),
            ];
        }

        echo json_encode($response);
    }

    public function load_client_notes()
    {
        $this->load->model('clients/mdl_client_notes');
        $data = [
            'client_notes' => $this->mdl_client_notes->where('client_id',
                $this->input->post('client_id'))->get()->result(),
        ];

        $this->layout->load_view('clients/partial_notes', $data);
    }

}
