<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

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

function generate_invoice_pdf($invoice_id, $stream = true, $invoice_template = null, $isGuest = null)
{
    $CI = &get_instance();

    $CI->load->model('invoices/mdl_invoices');
    $CI->load->model('invoices/mdl_items');
    $CI->load->model('invoices/mdl_invoice_tax_rates');
    $CI->load->model('payment_methods/mdl_payment_methods');
    $CI->load->library('encrypt');

    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
    if (!$invoice_template) {
        $CI->load->helper('template');
        $invoice_template = select_pdf_invoice_template($invoice);
    }

    $payment_method = $CI->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
    if ($invoice->payment_method == 0) {
        $payment_method = null;
    }

    $data = array(
        'invoice' => $invoice,
        'invoice_tax_rates' => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
        'items' => $CI->mdl_items->get_items_and_replace_vars($invoice_id, $invoice->invoice_date_created),
        'payment_method' => $payment_method,
        'output_type' => 'pdf'
    );

    $html = $CI->load->view('invoice_templates/pdf/' . $invoice_template, $data, true);

    $CI->load->helper('mpdf');
    return pdf_create($html, lang('invoice') . '_' . str_replace(array('\\', '/'), '_', $invoice->invoice_number),
        $stream, $invoice->invoice_password, 1, $isGuest);
}

function generate_quote_pdf($quote_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');

    $quote = $CI->mdl_quotes->get_by_id($quote_id);

    if (!$quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }

    $data = array(
        'quote' => $quote,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
        'items' => $CI->mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
        'output_type' => 'pdf'
    );

    $html = $CI->load->view('quote_templates/pdf/' . $quote_template, $data, true);

    $CI->load->helper('mpdf');

    return pdf_create($html, lang('quote') . '_' . str_replace(array('\\', '/'), '_', $quote->quote_number), $stream,
        $quote->quote_password);
}
