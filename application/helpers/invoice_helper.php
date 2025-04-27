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
 * Replace [BANQUES] on invoice terms or quote notes into the PDF/Public template (2022)
 * @author		Thomas I. @sudwebdesign
 * @param str $invoice->invoice_terms OR $quote->notes
 * @param str $custom_fields
 * NOTES ::: REPLACE in application/view/[invoice|quote]_template/[pdf|public]/...
    echo nl2br(htmlsc($invoice->invoice_terms));   by echo $invoice->invoice_terms;
    echo nl2br(htmlsc($quote->notes));             by echo $quote->notes;
 * @return string
 */
function custom_terms_or_notes($terms_or_notes, $custom_fields, $field = '[BANQUES]')
{
    $custom_rib = '';
    if(isset($custom_fields['invoice'][$field]) || isset($custom_fields['quote'][$field])){
        $ribs = '';
        if(isset($custom_fields['quote'][$field])){
            foreach($custom_fields['quote'][$field] as $id => $rib){
                $float = $id%3===1?'right':'left';
                $ribs .= '<div style="width:50%;float:'.$float.'">'.str_replace(' - ', '<br />', $rib).'</div>';
            }
        }

        if(isset($custom_fields['invoice'][$field])){
            $ribs = '';#RAZ
            foreach($custom_fields['invoice'][$field] as $id => $rib){
                $float = $id%3===1?'right':'left';
                $ribs .= '<div style="width:50%;float:'.$float.'">'.str_replace(' - ', '<br />', $rib).'</div>';
            }
        }

        $custom_rib = $ribs ? '<div>Virement bancaire<br />'.$ribs.'</div><br style="clear:both;" />' : '';
    }

    return nl2br(str_replace($field, $custom_rib, htmlsc($terms_or_notes)));
}

/**
 * Returns the invoice image.
 *
 * @return string
 */
function invoice_logo()
{
    $CI = &get_instance();

    if ($CI->mdl_settings->setting('invoice_logo')) {
        return '<img src="' . base_url() . 'uploads/' . $CI->mdl_settings->setting('invoice_logo') . '">';
    }

    return '';
}

/**
 * Returns the invoice logo for PDF files.
 *
 * @return string
 */
function invoice_logo_pdf()
{
    $CI = &get_instance();

    if ($CI->mdl_settings->setting('invoice_logo')) {
        $absolutePath = dirname(dirname(__DIR__));

        return '<img src="' . $absolutePath . '/uploads/' . $CI->mdl_settings->setting('invoice_logo') . '" id="invoice-logo">';
    }

    return '';
}

/**
 * Returns a Swiss IS / IS+ code line
 * Documentation: https://www.postfinance.ch/binp/postfinance/public/dam.M26m_i6_6ceYcN2XtAN4w8OHMynQG7FKxJVK8TtQzr0.spool/content/dam/pf/de/doc/consult/manual/dlserv/inpayslip_isr_man_en.pdf.
 *
 * @param string $slipType
 * @param        $amount
 * @param string $rnumb
 *
 * @return string
 *
 * @throws Error
 */
function invoice_genCodeline($slipType, $amount, $rnumb, $subNumb)
{
    $isEur = false;

    if ((int) $slipType > 14) {
        $isEur = true;
    } else {
        $amount = .5 * round((float) $amount / .5, 1);
    }

    if (! $isEur && $amount > 99999999.95) {
        throw new Error('Invalid amount');
    }

    if ($isEur && $amount > 99999999.99) {
        throw new Error('Invalid amount');
    }

    $amountLine    = sprintf('%010d', $amount * 100);
    $checkSlAmount = invoice_recMod10($slipType . $amountLine);

    if (! preg_match("/\d{2}-\d{1,6}-\d{1}/", $subNumb)) {
        throw new Error('Invalid subscriber number');
    }

    $subNumb = explode('-', $subNumb);
    $fullSub = $subNumb[0] . sprintf('%06d', $subNumb[1]) . $subNumb[2];
    $rnumb   = preg_replace('/\s+/', '', $rnumb);

    return $slipType . $amountLine . $checkSlAmount . '>' . $rnumb . '+ ' . $fullSub . '>';
}

/**
 * Calculate checksum using Recursive Mod10
 * See https://www.postfinance.ch/binp/postfinance/public/dam.Ii-X5NgtAixO8cQPvja46blV6d7cZCyGUscxO15L5S8.spool/content/dam/pf/de/doc/consult/manual/dldata/efin_recdescr_man_en.pdf
 * Page 5.
 *
 * @param string $in
 *
 * @return int
 */
function invoice_recMod10($in)
{
    $line  = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];
    $carry = 0;
    $chars = mb_str_split($in);

    foreach ($chars as $char) {
        $carry = $line[($carry + (int) $char) % 10];
    }

    return (10 - $carry) % 10;
}

/**
 * Returns a QR code for invoice payments.
 *
 * @param number invoice-id
 *
 * @return string
 */
function invoice_qrcode($invoice_id)
{
    $CI = &get_instance();

    if (
        $CI->mdl_settings->setting('qr_code')
        && $CI->mdl_settings->setting('qr_code_iban')
        && $CI->mdl_settings->setting('qr_code_bic')
    ) {
        $invoice = $CI->mdl_invoices->get_by_id($invoice_id);

        if ((float) $invoice->invoice_balance) {
            $CI->load->library('QrCode', ['invoice' => $invoice]);
            $qrcode_data_uri = $CI->qrcode->generate();

            return '<img src="' . $qrcode_data_uri . '" alt="QR Code" id="invoice-qr-code">';
        }
    }

    return '';
}
