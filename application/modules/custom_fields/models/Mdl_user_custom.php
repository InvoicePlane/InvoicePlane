<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * userPlane
 *
 * @author		userPlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 userPlane.com
 * @license		https://userplane.com/license.txt
 * @link		https://userplane.com
 */

/**
 * Class Mdl_User_Custom
 */
class Mdl_User_Custom extends Validator
{
    public $table = 'ip_user_custom';
    public $primary_key = 'ip_user_custom.user_custom_id';

    public function default_order_by()
    {
        $this->db->order_by('custom_field_table ASC, custom_field_order ASC, custom_field_label ASC');
    }

    /**
     * @param $user_id
     * @param $db_array
     * @return bool|string
     */
    public function save_custom($user_id, $db_array)
    {
        $result = $this->validate($db_array);

        if ($result === true) {
            $fData = $this->_formdata;
            $user_custom_id = null;
            foreach($fData as $key=>$value){
              $db_array = array(
                'user_id' => $user_id,
                'user_custom_fieldid' => $key,
                'user_custom_fieldvalue' => $value
              );

              $user_custom = $this->where('user_id', $user_id)->where('user_custom_fieldid', $key)->get();

              if ($user_custom->num_rows()) {
                  $user_custom_id = $user_custom->row()->user_custom_id;
              }

              parent::save($user_custom_id, $db_array);
            }
            return true;
        }

        return $result;
    }

    public function get_by_useid($user_id){
        $result = $this->where('ip_user_custom.user_id', $user_id)->get()->result();
        return $result;
    }

}
