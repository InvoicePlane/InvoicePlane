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
 * @copyright	Copyright (c) 2012 - 2014 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 * 
 */

class Mdl_Projects extends Response_Model
{

    public $table = 'ip_projects';
    public $primary_key = 'ip_projects.project_id';

    public function default_select()
    {
        $this->db->select('SQL_CALC_FOUND_ROWS *', FALSE);
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_projects.project_id');
    }

    public function default_join()
    {
        //$this->db->join('ip_projects', 'ip_projects.project_id = ip_client.project_id', 'left');
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_projects.client_id', 'left');
    }

    public function validation_rules()
    {
        return array(
            'project_name' => array(
                'field' => 'project_name',
                'label' => lang('project_name'),
                'rules' => 'required'
            ),
            'client_id' => array(
                'field' => 'client_id',
                'label' => lang('client'),
            )
        );
    }

}

?>