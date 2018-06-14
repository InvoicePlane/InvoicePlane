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

use Dompdf\Dompdf as PDF;
use Dompdf\Options;
use IP\Support\PDF\PDFAbstract;

class domPDF extends PDFAbstract
{
    public function save($html, $filename)
    {
        file_put_contents($filename, $this->getOutput($html));
    }

    public function getOutput($html)
    {
        $pdf = $this->getPdf($html);

        return $pdf->output();
    }

    private function getPdf($html)
    {
        $options = new Options();

        $options->setTempDir(storage_path('/'));
        $options->setFontDir(storage_path('/'));
        $options->setFontCache(storage_path('/'));
        $options->setLogOutputFile(storage_path('dompdf_log'));
        $options->setIsRemoteEnabled(true);
        $options->setIsHtml5ParserEnabled(true);
        $options->setIsFontSubsettingEnabled(true);

        $pdf = new PDF($options);

        $pdf->setPaper($this->paperSize, $this->paperOrientation);
        $pdf->loadHtml($html);

        $pdf->render();

        return $pdf;
    }

    public function download($html, $filename)
    {
        $response = response($this->getOutput($html));

        $response->header('Content-Type', 'application/pdf');
        $response->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response->send();
    }
}