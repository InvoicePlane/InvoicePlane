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

class Mdl_Uploads extends Response_Model
{
    public $table = 'ip_uploads';
    public $primary_key = 'ip_uploads.upload_id';
    public $date_modified_field = 'uploaded_date';


    public function default_order_by()
    {
        $this->db->order_by('ip_uploads.upload_id ASC');
    }

    public function default_join()
    {
    }

    public function create($db_array = NULL)
    {
        $upload_id = parent::save(NULL, $db_array);

        return $upload_id;
    }

    public function get_quote_uploads($id)
    {
        $this->load->model('quotes/mdl_quotes');
        $quote = $this->mdl_quotes->get_by_id($id);
        $query = $this->db->query("Select file_name_new,file_name_original from ip_uploads where url_key = '" . $quote->quote_url_key . "'");
        $names = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                array_push($names, array(
                    'path' => getcwd() . '/uploads/customer_files/' . $row->file_name_new,
                    'filename' => $row->file_name_original));
            }
        }
        return $names;
    }

    public function get_invoice_uploads($id)
    {
        $this->load->model('invoices/mdl_invoices');
        $invoice = $this->mdl_invoices->get_by_id($id);
        $query = $this->db->query("Select file_name_new,file_name_original from ip_uploads where url_key = '" . $invoice->invoice_url_key . "'");

        $names = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                array_push($names, array(
                    'path' => getcwd() . '/uploads/customer_files/' . $row->file_name_new,
                    'filename' => $row->file_name_original));
            }
        }
        return $names;
    }

    public function delete($url_key, $filename)
    {
        $this->db->where('url_key', $url_key);
        $this->db->where('file_name_original', $filename);
        $this->db->delete('ip_uploads');
    }


    public function by_client($client_id)
    {
        $this->filter_where('ip_uploads.client_id', $client_id);
        return $this;
    }


}
