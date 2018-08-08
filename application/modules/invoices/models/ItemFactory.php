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
 * Factory class for getting either a legacy item, which calculates invoices incorrectly, or a proper one that
 * calculates them properly.
 *
 * @author Michael Munger <mj@hph.io>
 */
class ItemFactory
{

    /**
     * @param $item
     * @return \Item|\ItemLegacy
     */
    public function get_item($item)
    {
        switch ($item->item_discount_calc) {
            case '1':
                return new Item($item);
                break;
            case '0':
                return new ItemLegacy($item);
                break;
        }
    }
}
