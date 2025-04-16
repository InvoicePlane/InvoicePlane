<?php

if (! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 *
 * eInvoicing add-ons by Verony
 */

/**
 * Returns path of invoice xml generated file.
 *
 * @scope  helpers/pdf_helper.php (2)
 *
 * @return string
 */
function generate_xml_invoice_file($invoice, $items, $xml_lib, $filename, $options)
{
    $CI = &get_instance();

    $CI->load->library('XMLtemplates/' . $xml_lib . 'Xml', [
        'invoice'  => $invoice,
        'items'    => $items,
        'filename' => $filename,
        'options'  => $options,
    ], 'ublciixml');
    $CI->ublciixml->xml();

    return UPLOADS_TEMP_FOLDER . $filename . '.xml';
}

function include_rdf($embedXml, $urn = 'factur-x')
{
    return '<rdf:Description rdf:about="" xmlns:zf="urn:' . $urn . ':pdfa:CrossIndustryDocument:invoice:1p0#">' . "\n"
         . '  <zf:DocumentType>INVOICE</zf:DocumentType>' . "\n"
         . '  <zf:DocumentFileName>' . $embedXml . '</zf:DocumentFileName>' . "\n"
         . '  <zf:Version>1.0</zf:Version>' . "\n"
         . '  <zf:ConformanceLevel>COMFORT</zf:ConformanceLevel>' . "\n"
         . '</rdf:Description>' . "\n";
}

/**
 * Returns all available xml-template items.
 *
 * @scope  modules/clients/controllers/Clients.php
 *
 * @return array
 */
function get_xml_template_files()
{
    $xml_template_items = [];
    $path = APPPATH . 'helpers/XMLconfigs/';
    $xml_config_files = array_diff(scandir($path), ['.', '..']);

    foreach ($xml_config_files as $key => $xml_config_file) {
        $xml_config_files[$key] = str_replace('.php', '', $xml_config_file);

        if (file_exists($path . $xml_config_files[$key] . '.php') && include $path . $xml_config_files[$key] . '.php') {
            // By default config filename
            $generator = $xml_config_files[$key];
            // Use other template? (Optional)
            if (! empty($xml_setting['generator'])) {
                $generator = $xml_setting['generator'];
            }

            // The template to generate the e-invoice file exist?
            if (file_exists(APPPATH . 'libraries/XMLtemplates/' . $generator . 'Xml.php')) {
                // Add the name in list + translated country
                $xml_template_items[$xml_config_files[$key]] = $xml_setting['full-name']
                . ' - ' . get_country_name(trans('cldr'), $xml_setting['countrycode']);
            }
        }
    }

    return $xml_template_items;
}

/**
 * Returns the XML template (UBL/CII) fullname of a given client_e-invoice_version value.
 *
 * @param $xml_Id
 *
 * @scope modules/clients/views/(form|view).php
 *
 * @return mixed
 */
function get_xml_full_name($xml_id)
{
    if (file_exists(APPPATH . 'helpers/XMLconfigs/' . $xml_id . '.php')) {
        include APPPATH . 'helpers/XMLconfigs/' . $xml_id . '.php';

        return $xml_setting['full-name'] . ' - ' . get_country_name(trans('cldr'), $xml_setting['countrycode']);
    }

    return null;
}

/**
 * @return array
 */

function get_ubl_eas_codes()
{
    $file = APPPATH . 'config' . DIRECTORY_SEPARATOR . 'peppol_eas_code_list.csv';
    $rows = array_map(function ($v) {
        return str_getcsv($v, ";");
    }, file($file));
    $head = array_shift($rows);
    $eas  = [];
    foreach ($rows as $row) {
        if ($row[1] == 'ICD') {
            $eas[] = array_combine($head, $row);
        }
    }

    return $eas;
}
