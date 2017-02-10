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
 * Class Mdl_Settings
 */
class Mdl_Settings extends CI_Model
{
    public $settings = array();

    /**
     * @param $key
     * @param $value
     */
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

    /**
     * @param $key
     * @return null
     */
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

    /**
     * @param $key
     */
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

    /**
     * @param $key
     * @param string $default
     * @return mixed|string
     */
    public function setting($key, $default = '')
    {
        return (isset($this->settings[$key])) ? $this->settings[$key] : $default;
    }

    /**
     * @param $key
     * @param $value
     */
    public function set_setting($key, $value)
    {
        $this->settings[$key] = $value;
    }

}
