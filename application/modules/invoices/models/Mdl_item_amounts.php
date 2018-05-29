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

        // calculate net subtotal and discount
        if ($item->item_price_isgross == "1") {
          $item_subtotal = $item->item_quantity * $item->item_price / (1 + $item->item_tax_rate_percent / 100);
          $item_discount = $item->item_discount_amount * $item->item_quantity / (1 + $item->item_tax_rate_percent / 100);
        } else {
          $item_subtotal = $item->item_quantity * $item->item_price;
          $item_discount = $item->item_discount_amount * $item->item_quantity;
        }

        // calculate item tax
        if (get_setting('default_item_tax_after_discount') == 1) {
            // if tax applies after discount: first apply discount, then add tax
            $item_subtotal_recalc = $item_subtotal - $item_discount;
            $item_tax_total = $item_subtotal_recalc * ($item->item_tax_rate_percent / 100);
            $item_total = $item_subtotal_recalc + $item_tax_total;
        } else {
            // if discount applies after tax: first apply tax, then draw discount
            $item_tax_total = $item_subtotal * ($item->item_tax_rate_percent / 100);
            $item_subtotal_recalc = $item_subtotal + $item_tax_total;
            $item_total = $item_subtotal_recalc - $item_discount;
        }

        $db_array = array(
            'item_id' => $item_id,
            'item_subtotal' => $item_subtotal,
            'item_subtotal_recalc' => $item_subtotal_recalc,
            'item_tax_total' => $item_tax_total,
            'item_discount' => $item_discount,
            'item_total' => $item_total,
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
