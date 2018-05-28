<?php

/**
 * This file is part of FusionInvoice.
 *
 * (c) FusionInvoice, LLC <jessedterry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FI\Support\PDF\Drivers;

use FI\Support\PDF\PDFAbstract;
use Knp\Snappy\Pdf;

class wkhtmltopdf extends PDFAbstract
{
    protected $paperSize;

    protected $paperOrientation;

    private function getPdf()
    {
        $pdf = new Pdf(config('fi.pdfBinaryPath'));
        $pdf->setOption('orientation', $this->paperOrientation);
        $pdf->setOption('page-size', $this->paperSize);
        $pdf->setOption('viewport-size', '1024x768');

        return $pdf;
    }

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

    public function save($html, $filename)
    {
        $pdf = $this->getPdf();
        $pdf->generateFromHtml($html, $filename);
    }
}