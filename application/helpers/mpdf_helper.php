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
 * @copyright	Copyright (c) 2012 - 2015 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 *
 */

function pdf_create($html, $filename, $stream = true, $password = null, $isInvoice = null, $isGuest = null, $zugferd_invoice = false, $associatedFiles = null)
{
	$CI = & get_instance();
	
    // ---it---inizio
    // Speciale motore stampa dompdf: primo motore stampa FI, poi tolto dalla versione originale e mantenuto nella versione italiana.
    // Questo motore PDF, infatti, mantiene il risultato visualizzato nell'anteprima PDF (a differenza del nuovo motore mPDF).
    if ($CI->mdl_settings->setting('it_print_engine') == 'dompdf')
    {
        return pdf_create_dompdf($html, $filename, $stream);
    }
    // ---it---fine

    // Get the invoice from the archive if available
    $invoice_array = array();

    // mPDF loading
    define('_MPDF_TEMP_PATH', FCPATH . 'uploads/temp/mpdf/');
    define('_MPDF_TTFONTDATAPATH', FCPATH . 'uploads/temp/mpdf/');

    require_once(FCPATH . 'vendor/mpdf/mpdf/mpdf.php');

    $mpdf = new mPDF();

    // mPDF configuration
    $mpdf->useAdobeCJK = true;
    $mpdf->autoScriptToLang = true;

    if (IP_DEBUG) {
        // Enable image error logging
        $mpdf->showImageErrors = true;
    }

    // Include zugferd if enabled
    if ($zugferd_invoice) {
        $CI->load->helper('zugferd');
        $mpdf->PDFA = true;
        $mpdf->PDFAauto = true;
        $mpdf->SetAdditionalRdf(zugferd_rdf());
        $mpdf->SetAssociatedFiles($associatedFiles);
    }

    // Set a password if set for the voucher
    if (!empty($password)) {
        $mpdf->SetProtection(array('copy', 'print'), $password, $password);
    }

    // Check if the archive folder is available
    if (!(is_dir('./uploads/archive/') || is_link('./uploads/archive/'))) {
        mkdir('./uploads/archive/', '0777');
    }

    // Set the footer if voucher is invoice and if set in settings
    if ($isInvoice && !empty($CI->mdl_settings->settings['pdf_invoice_footer'])) {
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_invoice_footer'] . '</div>');
    }

    $mpdf->WriteHTML($html);

    if ($isInvoice) {

        foreach (glob(UPLOADS_FOLDER . 'archive/*' . $filename . '.pdf') as $file) {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) && !is_null($isGuest)) {
            rsort($invoice_array);

            if ($stream) {
                return $mpdf->Output($filename . '.pdf', 'I');
            } else {
                return $invoice_array[0];
            }
        }

        $archived_file = UPLOADS_FOLDER . 'archive/' . date('Y-m-d') . '_' . $filename . '.pdf';
        $mpdf->Output($archived_file, 'F');

        if ($stream) {
            return $mpdf->Output($filename . '.pdf', 'I');
        } else {
            return $archived_file;
        }
    }

    // If $stream is true (default) the PDF will be displayed directly in the browser
    // otherwise will be returned as a download
    if ($stream) {
        return $mpdf->Output($filename . '.pdf', 'I');
    } else {

        $mpdf->Output(UPLOADS_FOLDER . 'temp/' . $filename . '.pdf', 'F');

        // Housekeeping
        // Delete any files in temp/ directory that are >1 hrs old
        $interval = 3600;
        if ($handle = @opendir(preg_replace('/\/$/', '', './uploads/temp/'))) {
            while (false !== ($file = readdir($handle))) {
                if (($file != '..') && ($file != '.') && !is_dir($file) && ((filemtime('./uploads/temp/' . $file) + $interval) < time()) && (substr($file, 0, 1) !== '.') && ($file != 'remove.txt')) { // mPDF 5.7.3
                    unlink(UPLOADS_FOLDER . 'temp/' . $file);
                }
            }
            closedir($handle);
        }

        return UPLOADS_FOLDER . 'temp/' . $filename . '.pdf';

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
