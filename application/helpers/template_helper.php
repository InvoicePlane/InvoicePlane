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
 * Parse a template by predefined template tags.
 *
 * @param $object
 * @param $body
 * @param $model_id
 *
 * @return mixed
 */
function parse_template($object, $body)
{
    if (preg_match_all('/{{{([^{|}]*)}}}/', $body, $template_vars)) {
        foreach ($template_vars[1] as $var) {
            switch ($var) {
                case 'invoice_guest_url':
                    $replace = site_url('guest/view/invoice/' . $object->invoice_url_key);
                    break;
                case 'invoice_date_due':
                    $replace = date_from_mysql($object->invoice_date_due, true);
                    break;
                case 'invoice_date_created':
                    $replace = date_from_mysql($object->invoice_date_created, true);
                    break;
                case 'invoice_item_subtotal':
                    $replace = format_currency($object->invoice_item_subtotal);
                    break;
                case 'invoice_item_tax_total':
                    $replace = format_currency($object->invoice_item_tax_total);
                    break;
                case 'invoice_total':
                    $replace = format_currency($object->invoice_total);
                    break;
                case 'invoice_paid':
                    $replace = format_currency($object->invoice_paid);
                    break;
                case 'invoice_balance':
                    $replace = format_currency($object->invoice_balance);
                    break;
                case 'invoice_status':
                    $replace = get_invoice_status($object->invoice_status_id);
                    break;
                case 'quote_item_subtotal':
                    $replace = format_currency($object->quote_item_subtotal);
                    break;
                case 'quote_tax_total':
                    $replace = format_currency($object->quote_tax_total);
                    break;
                case 'quote_item_discount':
                    $replace = format_currency($object->quote_item_discount);
                    break;
                case 'quote_total':
                    $replace = format_currency($object->quote_total);
                    break;
                case 'quote_date_created':
                    $replace = date_from_mysql($object->quote_date_created, true);
                    break;
                case 'quote_date_expires':
                    $replace = date_from_mysql($object->quote_date_expires, true);
                    break;
                case 'quote_guest_url':
                    $replace = site_url('guest/view/quote/' . $object->quote_url_key);
                    break;
                case 'sumex_casedate':
                    if (isset($object->sumex_casedate)) {
                        $replace = date_from_mysql($object->sumex_casedate, true);
                    }
                    break;
                default:
                    // Check if it's a custom field
                    if (preg_match('/ip_cf_([0-9].*)/', $var, $cf_id)) {
                        // Get the custom field
                        $CI = & get_instance();
                        $CI->load->model('custom_fields/mdl_custom_fields');
                        $cf = $CI->mdl_custom_fields->get_by_id($cf_id[1]);

                        if ($cf) {
                            // Get the values for the custom field
                            $cf_model = str_replace('ip_', 'mdl_', $cf->custom_field_table);
                            $replace  = $CI->mdl_custom_fields->get_value_for_field($cf_id[1], $cf_model, $object);
                            if ($cf->custom_field_type == 'SINGLE-CHOICE') {
                                $CI->load->model('custom_values/mdl_custom_values', 'cv');
                                $el      = $CI->cv->get_by_id($replace)->row();
                                $replace = $el->custom_values_value;
                            }
                        } else {
                            $replace = '';
                        }
                    } else {
                        $replace = $object->{$var} ?? $var;
                    }
            }

            $body = str_replace('{{{' . $var . '}}}', $replace, $body);
        }
    }

    return $body;
}

/**
 * Returns the translated invoice status.
 *
 * @param $invoice->invoice_status_id
 *
 * @return string
 */
function get_invoice_status($id)
{
    $CI = & get_instance();

    if (empty($CI->mdl_invoices)) {
        $CI->load->model('mdl_invoices');
    }
    $statuses = $CI->mdl_invoices->statuses();

    return $statuses[$id]['label'];
}

/**
 * Returns the appropriate PDF template for the given invoice.
 *
 * @param $invoice
 *
 * @return mixed
 */
function select_pdf_invoice_template($invoice)
{
    $CI = & get_instance();

    if ($invoice->is_overdue) {
        // Use the overdue template
        return $CI->mdl_settings->setting('pdf_invoice_template_overdue');
    }
    if ($invoice->invoice_status_id == 4) {
        // Use the paid template
        return $CI->mdl_settings->setting('pdf_invoice_template_paid');
    }

    // Use the default template
    return $CI->mdl_settings->setting('pdf_invoice_template');
}

/**
 * Returns the appropriate email template for the given invoice.
 *
 * @param $invoice
 *
 * @return mixed
 */
function select_email_invoice_template($invoice)
{
    $CI = & get_instance();

    if ($invoice->is_overdue) {
        // Use the overdue template
        return $CI->mdl_settings->setting('email_invoice_template_overdue');
    }
    if ($invoice->invoice_status_id == 4) {
        // Use the paid template
        return $CI->mdl_settings->setting('email_invoice_template_paid');
    }

    // Use the default template
    return $CI->mdl_settings->setting('email_invoice_template');
}
