<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * FusionInvoice
 * 
 * A free and open source web based invoicing system
 *
 * @package		FusionInvoice
 * @author		Jesse Terry
 * @copyright	Copyright (c) 2012 - 2013 FusionInvoice, LLC
 * @license		http://www.fusioninvoice.com/license.txt
 * @link		http://www.fusioninvoice.com
 * 
 */

function pdf_create($html, $filename, $stream = TRUE)
{

    require_once(APPPATH . 'helpers/mpdf/mpdf.php');

    $mpdf = new mPDF();

    $mpdf->SetAutoFont();

    $mpdf->WriteHTML($html);

    if ($stream)
    {
        $mpdf->Output($filename . '.pdf', 'D');
    }
    else
    {
        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');
        
        return './uploads/temp/' . $filename . '.pdf';
    }
}

?>