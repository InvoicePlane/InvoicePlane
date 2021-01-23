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
 * Represents an invoice item.
 *
 * @author Michael Munger <mj@hph.io>
 */
class ItemInvoice extends ItemBase
{

    public $invoice_id = null;

    /**
     * Invoice Item constructor.
     *
     * @param $item
     */
    public function __construct($item)
    {
        parent::__construct($item);

        $this->invoice_id = $item->invoice_id;

    }
}
