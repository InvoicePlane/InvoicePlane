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

function generate_invoice_pdf($invoice_id, $stream = true, $invoice_template = null, $isGuest = null)
{
    $CI = &get_instance();

    $CI->load->model('invoices/mdl_invoices');
    $CI->load->model('invoices/mdl_items');
    $CI->load->model('invoices/mdl_invoice_tax_rates');
    $CI->load->model('payment_methods/mdl_payment_methods');
    $CI->load->helper('country');

    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);

    if (!$invoice_template) {
        $CI->load->helper('template');
        $invoice_template = select_pdf_invoice_template($invoice);
    }

    $payment_method = $CI->mdl_payment_methods->where('payment_method_id', $invoice->payment_method)->get()->row();
    if ($invoice->payment_method == 0) $payment_method = false;

    // Determine if discounts should be displayed
    $items = $CI->mdl_items->where('invoice_id', $invoice_id)->get()->result();
    $show_discounts = false;

    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_discounts = true;
        }
    }

    // PDF associated files
    $include_zugferd = $CI->mdl_settings->setting('include_zugferd');

    if ($include_zugferd) {
        $CI->load->helper('zugferd');

        $associatedFiles = array(array(
            'name' => 'ZUGFeRD-invoice.xml',
            'description' => 'ZUGFeRD Invoice',
            'AFRelationship' => 'Alternative',
            'mime' => 'text/xml',
            'path' => generate_invoice_zugferd_xml_temp_file($invoice, $items)
        ));
    } else {
        $associatedFiles = null;
    }

    $data = array(
        'invoice' => $invoice,
        'invoice_tax_rates' => $CI->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result(),
        'items' => $items,
        'payment_method' => $payment_method,
        'output_type' => 'pdf',
        'show_discounts' => $show_discounts,
    );

    $html = $CI->load->view('invoice_templates/pdf/' . $invoice_template, $data, true);

    $CI->load->helper('mpdf');
    return pdf_create($html, trans('invoice') . '_' . str_replace(array('\\', '/'), '_', $invoice->invoice_number),
        $stream, $invoice->invoice_password, true, $isGuest, $include_zugferd, $associatedFiles);
}

function generate_quote_pdf($quote_id, $stream = true, $quote_template = null)
{
    $CI = &get_instance();

    $CI->load->model('quotes/mdl_quotes');
    $CI->load->model('quotes/mdl_quote_items');
    $CI->load->model('quotes/mdl_quote_tax_rates');
    $CI->load->helper('country');

    $quote = $CI->mdl_quotes->get_by_id($quote_id);

    if (!$quote_template) {
        $quote_template = $CI->mdl_settings->setting('pdf_quote_template');
    }

    // Determine if discounts should be displayed
    $items = $CI->mdl_quote_items->where('quote_id', $quote_id)->get()->result();
    $show_discounts = false;

    foreach ($items as $item) {
        if ($item->item_discount != '0.00') {
            $show_discounts = true;
        }
    }

    $data = array(
        'quote' => $quote,
        'quote_tax_rates' => $CI->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
        'items' => $items,
        'output_type' => 'pdf',
        'show_discounts' => $show_discounts,
    );

    $html = $CI->load->view('quote_templates/pdf/' . $quote_template, $data, true);

    $CI->load->helper('mpdf');

    return pdf_create($html, trans('quote') . '_' . str_replace(array('\\', '/'), '_', $quote->quote_number), $stream, $quote->quote_password);
}
