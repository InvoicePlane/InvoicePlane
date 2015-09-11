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

class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function save_user_client()
    {
        $user_id = $this->input->post('user_id');
        $client_name = $this->input->post('client_name');

        $this->load->model('clients/mdl_clients');
        $this->load->model('users/mdl_user_clients');

        $client = $this->mdl_clients->where('client_name', $client_name)->get();

        if ($client->num_rows() == 1) {
            $client_id = $client->row()->client_id;

            // Is this a new user or an existing user?
            if ($user_id) {
                // Existing user - go ahead and save the entries

                $user_client = $this->mdl_user_clients->where('ip_user_clients.user_id',
                    $user_id)->where('ip_user_clients.client_id', $client_id)->get();

                if (!$user_client->num_rows()) {
                    $this->mdl_user_clients->save(null, array('user_id' => $user_id, 'client_id' => $client_id));
                }
            } else {
                // New user - assign the entries to a session variable until user record is saved
                $user_clients = ($this->session->userdata('user_clients')) ? $this->session->userdata('user_clients') : array();

                $user_clients[$client_id] = $client_id;

                $this->session->set_userdata('user_clients', $user_clients);
            }
        }
    }

    public function load_user_client_table()
    {
        if ($session_user_clients = $this->session->userdata('user_clients')) {
            $this->load->model('clients/mdl_clients');

            $data = array(
                'id' => null,
                'user_clients' => $this->mdl_clients->where_in('ip_clients.client_id',
                    $session_user_clients)->get()->result()
            );
        } else {
            $this->load->model('users/mdl_user_clients');

            $data = array(
                'id' => $this->input->post('user_id'),
                'user_clients' => $this->mdl_user_clients->where('ip_user_clients.user_id',
                    $this->input->post('user_id'))->get()->result()
            );
        }

        $this->layout->load_view('users/partial_user_client_table', $data);
    }

}
