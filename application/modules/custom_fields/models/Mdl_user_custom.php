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
 * Class Mdl_User_Custom
 */
class Mdl_User_Custom extends Validator
{
    public $table = 'ip_user_custom';
    public $primary_key = 'ip_user_custom.user_custom_id';

    /**
     * @param $user_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($user_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $db_array = $this->_formdata;
            $user_custom_id = null;
            $db_array['user_id'] = $user_id;
            $user_custom = $this->where('user_id', $user_id)->get();

            if ($user_custom->num_rows()) {
                $user_custom_id = $user_custom->row()->user_custom_id;
            }

            parent::save($user_custom_id, $db_array);

            return true;
        }

        return $result;
    }

}
