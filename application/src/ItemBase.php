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
 * Decorator class to make processing items more sane.
 *
 * @author Michael Munger <mj@hph.io>
 */
abstract class ItemBase implements ItemInterface
{
    protected $original_item = null;
    public $item_id = null;
    public $item_quantity = null;
    public $item_price = null;
    public $item_discount_amount = null;
    public $item_tax_rate_percent = null;

    /*    public $item_amount_id        = null;
    public $item_subtotal         = null;
    public $item_tax_total        = null;
    public $item_discount         = null;
    public $item_total            = null;
    public $product_id            = null;
    public $family_id             = null;
    public $product_sku           = null;
    public $product_name          = null;
    public $product_description   = null;
    public $product_price         = null;
    public $purchase_price        = null;
    public $provider_name         = null;
    public $tax_rate_id           = null;
    public $unit_id               = null;
    public $product_tariff        = null;

    public $item_tax_rate_id      = null;
    public $item_product_id       = null;
    public $item_date_added       = null;
    public $item_task_id          = null;
    public $item_name             = null;
    public $item_description      = null;
    public $item_discount_calc    = null;
    public $item_order            = null;
    public $item_is_recurring     = null;
    public $item_product_unit     = null;
    public $item_product_unit_id  = null;
    public $item_date             = null;
    public $item_tax_rate_name    = null;*/

    /**
     * ItemBase constructor.
     *
     * @param $item
     */
    public function __construct($item)
    {
        $this->original_item = $item;
        $this->item_id = $item->item_id;
        $this->item_quantity = $item->item_quantity;
        $this->item_price = $item->item_price;
        $this->item_discount_amount = $item->item_discount_amount;
        $this->item_tax_rate_percent = $item->item_tax_rate_percent;
    }

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
