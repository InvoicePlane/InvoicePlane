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
 * Class ItemQuote
 *
 * @package InvoicePlane\InvoicePlane
 * @author  Michael Munger <mj@hph.io>
 */
class ItemQuote extends ItemBase
{
    public $quote_id = null;

    public function __construct($item)
    {
        parent::__construct($item);

        $this->quote_id = $item->quote_id;

    }
}
