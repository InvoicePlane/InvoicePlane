<?php

namespace InvoicePlane\InvoicePlane;

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
 * Class ItemLegacy
 *
 * It should never be used. It's only here to prevent the recalculation of old invoices
 * for users who are not yet ready to update all their invoices to get the correct amounts.
 *
 * @author Michael Munger <mj@hph.io>
 */
class ItemInvoiceLegacy extends ItemBase
{

    /**
     * @return array
     */
    public function get_values()
    {
        $item_subtotal = $this->item_quantity * $this->item_price;
        $item_tax_total = $item_subtotal * ($this->item_tax_rate_percent / 100);
        $item_discount_total = $this->item_discount_amount * $this->item_quantity;
        $item_total = $item_subtotal + $item_tax_total - $item_discount_total;

        $db_array = [
            'item_id' => $this->item_id,
            'item_subtotal' => $item_subtotal,
            'item_tax_total' => $item_tax_total,
            'item_discount' => $item_discount_total,
            'item_total' => $item_total,
        ];

        return $db_array;
    }
}
