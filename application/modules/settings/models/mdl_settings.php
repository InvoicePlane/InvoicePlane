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

class Mdl_Settings extends CI_Model
{
    public $settings = array();

    public function get($key)
    {
        $this->db->select('setting_value');
        $this->db->where('setting_key', $key);
        $query = $this->db->get('ip_settings');

        if ($query->row()) {
            return $query->row()->setting_value;
        } else {
            return null;
        }
    }

    public function save($key, $value)
    {
        $db_array = array(
            'setting_key' => $key,
            'setting_value' => $value
        );

        if ($this->get($key) !== null) {
            $this->db->where('setting_key', $key);
            $this->db->update('ip_settings', $db_array);
        } else {
            $this->db->insert('ip_settings', $db_array);
        }
    }

    public function delete($key)
    {
        $this->db->where('setting_key', $key);
        $this->db->delete('ip_settings');
    }

    public function load_settings()
    {
        $ip_settings = $this->db->get('ip_settings')->result();

        foreach ($ip_settings as $data) {
            $this->settings[$data->setting_key] = $data->setting_value;
        }
    }

    public function setting($key)
    {
        return (isset($this->settings[$key])) ? $this->settings[$key] : '';
    }

    public function set_setting($key, $value)
    {
        $this->settings[$key] = $value;
    }

}
