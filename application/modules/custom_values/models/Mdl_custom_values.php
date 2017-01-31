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

class Mdl_Custom_Values extends MY_Model
{
    public $table = 'ip_custom_values';
    public $primary_key = 'ip_custom_values.custom_values_id';

    public function save_custom($client_id, $db_array)
    {
        $client_custom_id = null;

        $db_array['client_id'] = $client_id;

        $client_custom = $this->where('client_id', $client_id)->get();

        if ($client_custom->num_rows()) {
            $client_custom_id = $client_custom->row()->client_custom_id;
        }

        parent::save($client_custom_id, $db_array);
    }

    public function custom_tables()
    {
        return array(
            'ip_client_custom' => 'client',
            'ip_invoice_custom' => 'invoice',
            'ip_payment_custom' => 'payment',
            'ip_quote_custom' => 'quote',
            'ip_user_custom' => 'user'
        );
    }

    public function custom_types(){
        return array(
            'TEXT',
            'DATE',
            'BOOLEAN',
            'SINGLE-CHOICE',
            'MULTIPLE-CHOICE'
          );
    }

    public function get_by_fid($id)
    {
      $this->where('custom_values_field', $id);
      return $this->get();
    }

    public function get_grouped(){
      $this->db->select('count(custom_field_label) as count');
      $this->db->group_by('ip_custom_fields.custom_field_label');
      return $this->get();
    }

    public function default_select()
    {
        $this->db->select('ip_custom_fields.*,ip_custom_values.*', false);
    }

    public function default_join()
    {
        $this->db->join('ip_custom_fields', 'ip_custom_values.custom_values_field = ip_custom_fields.custom_field_id', 'left');
    }

    public function default_order_by()
    {
      //$this->db->group_by('ip_custom_fields.custom_field_label');
    }

    public function default_group_by()
    {
      //$this->db->group_by('ip_custom_values.custom_values_field');
    }
}
