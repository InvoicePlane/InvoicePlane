<?php

if (! defined('BASEPATH')) {
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
class Ajax extends Admin_Controller
{
    public $ajax_controller = true;

    public function name_query($type = 1)
    {
        // Load the model & helper
        $this->load->model('users/mdl_users');
        $this->load->helper('user');

        $response = [];

        // Get the post input
        $query = $this->input->get('query');
        $permissiveSearchUsers = $this->input->get('permissive_search_users');

        if (empty($query)) {
            echo json_encode($response);
            exit;
        }

        // Search for chars "in the middle" of users names
        $permissiveSearchUsers ? $moreUsersQuery = '%' : $moreUsersQuery = '';

        // Search for users $type
        $escapedQuery = $this->db->escape_str($query);
        $escapedQuery = str_replace('%', '', $escapedQuery);
        // Not searched: user_address_1 user_address_2 user_city user_state user_zip user_country user_invoicing_contact
        $users = $this->mdl_users
            ->where('user_active', 1)
            ->where('user_type', $type)
            ->having('user_name LIKE \'' . $moreUsersQuery . $escapedQuery . '%\'')
            ->or_having('user_company LIKE \'' . $moreUsersQuery . $escapedQuery . '%\'')
            ->or_having('user_invoicing_contact LIKE \'' . $moreUsersQuery . $escapedQuery . '%\'')
            ->order_by('user_name')
            ->get()
            ->result();

        foreach ($users as $user) {
            $response[] = [
                'id'   => $user->user_id,
                'text' => format_user($user),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    /**
     * Get the latest users
     */
    public function get_latest()
    {
        // Load the model & helper
        $this->load->model('users/mdl_users');

        $response = [];

        $users = $this->mdl_users
            ->where('user_active', 1)
            ->limit(5)
            ->order_by('user_date_created')
            ->get()
            ->result();

        foreach ($users as $user) {
            $response[] = [
                'id'   => $user->user_id,
                'text' => htmlsc(format_user($user)),
            ];
        }

        // Return the results
        echo json_encode($response);
    }

    public function save_preference_permissive_search_users()
    {
        $this->load->model('mdl_settings');
        $permissiveSearchUsers = $this->input->get('permissive_search_users');

        if ( ! preg_match('!^[0-1]{1}$!', $permissiveSearchUsers)) {
            exit;
        }

        $this->mdl_settings->save('enable_permissive_search_users', $permissiveSearchUsers);
    }

    public function save_user_client()
    {
        $user_id = $this->input->post('user_id');
        $client_id = $this->input->post('client_id');

        $this->load->model('clients/mdl_clients');
        $this->load->model('users/mdl_user_clients');

        $client = $this->mdl_clients->get_by_id($client_id);
        if ($client) {
            $client_id = $client->client_id;

            // Is this a new user or an existing user?
            if (!empty($user_id)) {
                // Existing user - go ahead and save the entries
                $user_client = $this->mdl_user_clients->where('ip_user_clients.user_id', $user_id)
                    ->where('ip_user_clients.client_id', $client_id)->get();

                if (!$user_client->num_rows()) {
                    $this->mdl_user_clients->save(null, array('user_id' => $user_id, 'client_id' => $client_id));
                }
            } else {
                // New user - assign the entries to a session variable until user record is saved
                $user_clients = $this->session->userdata('user_clients') ? $this->session->userdata('user_clients') : array();

                $user_clients[$client_id] = $client_id;

                $this->session->set_userdata('user_clients', $user_clients);
            }
        }
    }

    public function load_user_client_table()
    {
        $session_user_clients = $this->session->userdata('user_clients');

        if ($session_user_clients) {
            $this->load->model('clients/mdl_clients');

            $data = array(
                'id' => null,
                'user_clients' => $this->mdl_clients->where_in('ip_clients.client_id', $session_user_clients)->get()->result()
            );
        } else {
            $this->load->model('users/mdl_user_clients');

            $data = array(
                'id' => $this->input->post('user_id'),
                'user_clients' => $this->mdl_user_clients->where('ip_user_clients.user_id', $this->input->post('user_id'))->get()->result()
            );
        }

        $this->layout->load_view('users/partial_user_client_table', $data);
    }

    public function modal_add_user_client($user_id = null)
    {
        $this->load->model('clients/mdl_clients');

        if ($session_user_clients = $this->session->userdata('user_clients')) {
            $clients = $this->mdl_clients->where_not_in('ip_clients.client_id', $session_user_clients)->get()->result();
            $assigned_clients = array();
        } else {
            $this->load->model('users/mdl_user_clients');
            $assigned_clients_query = $this->mdl_user_clients->where('ip_user_clients.user_id', $user_id)->get()->result();
            $assigned_clients = array();

            foreach ($assigned_clients_query as $assigned_client) {
                $assigned_clients[] = (int)$assigned_client->client_id;
            }

            if (empty($assigned_clients)) {
                $clients = $this->mdl_clients->get()->result();
            } else {
                $clients = $this->mdl_clients->where_not_in('ip_clients.client_id', $assigned_clients)->get()->result();
            }
        }

        $data = array(
            'user_id' => $user_id,
            'clients' => $clients,
        );

        $this->layout->load_view('users/modal_user_client', $data);
    }

}
