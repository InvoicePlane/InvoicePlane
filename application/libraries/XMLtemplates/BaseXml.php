<?php

defined('BASEPATH') || exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 * @Note        Base CLASS for the e-Invoices XML Templates
 *              Allow Dynamic properties by stdClass ;)
 *
 * @Need in     Facturxv10Xml and Ublv24Xml generator classes
 */

/**
 * Class BaseXml
 */
class BaseXml extends stdClass
{
    public $invoice;
    public $items;
    public $doc;
    public $filename;
    public $currencyCode;
    public $root;
    public $notax;
    public $item_decimals = 2;
    public $decimal_places = 2;
    public $legacy_calculation = false;
    public $itemsSubtotalGroupedByTaxPercent = [];

    public function __construct($params)
    {
        $this->invoice            = $params['invoice'];
        $this->items              = $params['items'];
        $this->filename           = $params['filename'];
        $this->currencyCode       = get_setting('currency_code');
        $this->item_decimals      = get_setting('default_item_decimals');
        $this->decimal_places     = get_setting('tax_rate_decimal_places');
        $this->legacy_calculation = config_item('legacy_calculation');

        $this->set_invoice_discount_amount_total();
        $this->setItemsSubtotalGroupedByTaxPercent();
        $this->notax = empty($this->itemsSubtotalGroupedByTaxPercent);
    }

    public function xml()
    {
        $this->doc = new DOMDocument('1.0', 'UTF-8');
        $this->doc->preserveWhiteSpace = false;
        $this->doc->formatOutput = IP_DEBUG;
    }

    /*
     * Add missing some discount invoice vars
     * [for factur-x validation](ecosio.com/en/peppol-and-xml-document-validator)
     * [for ubl 2.4 validation](validator.invoice-portal.de)
     *
     * invoice_subtotal                    Scope TaxBasisTotalAmount
     * invoice_discount_amount_total       Scope addSpecifiedTradeAllowanceCharge_discount()
     * invoice_discount_amount_subtotal    Scope SpecifiedTradeAllowanceCharge > ActualAmount>
     */
    public function set_invoice_discount_amount_total()
    {
        $item_discount = $item_subtotal = $discount = 0.0;

        foreach ($this->items as $item)
        {
            $item_discount += $item->item_discount;
            $item_subtotal += $item->item_subtotal;
        }

        if($this->invoice->invoice_discount_amount > 0)
        {
            $discount = $this->invoice->invoice_discount_amount;
        }
        elseif($this->invoice->invoice_discount_percent > 0)
        {
            $discount = $item_subtotal * ($this->invoice->invoice_discount_percent / 100);
        }
        $this->invoice->invoice_subtotal                 = $this->formattedFloat($item_subtotal);
        $this->invoice->items_discount_amount_total      = $this->formattedFloat($item_discount); // ublv24
        $this->invoice->invoice_discount_amount_total    = $this->formattedFloat($item_discount + $discount);
        $this->invoice->invoice_discount_amount_subtotal = $this->formattedFloat($discount);
    }

    public function setItemsSubtotalGroupedByTaxPercent()
    {
        $result = [];
        foreach ($this->items as $item)
        {
            if ($item->item_tax_rate_percent == 0)
            {
                continue;
            }

            if ( ! isset($result[$item->item_tax_rate_percent]))
            {
                $result[$item->item_tax_rate_percent] = [0, 0];
            }

            $result[$item->item_tax_rate_percent] = [
                $result[$item->item_tax_rate_percent][0] += ($item->item_total - $item->item_tax_total),
                $result[$item->item_tax_rate_percent][1] += $item->item_subtotal, // without discounts
            ];

        }
        $this->itemsSubtotalGroupedByTaxPercent = $result; // help to dispatch invoice global discount tax rate + same for vat's of invoices
    }

    // ===========================================================================
    // elements helpers
    // ===========================================================================

    /**
     * @return string|null
     */
    public function formattedDate($date, $format = 'Ymd')
    {
        if ($date)
        {
            $date = DateTime::createFromFormat('Y-m-d', $date);
            return $date->format($format);
        }
        return '';
    }

    public function formattedFloat($amount, $nb_decimals = 2)
    {
        return number_format(floatval($amount), $nb_decimals, '.', '');
    }

    public function formattedQuantity($qty)
    {
        return number_format(floatval($qty), $this->item_decimals, '.', '');
    }

}
