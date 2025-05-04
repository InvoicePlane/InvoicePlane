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
    $xml_config_files = is_dir($path) ? array_diff(scandir($path), ['.', '..']) : [];

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
 * @param int $user_id : get result only with it (or all if null)
 * @return array $user(s)
 */
function get_admin_active_users($user_id = ''): array
{
    $CI = &get_instance();

    $where = ['user_type' => '1', 'user_active' => '1']; // Administrators Active Only
    if ($user_id) {
        $where['user_id'] = $user_id;
    }

    return $CI->db->from('ip_users')->where($where)->get()->result();
}

/**
 * @scope clients & invoices controllers
 * @param object $client
 * @param int $user_id : get result only with it (or all if null)
 * @return object $req_fields
 */
function get_req_fields_einvoice($client = null, $user_id = ''): object
{
    $cid = empty($client->client_id) ? 0 : $client->client_id; // Client is New (form) or exist
    $c = new stdClass();
    // check if required (einvoicing) fields are filled in?
    $c->address_1 = $cid ? ($client->client_address_1 != '' ? 0 : 1) : 0;
    $c->zip       = $cid ? ($client->client_zip       != '' ? 0 : 1) : 0;
    $c->city      = $cid ? ($client->client_city      != '' ? 0 : 1) : 0;
    $c->country   = $cid ? ($client->client_country   != '' ? 0 : 1) : 0;
    $c->company   = $cid ? ($client->client_company   != '' ? 0 : 1) : 0;
    $c->vat_id    = $cid ? ($client->client_vat_id    != '' ? 0 : 1) : 0;
    // little tweak to run with or without vat_id
    if ($c->company + $c->vat_id == 2) {
        $c->company = 0;
        $c->vat_id  = 0;
    }

    $total_empty_fields_client = 0;
    foreach ($c as $val) {
        $total_empty_fields_client += $val;
    }

    $c->einvoicing_empty_fields = $total_empty_fields_client;
    $c->show_table              = ! $c->einvoicing_empty_fields;

    // Begin to save results
    $req_fields = new stdClass();
    $req_fields->clients[$cid] = $c;
    // Init user in session (tricks to make it 1st)
    $req_fields->users[$_SESSION['user_id']] = null;

    // $show_table = $c->einvoicing_empty_fields;
    $show_table = 0; // Only user

    // Get user(s) fields for eInvoicing
    $users = get_admin_active_users($user_id);
    foreach ($users as $o) {
        $u = new stdClass();
        // check if required (eInvoicing) fields are filled in?
        $u->address_1 = $o->user_address_1 != '' ? 0 : 1;
        $u->zip       = $o->user_zip       != '' ? 0 : 1;
        $u->city      = $o->user_city      != '' ? 0 : 1;
        // todo: user_tax user_tax_code user_bank user_iban user_bic ?
        $u->country   = $o->user_country   != '' ? 0 : 1;
        $u->company   = $o->user_company   != '' ? 0 : 1;
        $u->vat_id    = $o->user_vat_id    != '' ? 0 : 1;
        // little tweak to run with or without vat_id
        if ($u->company + $u->vat_id == 2) {
            $u->company = 0;
            $u->vat_id  = 0;
        }

        $total_empty_fields_user = 0;
        foreach ($u as $val) {
            $total_empty_fields_user += $val;
        }

        // Check mandatory fields (no company, client, email address, ...)
        $u->einvoicing_empty_fields = $total_empty_fields_user;

        // For show table (or not) record (in relation with client)
        $u->tr_show_address_1 = $u->address_1 + $c->address_1 > 0 ? 1 : 0;
        $u->tr_show_zip       = $u->zip       + $c->zip       > 0 ? 1 : 0;
        $u->tr_show_city      = $u->city      + $c->city      > 0 ? 1 : 0;
        $u->tr_show_country   = $u->country   + $c->country   > 0 ? 1 : 0;
        $u->tr_show_company   = $u->company   + $c->company   > 0 ? 1 : 0;
        $u->tr_show_vat_id    = $u->vat_id    + $c->vat_id    > 0 ? 1 : 0;
        $u->show_table        = $u->tr_show_address_1 +
                                 $u->tr_show_zip      +
                                 $u->tr_show_city     +
                                 $u->tr_show_country  +
                                 $u->tr_show_company  +
                                 $u->tr_show_vat_id > 0 ? 1 : 0;

        // No nessessary to check but for handly loop in view
        $u->user_name = $o->user_name;

        // Save user
        $req_fields->users[$o->user_id] = $u;
        $show_table += $u->show_table;
    }

    $req_fields->show_table = $show_table;

    return $req_fields;
}