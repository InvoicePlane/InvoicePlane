<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author		InvoicePlane Developers & Contributors
 * @copyright	Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license		https://invoiceplane.com/license.txt
 * @link		https://invoiceplane.com
 */

/**
 * Returns the invoice image
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
 * Returns the invoice logo for PDF files
 *
 * @return string
 */
function invoice_logo_pdf()
{
    $CI = &get_instance();

    if ($CI->mdl_settings->setting('invoice_logo')) {
        return '<img src="' . getcwd() . '/uploads/' . $CI->mdl_settings->setting('invoice_logo') . '" id="invoice-logo">';
    }

    return '';
}


/**
 * Returns a Swiss IS / IS+ code line
 * Documentation: https://www.postfinance.ch/binp/postfinance/public/dam.M26m_i6_6ceYcN2XtAN4w8OHMynQG7FKxJVK8TtQzr0.spool/content/dam/pf/de/doc/consult/manual/dlserv/inpayslip_isr_man_en.pdf
 *
 * @param $slipType
 * @param $amount
 * @param $rnumb
 * @param $subNumb
 * @return string
 * @throws Error
 */
function invoice_genCodeline($slipType, $amount, $rnumb, $subNumb)
{
    $isEur = false;

    if ((int)$slipType > 14) {
        $isEur = true;
    } else {
        $amount = .5 * round((float)$amount / .5, 1);
    }

    if (!$isEur && $amount > 99999999.95) {
        throw new Error("Invalid amount");
    } elseif ($isEur && $amount > 99999999.99) {
        throw new Error("Invalid amount");
    }

    $amountLine = sprintf("%010d", $amount * 100);
    $checkSlAmount = invoice_recMod10($slipType . $amountLine);

    if (!preg_match("/\d{2}-\d{1,6}-\d{1}/", $subNumb)) {
        throw new Error("Invalid subscriber number");
    }

    $subNumb = explode("-", $subNumb);
    $fullSub = $subNumb[0] . sprintf("%06d", $subNumb[1]) . $subNumb[2];
    $rnumb = preg_replace('/\s+/', '', $rnumb);

    return $slipType . $amountLine . $checkSlAmount . ">" . $rnumb . "+ " . $fullSub . ">";
}

/**
 * Calculate checksum using Recursive Mod10
 * See https://www.postfinance.ch/binp/postfinance/public/dam.Ii-X5NgtAixO8cQPvja46blV6d7cZCyGUscxO15L5S8.spool/content/dam/pf/de/doc/consult/manual/dldata/efin_recdescr_man_en.pdf
 * Page 5
 *
 * @return string
 */
function invoice_recMod10($in)
{
    $line = [0, 9, 4, 6, 8, 2, 7, 1, 3, 5];
    $carry = 0;
    $chars = str_split($in);

    foreach ($chars as $char) {
        $carry = $line[($carry + intval($char)) % 10];
    }

    return (10 - $carry) % 10;
}
