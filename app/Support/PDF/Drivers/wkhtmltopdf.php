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

namespace IP\Support\PDF\Drivers;

use IP\Support\PDF\PDFAbstract;
use Knp\Snappy\Pdf;

class wkhtmltopdf extends PDFAbstract
{
    protected $paperSize;

    protected $paperOrientation;

    public function download($html, $filename)
    {
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo $this->getOutput($html);
    }

    public function getOutput($html)
    {
        $pdf = $this->getPdf();

        return $pdf->getOutputFromHtml($html);
    }

    private function getPdf()
    {
        $pdf = new Pdf(config('ip.pdfBinaryPath'));
        $pdf->setOption('orientation', $this->paperOrientation);
        $pdf->setOption('page-size', $this->paperSize);
        $pdf->setOption('viewport-size', '1024x768');

        return $pdf;
    }

    public function save($html, $filename)
    {
        $pdf = $this->getPdf();
        $pdf->generateFromHtml($html, $filename);
    }
}