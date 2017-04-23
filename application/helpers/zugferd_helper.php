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
 * Generate a temporaty XML file from the invoice data
 * @param $invoice
 * @param $items
 * @return string
 */
function generate_invoice_zugferd_xml_temp_file($invoice, $items)
{
    $CI = &get_instance();
    $CI->load->helper('file');

    $path = './uploads/temp/invoice_' . $invoice->invoice_id . '_zugferd.xml';
    $CI->load->library('zugferdxml', array('invoice' => $invoice, 'items' => $items));

    write_file($path, $CI->zugferdxml->xml());
    return $path;
}

/**
 * Returns the correct RDF string for the Zugferd XML
 * @return string
 */
function zugferd_rdf()
{
    $s = '<rdf:Description rdf:about="" xmlns:zf="urn:ferd:pdfa:CrossIndustryDocument:invoice:1p0#">' . "\n";
    $s .= '  <zf:DocumentType>INVOICE</zf:DocumentType>' . "\n";
    $s .= '  <zf:DocumentFileName>ZUGFeRD-invoice.xml</zf:DocumentFileName>' . "\n";
    $s .= '  <zf:Version>1.0</zf:Version>' . "\n";
    $s .= '  <zf:ConformanceLevel>COMFORT</zf:ConformanceLevel>' . "\n";
    $s .= '</rdf:Description>' . "\n";
    return $s;
}
