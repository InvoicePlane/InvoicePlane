<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Class Item
 * Represents a single invoice item
 *
 * @author Michael Munger <mj@hph.io>
 */
class QuoteItem extends QuoteItemBase
{

    /**
     * @return array
     */
    public function get_values()
    {
        $db_array = [];
        $db_array['item_id'] = $this->item_id;
        $db_array['item_subtotal'] = $this->get_subtotal();
        $db_array['item_tax_total'] = $this->get_tax_total();
        $db_array['item_discount'] = $this->get_item_discount_total();
        $db_array['item_total'] = $this->get_item_total();

        return $db_array;
    }

    /**
     * Calculates subtotals using correct types and math.
     *
     * @return float|int
     */
    public function get_subtotal()
    {
        $quantity = (int)round($this->item_quantity * 100, 2);
        $price = (int)round($this->item_price * 100, 2);
        $sub_total = $quantity * $price / 100 / 100;
        return $sub_total;
    }

    /**
     * Calculates discount total using correct types and math.
     *
     * @return float|int
     */
    public function get_item_discount_total()
    {
        $quantity = (int)round($this->item_quantity * 100, 2);
        $discount = (int)round($this->item_discount_amount * 100, 2);
        $tax_total = $quantity * $discount / 100 / 100;
        return $tax_total;
    }

    /**
     * Calculates tax total using correct types and math.
     *
     * @return float|int
     */
    public function get_tax_total()
    {
        $item_taxable_amount = ($this->get_subtotal() - $this->get_item_discount_total()) * 100;
        $item_tax_total = ($item_taxable_amount * $this->item_tax_rate_percent) / 100 / 100;
        return $item_tax_total;
    }

    /**
     * Gets the item's properly calculated total
     *
     * @return float|int
     */
    public function get_item_total()
    {
        return $this->get_subtotal() + $this->get_tax_total() - $this->get_item_discount_total();
    }
}
