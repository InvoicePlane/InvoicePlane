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
 *
 * eInvoicing add-ons by Verony
 */

function generate_xml_invoice_file($invoice, $items, $xml_lib, $filename)
{
    $CI = &get_instance();

    $CI->load->library('XMLtemplates/' . $xml_lib . 'Xml', [
        'invoice'  => $invoice,
        'items'    => $items,
        'filename' => $filename,
    ], 'ublciixml');
    $CI->ublciixml->xml();
    $path = UPLOADS_TEMP_FOLDER . $filename . '.xml';

    return $path;
}

function include_rdf($embedXml)
{
    $rdf = '<rdf:Description rdf:about="" xmlns:zf="urn:zugferd:pdfa:CrossIndustryDocument:invoice:1p0#">' . "\n";
    $rdf .= '  <zf:DocumentType>INVOICE</zf:DocumentType>' . "\n";
    $rdf .= '  <zf:DocumentFileName>' . $embedXml . '</zf:DocumentFileName>' . "\n";
    $rdf .= '  <zf:Version>1.0</zf:Version>' . "\n";
    $rdf .= '  <zf:ConformanceLevel>COMFORT</zf:ConformanceLevel>' . "\n";
    $rdf .= '</rdf:Description>' . "\n";

    return $rdf;
}

/**
 * Returns all available xml-template items.
 *
 * @return array
 */
function get_xml_template_files()
{
    $path = APPPATH . 'libraries/XMLtemplates';
    $xml_template_files = array_diff(scandir($path), ['.', '..']);

    foreach ($xml_template_files as $key => $xml_template_file) {
        $xml_template_files[$key] = str_replace('Xml.php', '', $xml_template_file);

        if (file_exists(APPPATH . 'helpers/XMLconfigs/' . $xml_template_files[$key] . '.php')) {
            include APPPATH . 'helpers/XMLconfigs/' . $xml_template_files[$key] . '.php';

            $xml_template_items[$xml_template_files[$key]] = $xml_setting['full-name'] . ' - ' . get_country_name(trans('cldr'), $xml_setting['countrycode']);
        }
    }

    return $xml_template_items;
}

/**
 * Returns the XML template (UBL/CII) fullname of a given client_e-invoice_version value.
 *
 * @param $xml_Id
 *
 * @return mixed
 */
function get_xml_full_name($xml_id)
{
    if (file_exists(APPPATH . 'helpers/XMLconfigs/' . $xml_id . '.php')) {
        include APPPATH . 'helpers/XMLconfigs/' . $xml_id . '.php';

        return $xml_setting['full-name'] . ' - ' . get_country_name(trans('cldr'), $xml_setting['countrycode']);
    }
}
