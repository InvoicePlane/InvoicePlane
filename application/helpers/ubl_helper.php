<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2017 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Generate a (temporaty) XML file from the invoice data
 * @param $invoice
 * @param $items
 * @param $ubl_lib
 * @param $include_ubl
 * @param $filename
 * @return string
 */
function generate_xml_invoice_file($invoice, $items, $ubl_lib, $include_ubl = false, $filename)
{
    $CI = &get_instance();
    $CI->load->helper('file');
   
    $path = '';
    if ($include_ubl) {
        //generate the temp xml (Zugferd) file
        $path = './uploads/temp/' . $filename . '.xml';    
        $CI->load->library('UBLtemplates/'.$ubl_lib.'Xml', array('invoice' => $invoice, 'items' => $items), 'xmlinpdf');
        write_file($path, $CI->xmlinpdf->xml() );         
    } else {
        // generate the UBL file
        $path = './uploads/archive/' . $filename . '.xml';
        $CI->load->library('UBLtemplates/'.$ubl_lib.'Xml', 
                            array('invoice' => $invoice, 'items' => $items, 'filename' => $filename, 
                                  'pdf_file' => './uploads/archive/' . $filename . '.pdf' ), 'ublxml');
        $CI->ublxml->xml();
    } 
   
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

/**
 * Returns array data from the UBL version settings file.
 *
 * @param $col
 * @return mixed
 */
function get_ubl_arrlist($col)
{
    if (file_exists(APPPATH . 'helpers/ubl/version_settings.php') ) { 
        include APPPATH . 'helpers/ubl/version_settings.php';
        return (array_column($ubl, $col, 'id'));
    }
}

/**
 * Returns the UBL fullname of a given column $ublId.
 *
 * @param $ublId
 * @return mixed
 */
function get_ubl_fullname($ublId)
{   
    $ublname = get_ubl_arrlist('full-name'); 
    $ublcountry = get_ubl_arrlist('countrycode');     
    $ubl_fullname = $ublname[$ublId] ." - ". get_country_name(trans('cldr'), $ublcountry[$ublId]);

    return $ubl_fullname;
} 