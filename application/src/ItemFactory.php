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
 * Factory class for getting either a legacy item, which calculates invoices incorrectly, or a proper one that
 * calculates them properly.
 *
 * @author Michael Munger <mj@hph.io>
 */
class ItemFactory
{
    /**
     * @param $type
     * @param $item
     * @return mixed
     */
    public function get_item($type, $item)
    {
        switch ($type) {
            case 'invoice':
                return $this->getInvoiceObject($item);
                break;
            case 'quote':
                return $this->getQuoteObject($item);
                break;
        }
    }

    /**
     * @param $item
     * @return ItemInvoice|ItemInvoiceLegacy
     */
    public function getInvoiceObject($item)
    {
        switch ($item->item_discount_calc) {
            case '1':
                return new ItemInvoice($item);
                break;
            case '0':
                return new ItemInvoiceLegacy($item);
                break;
        }
    }

    /**
     * @param $item
     * @return ItemQuote|ItemQuoteLegacy
     */
    public function getQuoteObject($item)
    {
        switch ($item->item_discount_calc) {
            case '1':
                return new ItemQuote($item);
                break;
            case '0':
                return new ItemQuoteLegacy($item);
                break;
        }
    }
}
