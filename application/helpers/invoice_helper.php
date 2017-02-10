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
