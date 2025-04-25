<?php

if (! defined('BASEPATH'))
{
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

#[AllowDynamicProperties]
class Mdl_Quote_Item_Amounts extends CI_Model
{
    /**
     * item_amount_id
     * item_id
     * item_subtotal (item_quantity * item_price)
     * item_tax_total
     * item_total ((item_quantity * item_price) + item_tax_total)
     *
     * @param $item_id
     * @param $global_discount
     */
    public function calculate($item_id, & $global_discount)
    {
        $this->load->model('quotes/mdl_quote_items');
        $item = $this->mdl_quote_items->get_by_id($item_id);

        $item_subtotal = $item->item_quantity * $item->item_price;

        // Discounts calculation - since v1.6.3
        if(config_item('legacy_calculation'))
        {
            $item_tax_total = $item_subtotal * ($item->item_tax_rate_percent / 100);
            $item_discount_total = $item->item_discount_amount * $item->item_quantity;
            $item_total = $item_subtotal + $item_tax_total - $item_discount_total;
        }
        else
        {
            $item_discount = 0.0; // For total & tax calculation after all discounts applied Proportionally by item
            if($global_discount['amount'] != 0 && $global_discount['items_subtotal'] != 0) // Prevent divide per 0
            {
                $item_discount = round($global_discount['amount'] * ($item_subtotal / $global_discount['items_subtotal']), 2);
            }
            if($global_discount['percent'] != 0) // Percent per default
            {
                $item_discount = round(($item_subtotal * ($global_discount['percent'] / 100)), 2);
            }

            $global_discount['item'] += $item_discount; // for Mdl_quote_amounts calculation
            $item_discount_total = $item->item_discount_amount * $item->item_quantity;
            $item_tax_total = ($item_subtotal - $item_discount - $item_discount_total) * ($item->item_tax_rate_percent / 100);
            $item_total = $item_subtotal - $item_discount - $item_discount_total + $item_tax_total;
        }

        $db_array = [
            'item_id'        => $item_id,
            'item_subtotal'  => $item_subtotal,
            'item_tax_total' => $item_tax_total,
            'item_discount'  => $item_discount_total,
            'item_total'     => $item_total,
        ];

        $this->db->where('item_id', $item_id);
        if ($this->db->get('ip_quote_item_amounts')->num_rows())
        {
            $this->db->where('item_id', $item_id);
            $this->db->update('ip_quote_item_amounts', $db_array);
        }
        else
        {
            $this->db->insert('ip_quote_item_amounts', $db_array);
        }
    }
}
