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
 * Class Mdl_User_Clients
 */
class Mdl_User_Clients extends MY_Model
{
    public $table = 'ip_user_clients';
    public $primary_key = 'ip_user_clients.user_client_id';

    public function default_select()
    {
        $this->db->select('ip_user_clients.*, ip_users.user_name, ip_clients.client_name');
    }

    public function default_join()
    {
        $this->db->join('ip_users', 'ip_users.user_id = ip_user_clients.user_id');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_user_clients.client_id');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_clients.client_name');
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

}
