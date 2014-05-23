<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

class Mdl_User_Clients extends MY_Model {
    
    public $table = 'fi_user_clients';
    public $primary_key = 'fi_user_clients.user_client_id';
    
    public function default_select()
    {
        $this->db->select('fi_user_clients.*, fi_users.user_name, fi_clients.client_name');
    }
    
    public function default_join()
    {
        $this->db->join('fi_users', 'fi_users.user_id = fi_user_clients.user_id');
        $this->db->join('fi_clients', 'fi_clients.client_id = fi_user_clients.client_id');
    }
    
    public function default_order_by()
    {
        $this->db->order_by('fi_clients.client_name');
    }
    
    public function assigned_to($user_id)
    {
        $this->filter_where('fi_user_clients.user_id', $user_id);
        return $this;
    }
    
}

?>