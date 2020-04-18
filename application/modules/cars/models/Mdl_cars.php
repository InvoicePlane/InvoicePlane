<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Aeolun (www.serial-experiments.com)
 * @copyright	Copyright (c) 2012 - 2016 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */
class Mdl_Cars extends Response_Model
{
    public $table = 'ip_cars';
    public $primary_key = 'ip_cars.car_id';
    public $validation_rules = 'validation_rules';
    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS ip_car_custom.*,
            ip_clients.client_name,
	    ip_clients.client_surname,
            ip_clients.client_id,
            ip_cars.*", FALSE);
    }
    public function default_order_by()
    {
        $this->db->order_by('ip_cars.car_date_modified DESC');
    }
    public function default_join()
    {
	$this->db->join('ip_car_custom', 'ip_car_custom.car_id = ip_cars.car_id', 'left');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_cars.car_client_id', 'left');
    }
    public function validation_rules()
    {
        return array(
            'car_vehicle' => array(
                'field' => 'car_vehicle',
                'label' => lang('car_vehicle'),
                'rules' => 'required'
            ),
            'car_kmstand' => array(
                'field' => 'car_kmstand',
                'label' => lang('car_kmstand'),
                'rules' => 'required'
            ),
            'car_chassnr' => array(
                'field' => 'car_chassnr',
                'label' => lang('car_chassnr'),
                'rules' => 'required'
            ),
            'car_builddate' => array(
                'field' => 'car_builddate',
                'label' => lang('car_builddate'),
		'rules' => 'required'
            ),
            'car_licenseplate' => array(
                'field' => 'car_licenseplate',
                'label' => lang('car_licenseplate')
            ),
            'car_auhu' => array(
                'field' => 'car_auhu',
                'label' => lang('car_auhu'),
		'rules' => 'required'
            ),
	    'car_client_id' => array(
                'field' => 'car_client_id',
                'label' => lang('client')
            ),
	    'car_note' => array(
                'field' => 'car_note',
                'label' => lang('note')
            ),
            'car_date_created' => array(
                'field' => 'car_date_created',
                'label' => lang('car_date_created')
            ),
            'car_date_modified' => array(
                'field' => 'car_date_modified',
                'label' => lang('car_date_modified')
            ),
        );
    }

    public function save($id = NULL, $db_array = NULL)
    {
        $db_array = ($db_array) ? $db_array : $this->db_array();
        // Save the car
        $id = parent::save($id, $db_array);
        return $id;
    }
	
    public function db_array()
    {
        $db_array = parent::db_array();
        $db_array['car_date_created'] = date_to_mysql($db_array['car_date_created']);
        $db_array['car_date_modified'] = date_to_mysql($db_array['car_date_modified']);
	$db_array['car_builddate'] = date_to_mysql($db_array['car_builddate']);	
	$db_array['car_auhu'] = date_to_mysql($db_array['car_auhu']);
	$db_array['car_url_key'] = $this->get_url_key($this->form_value('car_id'));

        return $db_array;
    }

    public function prep_form($id = NULL)
    {
        if (!parent::prep_form($id)) {
            return FALSE;
        }
        if (!$id) {
            parent::set_form_value('car_builddate', date('Y-m-d'));
	    parent::set_form_value('car_auhu', date('Y-m-d'));
        }
        return TRUE;
    }

    public function is_active()
    {
        $this->filter_where('car_active', 1);
        return $this;
    }

    /**
     * @return string
     */
    public function get_url_key($car_id)
    {
        $key = $this->db->select('ip_cars.car_url_key')
            ->from('ip_cars')
            ->where('ip_cars.car_id', $car_id)
            ->get()->result();

        if ($key[0]->car_url_key == '')
        {
		$this->load->helper('string');
                $url_key = random_string('alnum', 32);
        }
        else
        {
                $url_key = $key[0]->car_url_key;
        }

        return $url_key;
    }

    public function has_url_key($car_id)
    {
        if (!$car_id) {
            return false;
        }

        $key = $this->db->select('ip_cars.car_url_key')
            ->from('ip_cars')
            ->where('ip_cars.car_id', $car_id)
            ->get()->result();

	if ($key[0]->car_url_key == '')
        {
                return false;
        }
        else
        {
                return true;
        }
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_cars.car_client_id', $client_id);
        return $this;
    }

    public function by_id($car_id)
    {
        $this->filter_where('ip_cars.car_id', $car_id);
        return $this;
    }
}
