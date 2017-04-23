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
 * Class User_Clients
 */
class User_Clients extends Admin_Controller
{
    /**
     * Custom_Values constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('users/mdl_users');
        $this->load->model('clients/mdl_clients');
        $this->load->model('user_clients/mdl_user_clients');

        $this->load->helper('client');
    }

    public function index()
    {
        redirect('users');
    }

    /**
     * @param null $id
     */
    public function user($id = null)
    {
        if ($this->input->post('btn_cancel')) {
            redirect('users');
        }

        $user = $this->mdl_users->get_by_id($id);

        if (empty($user)) {
            redirect('users');
        }

        $user_clients = $this->mdl_user_clients->assigned_to($id)->get()->result();

        $this->layout->set('user', $user);
        $this->layout->set('user_clients', $user_clients);
        $this->layout->set('id', $id);
        $this->layout->buffer('content', 'user_clients/field');
        $this->layout->render();
    }

    /**
     * @param null $user_id
     */
    public function create($user_id = null)
    {
        if (!$user_id) {
            redirect('custom_values');
        }

        if ($this->input->post('btn_cancel')) {
            redirect('user_clients/field/' . $user_id);
        }

        if ($this->mdl_user_clients->run_validation()) {
            $this->mdl_user_clients->save();
            redirect('user_clients/user/' . $user_id);
        }

        $user = $this->mdl_users->get_by_id($user_id);
        $clients = $this->mdl_clients->get_not_assigned_to_user($user_id);

        $this->layout->set('id', $user_id);
        $this->layout->set('user', $user);
        $this->layout->set('clients', $clients);
        $this->layout->buffer('content', 'user_clients/new');
        $this->layout->render();
    }

    /**
     * @param integer $user_client_id
     */
    public function delete($user_client_id)
    {
        $ref = $this->mdl_user_clients->get_by_id($user_client_id);

        $this->mdl_user_clients->delete($user_client_id);
        redirect('user_clients/user/' . $ref->user_id);
    }

}
