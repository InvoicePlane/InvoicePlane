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
 * Class Ajax
 */
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function name_query()
    {
        // Load the model & helper
        $this->load->model('clients/mdl_clients');

        $response = array();

        // Get the post input
        $query = $this->input->get('query');

        if (empty($query)) {
            echo json_encode($response);
            exit;
        }

        // Search for clients
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace("%", "", $escapedQuery);
        $clients = $this->mdl_clients
            ->where('client_active', 1)
            ->having('client_name LIKE \'' . $escapedQuery . '%\'')
            ->or_having('client_surname LIKE \'' . $escapedQuery . '%\'')
            ->or_having('client_fullname LIKE \'' . $escapedQuery . '%\'')
            ->order_by('client_name')
            ->get()
            ->result();

        foreach ($clients as $client) {
            $response[] = array(
                'id' => $client->client_id,
                'text' => htmlsc(format_client($client)),
            );
        }

        // Return the results
        echo json_encode($response);
    }

    public function save_client_note()
    {
        $this->load->model('clients/mdl_client_notes');

        if ($this->mdl_client_notes->run_validation()) {
            $this->mdl_client_notes->save();

            $response = array(
                'success' => 1,
                'new_token' => $this->security->get_csrf_hash(),
            );
        } else {
            $this->load->helper('json_error');
            $response = array(
                'success' => 0,
                'new_token' => $this->security->get_csrf_hash(),
                'validation_errors' => json_errors(),
            );
        }

        echo json_encode($response);
    }

    public function load_client_notes()
    {
        $this->load->model('clients/mdl_client_notes');
        $data = array(
            'client_notes' => $this->mdl_client_notes->where('client_id', $this->input->post('client_id'))->get()->result()
        );

        $this->layout->load_view('clients/partial_notes', $data);
    }

}
