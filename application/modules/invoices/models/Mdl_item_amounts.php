<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Mdl_Item_Amounts
 */
class Mdl_Item_Amounts extends CI_Model
{
    /**
     * item_amount_id
     * item_id
     * item_subtotal (item_quantity * item_price)
     * item_tax_total
     * item_total ((item_quantity * item_price) + item_tax_total)
     *
     * @param $item_id
     */
    public function calculate($item_id)
    {
        $this->load->model('invoices/mdl_items');
        $item = $this->mdl_items->get_by_id($item_id);

        $item_total = 0;
        $item_subtotal = 0;
        $item_tax_total = 0;
        $item_discount_total = 0;

        // If the setting is set to 0 or 'apply_before_item_tax', 
        // we calculate the item tax on the net amount.
        // total: ((subtotal - discount) + ((subtotal - discount) / 100 * tax_percent))
        if(get_setting('default_invoice_tax_rate_placement') === "0") {

            $item_subtotal = $item->item_quantity * $item->item_price;
            $item_discount_total = ($item->item_discount_amount * $item->item_quantity) + ((($item->item_price / 100) * $item->item_discount_percent) * $item->item_quantity);
            $item_discounted = $item_subtotal - $item_discount_total;
            $item_tax_total = $item_discounted * ($item->item_tax_rate_percent / 100); 
            $item_total = $item_discounted + $item_tax_total;
        
        // If the setting is set to 1 or 'apply_after_item_tax',
        // we calculate an offset 'x' to discount, to equal final price - 'real discount'
        // total: (subtotal + tax_amount) - discount = (price - x) + ((price - x) / 100 * tax_perc)
        } else {

            $temp_subtotal = $item->item_quantity * $item->item_price;
            $temp_taxed = $temp_subtotal * ($item->item_tax_rate_percent / 100); 
            $temp_total = $temp_subtotal + $temp_taxed;
            $temp_discount = ($item->item_discount_amount * $item->item_quantity) + ((($temp_total / 100) * $item->item_discount_percent) * $item->item_quantity);

            // let k be (price - x), where x is the offset amount
            $k_value = (100 / (100 + $item->item_tax_rate_percent)) * ($temp_subtotal + $temp_taxed - $temp_discount);

            // reverse the formula and get the offset value
            $x_value = $temp_subtotal - $k_value;

            $item_subtotal = ($temp_subtotal - $x_value);
            $item_tax_total = (($temp_subtotal - $x_value) / 100 * $item->item_tax_rate_percent);
            $item_total = $item_subtotal + $item_tax_total;
            $item_discount_total = $temp_discount;
        }

        $db_array = array(
            'item_id' => $item_id,
            'item_subtotal' => $item_subtotal,
            'item_tax_total' => $item_tax_total,
            'item_discount' => $item_discount_total,
            'item_total' => $item_total
        );

        $this->db->where('item_id', $item_id);
        if ($this->db->get('ip_invoice_item_amounts')->num_rows()) {
            $this->db->where('item_id', $item_id);
            $this->db->update('ip_invoice_item_amounts', $db_array);
        } else {
            $this->db->insert('ip_invoice_item_amounts', $db_array);
        }
    }

}
