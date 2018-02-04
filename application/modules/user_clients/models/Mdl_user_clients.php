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
 * Class Mdl_User_Clients
 */
class Mdl_User_Clients extends MY_Model
{
    public $table = 'ip_user_clients';
    public $primary_key = 'ip_user_clients.user_client_id';

    public function default_select()
    {
        $this->db->select('ip_user_clients.*, ip_users.user_name, ip_clients.client_name, ip_clients.client_surname');
    }

    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_clients.user_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_user_clients.client_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_clients.client_name', 'ACS');
    }

    /**
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'user_id' => array(
                'field' => 'user_id',
                'label' => trans('user'),
                'rules' => 'required'
            ),
            'client_id' => array(
                'field' => 'client_id',
                'label' => trans('client'),
                'rules' => 'required'
            ),
        );
    }

    /**
     * @param $user_id
     * @return $this
     */
    public function assigned_to($user_id)
    {
        $this->filter_where('ip_user_clients.user_id', $user_id);
        return $this;
    }
    
    /**
    * 
    * @param array $users_id
    */
    public function set_all_clients_user($users_id)
    {
        $this->load->model('clients/mdl_clients');
        
        for ($x = 0; $x < count($users_id); $x++) {
            $clients = $this->mdl_clients->get_not_assigned_to_user($users_id[$x]);
            
            for ($i = 0; $i < count($clients); $i++) {
                $user_client = array(
                    'user_id' => $users_id[$x],
                    'client_id' => $clients[$i]->client_id
                );
                
                $this->db->insert('ip_user_clients', $user_client);
            }
        }
    }
    
    public function get_users_all_clients()
    {
        $this->load->model('users/mdl_users');
        $users = $this->mdl_users->where('user_all_clients', 1)->get()->result();
        
        $new_users = array();
        
        for ($i = 0; $i < count($users); $i++) {
            array_push($new_users, $users[$i]->user_id);
        }
        
        $this->set_all_clients_user($new_users);
    }
}
