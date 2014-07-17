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
	// ---it---inizio
	// Speciale motore stampa dompdf: primo motore stampa FI, poi tolto dalla versione originale e mantenuto nella versione italiana.
	// Questo motore PDF, infatti, mantiene il risultato visualizzato nell'anteprima PDF (a differenza del nuovo motore mPDF).
	$CI = & get_instance();
	if ($CI->mdl_settings->setting('it_print_engine') == 'dompdf')
	{
		return pdf_create_dompdf($html, $filename, $stream);
	}
	// ---it---fine
	
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

// ---it---inizio Utilizza ancora dompdf: mpdf d� problemi (test con modello fattura s2 software)
/*
 * FusionInvoice
*
* A free and open source web based invoicing system
*
* @package		FusionInvoice
* @author		Jesse Terry
* @copyright	Copyright (c) 2012 - 2013, Jesse Terry
* @license		http://www.fusioninvoice.com/license.txt
* @link		http://www.fusioninvoice.
*
*/
function pdf_create_dompdf($html, $filename, $stream = TRUE) {

	require_once(APPPATH . 'helpers/dompdf/dompdf_config.inc.php');

	$dompdf = new DOMPDF();

	$dompdf->load_html($html);

	$dompdf->set_paper('a4');	//---it---
	$dompdf->render();

	if ($stream) {

		$dompdf->stream($filename . '.pdf');

	}

	else {

		$CI =& get_instance();

		$CI->load->helper('file');

		write_file('./uploads/temp/' . $filename . '.pdf', $dompdf->output());

		return './uploads/temp/' . $filename . '.pdf';
	}

}
//---it---fine

?>