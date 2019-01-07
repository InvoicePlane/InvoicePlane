<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Create a PDF
 *
 * @param $html
 * @param $filename
 * @param bool $stream
 * @param null $password
 * @param null $isInvoice
 * @param null $is_guest
 * @param bool $zugferd_invoice
 * @param null $associated_files
 *
 * @return string
 * @throws \Mpdf\MpdfException
 */
function pdf_create(
    $html,
    $filename,
    $stream = true,
    $password = null,
    $isInvoice = null,
    $is_guest = null,
    $zugferd_invoice = false,
    $associated_files = null
) {
    $CI = &get_instance();

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
    if (!defined('_MPDF_TEMP_PATH')) {
        define('_MPDF_TEMP_PATH', UPLOADS_TEMP_MPDF_FOLDER);
        define('_MPDF_TTFONTDATAPATH', UPLOADS_TEMP_MPDF_FOLDER);
    }

    $mpdf = new \Mpdf\Mpdf();

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
        $mpdf->SetAdditionalXmpRdf(zugferd_rdf());
        $mpdf->SetAssociatedFiles($associated_files);
    }

    // Set a password if set for the voucher
    if (!empty($password)) {
        $mpdf->SetProtection(array('copy', 'print'), $password, $password);
    }

    // Check if the archive folder is available
    if (!(is_dir(UPLOADS_ARCHIVE_FOLDER) || is_link(UPLOADS_ARCHIVE_FOLDER))) {
        mkdir(UPLOADS_ARCHIVE_FOLDER, '0777');
    }

    // Set the footer if voucher is invoice and if set in settings
    if (!empty($CI->mdl_settings->settings['pdf_invoice_footer']) && $isInvoice) {
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_invoice_footer'] . '</div>');
    }

    // Set the footer if voucher is quote and if set in settings
    if (!empty($CI->mdl_settings->settings['pdf_quote_footer']) && strpos($filename, trans('quote')) !== false) {
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_quote_footer'] . '</div>');
    }

    // Watermark
    if (get_setting('pdf_watermark')) {
        $mpdf->showWatermarkText = true;
    }

    $mpdf->WriteHTML((string) $html);

    if ($isInvoice) {

        foreach (glob(UPLOADS_ARCHIVE_FOLDER . '*' . $filename . '.pdf') as $file) {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) && !is_null($is_guest)) {
            rsort($invoice_array);

            if ($stream) {
                return $mpdf->Output($filename . '.pdf', 'I');
            } else {
                return $invoice_array[0];
            }
        }

        $archived_file = UPLOADS_ARCHIVE_FOLDER . date('Y-m-d') . '_' . $filename . '.pdf';
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
        $mpdf->Output(UPLOADS_TEMP_FOLDER . $filename . '.pdf', 'F');
        return UPLOADS_TEMP_FOLDER . $filename . '.pdf';
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