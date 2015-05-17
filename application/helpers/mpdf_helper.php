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

function pdf_create($html, $filename, $stream = TRUE, $password = NULL,$isInvoice = NULL,$isGuest = NULL)
{
    require_once(APPPATH . 'helpers/mpdf/mpdf.php');

    $mpdf = new mPDF();
    $mpdf->useAdobeCJK = true;
	$mpdf->SetAutoFont();
    $mpdf->SetProtection(array('copy','print'), $password, $password);
    if(!(is_dir('./uploads/archiv/') OR is_link('./uploads/archiv/') ))
        mkdir ('./uploads/archiv/','0777');

    if (strpos($filename, lang('invoice')) !== false) {
        $CI = &get_instance();
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<div id="footer">' . $CI->mdl_settings->settings['pdf_invoice_footer'] . '</div>');
    }
    $invoice_array = array();
    $mpdf->WriteHTML($html);

    if ($stream) {
        if (!$isInvoice) {
            return $mpdf->Output($filename . '.pdf', 'I');
        }

        foreach (glob('./uploads/archiv/*' . $filename . '.pdf') as $file) {
            array_push($invoice_array, $file);
        }

        if (!empty($invoice_array) AND $isGuest) {
            rsort($invoice_array);
            header('Content-type: application/pdf');
            return readfile($invoice_array[0]);
        } else
            if ($isGuest){
            //todo flashdata is deleted between requests
            //$CI->session->flashdata('alert_error', 'sorry no Invoice found!');
            redirect('guest/view/invoice/' . end($CI->uri->segment_array()));
        }
        $mpdf->Output('./uploads/archiv/' . date('Y-m-d') . '_' . $filename . '.pdf', 'F');
        return $mpdf->Output('./uploads/archiv/' . date('Y-m-d') . '_' . $filename . '.pdf', 'I');
    }

    else {

        if($isInvoice) {

            foreach (glob('./uploads/archiv/*' .  $filename . '.pdf') as $file) {
                array_push($invoice_array, $file);
            }
            if (!empty($invoice_array) && !is_null($isGuest)) {
                rsort($invoice_array);
                return $invoice_array[0];
            }
            $mpdf->Output('./uploads/archiv/' . date('Y-m-d') .'_'. $filename . '.pdf', 'F');
            return './uploads/archiv/'.date('Y-m-d').'_'. $filename . '.pdf';
        }
        $mpdf->Output('./uploads/temp/' . $filename . '.pdf', 'F');
        return './uploads/temp/' . $filename . '.pdf';
    }
}
