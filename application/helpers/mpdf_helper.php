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
    require_once(APPPATH . 'helpers/mpdf/mpdf.php');

    $mpdf = new mPDF();
    $mpdf->useAdobeCJK = true;
    $mpdf->SetAutoFont();

    if ($zugferd_invoice) {
        $CI = &get_instance();
        $CI->load->helper('zugferd');
        $mpdf->PDFA = true;
        $mpdf->PDFAauto = true;
        $mpdf->SetAdditionalRdf(zugferd_rdf());
        $mpdf->SetAssociatedFiles($associatedFiles);
    } else {
        // Avoid setting protection when password is blank/empty
        if (!empty($password)) {
            $mpdf->SetProtection(array('copy', 'print'), $password, $password);
        }
    }

    if (!(is_dir('./uploads/archive/') || is_link('./uploads/archive/'))) {
        mkdir('./uploads/archive/', '0777');
    }

    // Enable image error logging
    if (IP_DEBUG) {
        $mpdf->showImageErrors = true;
    }

    if (strpos($filename, trans('invoice')) !== false) {
        $CI = &get_instance();
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_invoice_footer'] . '</div>');
    }

    $invoice_array = array();
    $mpdf->WriteHTML($html);

    // If $stream is true (default) the PDF will be displayed directly in the browser, otherwise will be returned as a download
    if ($stream) {

        if (!$isInvoice) {
            return $mpdf->Output($filename . '.pdf', 'I');
        }

        foreach (glob('./uploads/archive/*' . $filename . '.pdf') as $file) {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) && $isGuest) {
            rsort($invoice_array);
            header('Content-type: application/pdf');
            return readfile($invoice_array[0]);
        } else
            if ($isGuest) {
                // @TODO flashdata is deleted between requests
                //$CI->session->flashdata('alert_error', 'sorry no Invoice found!');
                redirect('guest/view/invoice/' . end($CI->uri->segment_array()));
            }

        $mpdf->Output('./uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');

        return $mpdf->Output($filename . '.pdf', 'I');

    } else {

        if ($isInvoice) {

            foreach (glob('./uploads/archive/*' . $filename . '.pdf') as $file) {
                array_push($invoice_array, $file);
            }
            if (!empty($invoice_array) && !is_null($isGuest)) {
                rsort($invoice_array);
                return $invoice_array[0];
            }
            $mpdf->Output('./uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
            return './uploads/archive/' . date('Y-m-d') . '_' . $filename . '.pdf';
        }

        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');

        // Housekeeping
        // Delete any files in temp/ directory that are >1 hrs old
        $interval = 3600;
        if ($handle = @opendir(preg_replace('/\/$/', '', './uploads/temp/'))) {
            while (false !== ($file = readdir($handle))) {
                if (($file != '..') && ($file != '.') && !is_dir($file) && ((filemtime('./uploads/temp/' . $file) + $interval) < time()) && (substr($file, 0, 1) !== '.') && ($file != 'remove.txt')) { // mPDF 5.7.3
                    unlink('./uploads/temp/' . $file);
                }
            }
            closedir($handle);
        }

        // Return the pdf itself
        return './uploads/temp/' . $filename . '.pdf';
    }
}
