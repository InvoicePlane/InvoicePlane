<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

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

class Mdl_User_Custom extends MY_Model
{
    public $table = 'ip_user_custom';
    public $primary_key = 'ip_user_custom.user_custom_id';

    public function save_custom($user_id, $db_array)
    {
        $user_custom_id = NULL;

        $db_array['user_id'] = $user_id;

        $user_custom = $this->where('user_id', $user_id)->get();

        if ($user_custom->num_rows()) {
            $user_custom_id = $user_custom->row()->user_custom_id;
        }

        parent::save($user_custom_id, $db_array);
    }

}
