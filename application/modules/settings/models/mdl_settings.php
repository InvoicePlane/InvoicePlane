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

class Mdl_Settings extends CI_Model {

	public $settings = array();

	public function get($key)
	{
		$this->db->select('setting_value');
		$this->db->where('setting_key', $key);
		$query = $this->db->get('fi_settings');

		if ($query->row())
		{
			return $query->row()->setting_value;
		}
		else
		{
			return NULL;
		}
	}

	public function save($key, $value)
	{
		$db_array = array(
			'setting_key' => $key,
			'setting_value' => $value
		);

		if ($this->get($key) !== NULL)
		{
			$this->db->where('setting_key', $key);
			$this->db->update('fi_settings', $db_array);
		}
		else
		{
			$this->db->insert('fi_settings', $db_array);
		}
	}

	public function delete($key)
	{
		$this->db->where('setting_key', $key);
		$this->db->delete('fi_settings');
	}

	public function load_settings()
	{
		$fi_settings = $this->db->get('fi_settings')->result();

		foreach ($fi_settings as $data)
		{
			$this->settings[$data->setting_key] = $data->setting_value;
		}
	}

	public function setting($key)
	{
		return (isset($this->settings[$key])) ? $this->settings[$key] : '';
	}

	public function set_setting($key, $value)
	{
		$this->settings->$key = $value;
	}

}

?>