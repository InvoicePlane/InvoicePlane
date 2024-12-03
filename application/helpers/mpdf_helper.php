<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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
 * @param $pdf_stamp	 
 *   default "", no stamp. true with default stamp, 
 *   can also use a filename for multiple different stamps
 *
 * @return string
 * @throws \Mpdf\MpdfException
 */

use mikehaertl\pdftk\Pdf;

function pdf_create(
    $html,
    $filename,
    $stream = true,
    $password = null,
    $isInvoice = null,
    $is_guest = null,
    $zugferd_invoice = false,
    $associated_files = null,

    $pdf_stamp = "",
    $additionalFooter =""
) {
    $CI = &get_instance();

    // Get the invoice from the archive if available
    $invoice_array = array();

    // mPDF loading
    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
    ]);

    // size, margin by chrissie, what is this unit? it was trial and error
    $invoiceNrAndPageOnFooter = env('INVOICE_PAGE_FOOTER');
    if ($invoiceNrAndPageOnFooter == true ) {
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',
        'margin_left'   => 19,
        'margin_right'  => 10,
        'margin_top'    => 40,
        'margin_bottom' => 22,
        'margin_header' => 0,
        'margin_footer' => 7,
        'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
        ]);
    } else {
        $mpdf = new \Mpdf\Mpdf(['format' => 'A4',
        'margin_left'   => 19,
        'margin_right'  => 10,
        'margin_top'    => 10,
        'margin_bottom' => 10,
        'margin_header' => 0,
        'margin_footer' => 15,
        'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
        ]);
    }

    // how to add new font by chrissie
    //./vendor/mpdf/mpdf/ttfonts/Raleway-Medium.ttf
    // change default dejavusanscondensed
    // to raleway - your mileage may vary
    $mpdf->fontdata=[];
    $mpdf->fontdata['dejavusanscondensed'] =
    [
        'R' => 'Raleway-Medium.ttf',
        'I' => 'Raleway-Italic.ttf',
        'B' => 'Raleway-Bold.ttf',
    ];
    // dejavuserifcondensed needed for watermark
    $mpdf->fontdata['dejavuserifcondensed'] =
    [
        'R' => 'Raleway-Medium.ttf',
        'I' => 'Raleway-Italic.ttf',
        'B' => 'Raleway-Bold.ttf',
    ];


    // mPDF configuration
    $mpdf->useAdobeCJK = true;
    $mpdf->autoScriptToLang = true;
    $mpdf->autoVietnamese = true;
    $mpdf->autoArabic = true;
    $mpdf->autoLangToFont = true;

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

    // by chrissie : special page nr footer and addidional footer
    if ($isInvoice) {
        $f="";
        if (!empty($additionalFooter)) {
	        $f .= '<div id="footer"><p align="center">'.$additionalFooter.'</p></div>';
        }
        if ($invoiceNrAndPageOnFooter == true) {
            $my_invoice_nr = "";
            if (!empty($CI->load->_ci_cached_vars['invoice']->invoice_number))
              $my_invoice_nr = "Rechnung Nr. ".$CI->load->_ci_cached_vars['invoice']->invoice_number." / ";
            $f .= '<div id="footer"><p align=right>'.$my_invoice_nr.' Seite {PAGENO} von {nbpg}</p></div>';
        }
        if (!empty ($f)) {
	    $mpdf->setAutoBottomMargin = 'stretch';
            $mpdf->SetHTMLFooter($f);
	}
    }
    // ^end


    // Watermark
    if (get_setting('pdf_watermark')) {
        $mpdf->showWatermarkText = true;
    }

    $mpdf->WriteHTML((string) $html);

    if ($isInvoice) {
	// invoice copy by chrissie with watermark
	$invoice_copy = env('INVOICE_COPY');
	$invoice_copy_watermark = env('INVOICE_COPY_WATERMARK');

	// only returne archived files when no copy - todo improfe!
	if ($invoice_copy != true) {
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
	}

        // generate new pdf
        $archived_file = UPLOADS_ARCHIVE_FOLDER . date('Y-m-d') . '_' . $filename . '.pdf';
        $mpdf->Output($archived_file, 'F');

	if ($invoice_copy == true) {
		$archived_file_copy = UPLOADS_ARCHIVE_FOLDER . date('Y-m-d') . '_' . $filename . '-copy.pdf';
		$xpdf = new \Mpdf\Mpdf([
		    'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
		]);
		$xpdf->SetWatermarkText($invoice_copy_watermark);
		$xpdf->showWatermarkText = true;
		$pagecount = $xpdf->SetSourceFile($archived_file);
		$tplId = $xpdf->importPage($pagecount);
		$xpdf->useTemplate($tplId);
		$xpdf->Output($archived_file_copy, 'F');
	}

            // pdf stamping invoice by chrissie
            if(!empty($pdf_stamp) && file_exists( UPLOADS_CFILES_FOLDER . $pdf_stamp)) {
                    $pdf = new Pdf($archived_file);	// here pdftk mikehaertl is being used
                    $pdf->multiStamp( UPLOADS_CFILES_FOLDER . $pdf_stamp)
                            ->saveAs($archived_file);

	  // invoice copy by chrissie with watermark
	  if ($invoice_copy == true) {
		// stamping of copy
		if(!empty($pdf_stamp) && file_exists( UPLOADS_CFILES_FOLDER . $pdf_stamp)) {
		        $pdf = new Pdf($archived_file_copy);
			$pdf->multiStamp( UPLOADS_CFILES_FOLDER . $pdf_stamp)
			        ->saveAs($archived_file_copy);
		}
	
		// concatenate both pdf
		$pdf = new Pdf();
		$pdf->addFile($archived_file);
		$pdf->addFile($archived_file_copy);
		$pdf->saveAs($archived_file_copy);
	  }
	}


            // using readfile, setting header!
            if ($stream) {
                    header('Content-type: application/pdf');
                    header('Content-Disposition: inline; filename="' . $filename . '.pdf"');
                    header('Content-Transfer-Encoding: binary');
                    header('Accept-Ranges: bytes');

		if ($invoice_copy == true) {
                    @readfile ($archived_file_copy);
		    return;
		} else {
                    @readfile ($archived_file);
		    return;
		}
            } else {
		if ($invoice_copy == true) {
                    return $archived_file_copy;
		} else {
                    return $archived_file;
		}
            }
    } // END $isInvoice

	
    // generate new pdf : other files but not invoice
    $t = UPLOADS_TEMP_FOLDER . $filename . '.pdf';
    $mpdf->Output($t, 'F');

    // pdf stamping other by chrissie
    if(!empty($pdf_stamp) && file_exists( UPLOADS_CFILES_FOLDER . $pdf_stamp)) {
            $pdf = new Pdf($t);	// here pdftk is being used
            $pdf->multiStamp( UPLOADS_CFILES_FOLDER . $pdf_stamp)
                    ->saveAs($t);
    }

    // If $stream is true (default) the PDF will be displayed directly in the browser
    // otherwise will be returned as a download
    if ($stream) {
          header('Content-type: application/pdf');
          header('Content-Disposition: inline; filename="' . $filename . '.pdf"');
          header('Content-Transfer-Encoding: binary');
          header('Accept-Ranges: bytes');
          @readfile ($t);
    } else {
        return $t;
    }
}
