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
        // Load the model
        $this->load->model('clients/mdl_clients');

        // Get the post input
        $query = $this->input->post('query');

        $escapedQuery = $this->db->escape_like_string($query);

        $clients = $this->mdl_clients->select('client_name,client_surname')
            ->where(
            "client_name LIKE $escapedQuery%
            OR client_surname LIKE $escapedQuery%
            OR CONCATENATE(client_name,' ', client_surname) LIKE  $escapedQuery%")
            ->order_by('CONCATENATE(client_name,client_surname)')
            ->get(array(), false)
            ->result();

        $response = array();

        foreach ($clients as $client) {
            $response[] = $client->client_name;
        }

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
        $this->load->helper('client');
        $data = array(
            'client_notes' => $this->mdl_client_notes->where('client_id', $this->input->post('client_id'))->get()->result()
        );

        $this->layout->load_view('clients/partial_notes', $data);
    }

}
