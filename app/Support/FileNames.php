<?php

/**
 * InvoicePlane
 *
 * @package     InvoicePlane
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (C) 2014 - 2018 InvoicePlane
 * @license     https://invoiceplane.com/license
 * @link        https://invoiceplane.com
 *
 * Based on FusionInvoice by Jesse Terry (FusionInvoice, LLC)
 */

namespace FI\Support;

class FileNames
{
    public static function invoice($invoice)
    {
        return trans('fi.invoice') . '_' . str_replace('/', '-', $invoice->number) . '.pdf';
    }

    public static function quote($quote)
    {
        return trans('fi.quote') . '_' . str_replace('/', '-', $quote->number) . '.pdf';
    }
}