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

namespace IP\Support\PDF;

abstract class PDFAbstract implements PDFInterface
{
    protected $paperSize;

    protected $paperOrientation;

    public function __construct()
    {
        $this->paperSize = config('ip.paperSize') ?: 'letter';
        $this->paperOrientation = config('ip.paperOrientation') ?: 'portrait';
    }

    public function setPaperSize($paperSize)
    {
        $this->paperSize = $paperSize;
    }

    public function setPaperOrientation($paperOrientation)
    {
        $this->paperOrientation = $paperOrientation;
    }
}