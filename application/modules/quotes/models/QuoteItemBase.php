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
 * Decorator class to make processing items more sane.
 *
 * @author Michael Munger <mj@hph.io>
 */
abstract class QuoteItemBase implements QuoteItemInterface
{
    private $original_item         = null;

    public $item_amount_id        = null;
    public $item_id               = null;
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
    public $quote_id              = null;
    public $item_tax_rate_id      = null;
    public $item_product_id       = null;
    public $item_date_added       = null;
    public $item_name             = null;
    public $item_description      = null;
    public $item_quantity         = null;
    public $item_price            = null;
    public $item_discount_amount  = null;
    public $item_discount_calc    = null;
    public $item_order            = null;
    public $item_is_recurring     = null;
    public $item_product_unit     = null;
    public $item_product_unit_id  = null;
    public $item_tax_rate_percent = null;
    public $item_tax_rate_name    = null;

    /**
     * Item constructor.
     * @param $item
     */
    public function __construct($item)
    {
        // @TODO For some reason, saving quote items sometimes lead to errors because vars like $item->product_id or $item->family_id are not available and thus cannot be set correctly for $this
        // The loop should fix this by asking the $item if a property defined by the class exists and saves it if available.
        // The approach with defining each property (see commented code) is working well for invoices but not quotes?!
        foreach (get_object_vars($this) as $key => $var) {
            $this->{$key} = isset($item->{$key}) ? $item->{$key} : null;
        }

        //$this->original_item          = $item;
        //$this->item_amount_id         = $item->item_amount_id;
        //$this->item_id                = $item->item_id;
        //$this->item_subtotal          = $item->item_subtotal;
        //$this->item_tax_total         = $item->item_tax_total;
        //$this->item_discount          = $item->item_discount;
        //$this->item_total             = $item->item_total;
        //$this->product_id             = $item->product_id;
        //$this->family_id              = $item->family_id;
        //$this->product_sku            = $item->product_sku;
        //$this->product_name           = $item->product_name;
        //$this->product_description    = $item->product_description;
        //$this->product_price          = $item->product_price;
        //$this->purchase_price         = $item->purchase_price;
        //$this->provider_name          = $item->provider_name;
        //$this->tax_rate_id            = $item->tax_rate_id;
        //$this->unit_id                = $item->unit_id;
        //$this->product_tariff         = $item->product_tariff;
        //$this->quote_id               = $item->quote_id;
        //$this->item_tax_rate_id       = $item->item_tax_rate_id;
        //$this->item_product_id        = $item->item_product_id;
        //$this->item_date_added        = $item->item_date_added;
        //$this->item_name              = $item->item_name;
        //$this->item_description       = $item->item_description;
        //$this->item_quantity          = $item->item_quantity;
        //$this->item_price             = $item->item_price;
        //$this->item_discount_amount   = $item->item_discount_amount;
        //$this->item_discount_calc     = $item->item_discount_calc;
        //$this->item_order             = $item->item_order;
        //$this->item_is_recurring      = $item->item_is_recurring;
        //$this->item_product_unit      = $item->item_product_unit;
        //$this->item_product_unit_id   = $item->item_product_unit_id;
        //$this->item_tax_rate_percent  = $item->item_tax_rate_percent;
        //$this->item_tax_rate_name     = $item->item_tax_rate_name;
    }
}
