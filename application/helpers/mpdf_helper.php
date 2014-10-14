<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/*
 * InvoicePlane
 * 
 * A free and open source web based invoicing system
 *
 * @package		InvoicePlane
 * @author		Kovah (www.kovah.de)
 * @copyright	Copyright (c) 2012 - 2014 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
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
        $mpdf->Output($filename . '.pdf', 'I');
    }
    else
    {
        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');
        
        return './uploads/temp/' . $filename . '.pdf';
    }
}

?>
