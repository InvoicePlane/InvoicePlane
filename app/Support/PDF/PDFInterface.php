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

namespace FI\Support\PDF;

interface PDFInterface
{
    public function save($html, $filename);

    public function download($html, $filename);

    public function setPaperSize($paperSize);

    public function setPaperOrientation($paperOrientation);
}