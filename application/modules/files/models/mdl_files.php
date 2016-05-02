<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Invoice Plane
 *
 * A free and opensource web based invoicing system
 *
 * @package     InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @contributor Longka (www.longkyanh.info)
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

class Mdl_Files extends Response_Model
{
    public $table = 'ip_files';
    public $primary_key = 'ip_files.file_id';
    public $date_created_field = 'file_date_created';
    public $date_modified_field = 'file_date_modified';

    public function default_join()
    {
        $this->db->join('ip_clients', 'ip_clients.client_id = ip_files.client_id', 'left');
    }

    public function create($data)
    {
        $this->db->insert('ip_files', $data);

        return $this->db->insert_id();
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_files.file_date_modified DESC');
    }

    public function delete($file_id)
    {
        parent::delete($file_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    public function by_client($client_id)
    {
        $this->filter_where('ip_files.client_id', $client_id);
        return $this;
    }
}
