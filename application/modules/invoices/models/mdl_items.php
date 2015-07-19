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

class Mdl_Items extends Response_Model
{
    public $table = 'ip_invoice_items';
    public $primary_key = 'ip_invoice_items.item_id';
    public $date_created_field = 'item_date_added';

	 public function get_items_and_replace_vars($invoice_id)
	 {
		 $items = array();
		 $query = $this->where('invoice_id', $invoice_id)->get();

		 foreach($query->result() as $item) {
			 $item->item_description = $this->parse_item($item->item_description);
			 $items[] = $item;
		 }
		 return $items;
	 }

    private function parse_item($string)
    {
        if (preg_match_all('/{{{([^{|}]*)}}}/', $string, $template_vars)) {
            foreach ($template_vars[1] as $var) {
                switch ($var) {
                    case 'nextYear':
                        $replace = date('Y') + 1;
                        break;
                    default:
                        $replace = '';
                }

                $string = str_replace('{{{' . $var . '}}}', $replace, $string);
            }
        }

        return $string;
    }

    public function default_select()
    {
        $this->db->select('ip_invoice_item_amounts.*, ip_invoice_items.*, item_tax_rates.tax_rate_percent AS item_tax_rate_percent');
    }

    public function default_order_by()
    {
        $this->db->order_by('ip_invoice_items.item_order');
    }

    public function default_join()
    {
        $this->db->join('ip_invoice_item_amounts', 'ip_invoice_item_amounts.item_id = ip_invoice_items.item_id', 'left');
        $this->db->join('ip_tax_rates AS item_tax_rates', 'item_tax_rates.tax_rate_id = ip_invoice_items.item_tax_rate_id', 'left');
    }

    public function validation_rules()
    {
        return array(
            'invoice_id' => array(
                'field' => 'invoice_id',
                'label' => lang('invoice'),
                'rules' => 'required'
            ),
            'item_name' => array(
                'field' => 'item_name',
                'label' => lang('item_name'),
                'rules' => 'required'
            ),
            'item_description' => array(
                'field' => 'item_description',
                'label' => lang('description')
            ),
            'item_quantity' => array(
                'field' => 'item_quantity',
                'label' => lang('quantity'),
                'rules' => 'required'
            ),
            'item_price' => array(
                'field' => 'item_price',
                'label' => lang('price'),
                'rules' => 'required'
            ),
            'item_tax_rate_id' => array(
                'field' => 'item_tax_rate_id',
                'label' => lang('item_tax_rate')
            )
        );
    }

    public function save($invoice_id, $id = NULL, $db_array = NULL)
    {
        $id = parent::save($id, $db_array);

        $this->load->model('invoices/mdl_item_amounts');
        $this->mdl_item_amounts->calculate($id);

        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);

        return $id;
    }

    public function delete($item_id)
    {
        // Get the invoice id so we can recalculate invoice amounts
        $this->db->select('invoice_id');
        $this->db->where('item_id', $item_id);
        $invoice_id = $this->db->get('ip_invoice_items')->row()->invoice_id;

        // Delete the item
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('ip_invoice_item_amounts');

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);
    }

}
