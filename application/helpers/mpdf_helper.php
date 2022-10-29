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

    // Get the invoice from the archive if available
    $invoice_array = array();

    // mPDF loading
    $mpdf = new \Mpdf\Mpdf([
        'tempDir' => UPLOADS_TEMP_MPDF_FOLDER
    ]);

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
