<?php

if ( ! defined('BASEPATH')) {
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
 * Returns the invoice image.
 */
function invoice_logo(): string
{
    $CI = &get_instance();

    if ($CI->mdl_settings->setting('invoice_logo')) {
        return '<img src="' . base_url() . 'uploads/' . $CI->mdl_settings->setting('invoice_logo') . '">';
    }

    return '';
}

/**
 * Returns the invoice logo for PDF files.
 */
function invoice_logo_pdf(): string
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
 * @param        $amount
 * @param string $rnumb
 *
 * @throws Error
 */
function invoice_genCodeline(string $slipType, $amount, $rnumb, $subNumb): string
{
    $isEur = false;

    if ((int) $slipType > 14) {
        $isEur = true;
    } else {
        $amount = .5 * round((float) $amount / .5, 1);
    }

    if ( ! $isEur && $amount > 99999999.95) {
        throw new Error('Invalid amount');
    }

    if ($isEur && $amount > 99999999.99) {
        throw new Error('Invalid amount');
    }

    $amountLine    = sprintf('%010d', $amount * 100);
    $checkSlAmount = invoice_recMod10($slipType . $amountLine);

    if ( ! preg_match("/\d{2}-\d{1,6}-\d{1}/", $subNumb)) {
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
 */
function invoice_recMod10($in): int
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
 */
function invoice_qrcode($invoice_id): string
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
