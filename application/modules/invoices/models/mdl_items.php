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

class Mdl_Items extends Response_Model
{
    public $table = 'ip_invoice_items';
    public $primary_key = 'ip_invoice_items.item_id';
    public $date_created_field = 'item_date_added';

    public function get_items_and_replace_vars($invoice_id, $invoice_date_created = 'now')
    {
        $items = array();
        $query = $this->where('invoice_id', $invoice_id)->get();

        foreach ($query->result() as $item) {
            $item->item_name = $this->parse_item($item->item_name, $invoice_date_created);
            $item->item_description = $this->parse_item($item->item_description, $invoice_date_created);
            $items[] = $item;
        }
        return $items;
    }

    private function parse_item($string, $invoice_date_created)
    {
        if (preg_match_all('/{{{(?<format>[yYmMdD])(?:(?<=[Yy])ear|(?<=[Mm])onth|(?<=[Dd])ay)(?:(?<operation>[-+])(?<amount>[1-9]+))?}}}/m',
            $string, $template_vars, PREG_SET_ORDER)) {
            try {
                $formattedDate = new DateTime($invoice_date_created);
            } catch (Exception $e) { // If creating a date based on the invoice_date_created isn't possible, use current date
                $formattedDate = new DateTime();
            }

            /* Calculate the date first, before starting replacing the variables */
            foreach ($template_vars as $var) {
                if (!isset($var['operation'], $var['amount'])) {
                    continue;
                }

                if ($var['operation'] == '-') {
                    $formattedDate->sub(new DateInterval('P' . $var['amount'] . strtoupper($var['format'])));
                } else {
                    if ($var['operation'] == '+') {
                        $formattedDate->add(new DateInterval('P' . $var['amount'] . strtoupper($var['format'])));
                    }
                }
            }

            /* Let's replace all variables */
            foreach ($template_vars as $var) {
                $string = str_replace($var[0], $formattedDate->format($var['format']), $string);
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
        $this->db->join('ip_invoice_item_amounts', 'ip_invoice_item_amounts.item_id = ip_invoice_items.item_id',
            'left');
        $this->db->join('ip_tax_rates AS item_tax_rates',
            'item_tax_rates.tax_rate_id = ip_invoice_items.item_tax_rate_id', 'left');
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

    public function save($invoice_id, $id = null, $db_array = null)
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
        // Get item:
        // the invoice id is needed to recalculate invoice amounts
        // and the task id to update status if the item refers a task
        $query = $this->db->get_where($this->table,
            array('item_id' => $item_id));
        if ($query->num_rows() == 0) {
            return null;
        }

        $row = $query->row();
        $invoice_id = $row->invoice_id;

        // Delete the item
        parent::delete($item_id);

        // Delete the item amounts
        $this->db->where('item_id', $item_id);
        $this->db->delete('ip_invoice_item_amounts');

        // Recalculate invoice amounts
        $this->load->model('invoices/mdl_invoice_amounts');
        $this->mdl_invoice_amounts->calculate($invoice_id);
        return $row;
    }
}
