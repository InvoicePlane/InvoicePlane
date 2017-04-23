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
 * Parse a template by predefined template tags
 *
 * @param $object
 * @param $body
 * @param $model_id
 * @return mixed
 */
function parse_template($object, $body, $model_id)
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
                case 'invoice_total':
                    $replace = format_currency($object->invoice_total);
                    break;
                case 'invoice_paid':
                    $replace = format_currency($object->invoice_paid);
                    break;
                case 'invoice_balance':
                    $replace = format_currency($object->invoice_balance);
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
                default:
                    // Check if it's a custom field
                    if (preg_match('/ip_cf_([0-9].*)/', $var, $cf_id)) {
                        // Get the custom field
                        $CI =& get_instance();
                        $CI->load->model('custom_fields/mdl_custom_fields');
                        $cf = $CI->mdl_custom_fields->get_by_id($cf_id[1]);

                        if ($cf) {
                            // Get the values for the custom field
                            $cf_model = str_replace('ip_', 'mdl_', $cf->custom_field_table);
                            $replace = $CI->mdl_custom_fields
                                ->get_value_for_field($cf_id[1], $cf_model, $model_id);
                        } else {
                            $replace = '';
                        }
                    } else {
                        $replace = isset($object->{$var}) ? $object->{$var} : $var;
                    }
            }

            $body = str_replace('{{{' . $var . '}}}', $replace, $body);
        }
    }

    return $body;
}

/**
 * Returns the appropriate PDF template for the given invoice
 *
 * @param $invoice
 * @return mixed
 */
function select_pdf_invoice_template($invoice)
{
    $CI =& get_instance();

    if ($invoice->is_overdue) {
        // Use the overdue template
        return $CI->mdl_settings->setting('pdf_invoice_template_overdue');
    } elseif ($invoice->invoice_status_id == 4) {
        // Use the paid template
        return $CI->mdl_settings->setting('pdf_invoice_template_paid');
    } else {
        // Use the default template
        return $CI->mdl_settings->setting('pdf_invoice_template');
    }
}

/**
 * Returns the appropriate email template for the given invoice
 *
 * @param $invoice
 * @return mixed
 */
function select_email_invoice_template($invoice)
{
    $CI =& get_instance();

    if ($invoice->is_overdue) {
        // Use the overdue template
        return $CI->mdl_settings->setting('email_invoice_template_overdue');
    } elseif ($invoice->invoice_status_id == 4) {
        // Use the paid template
        return $CI->mdl_settings->setting('email_invoice_template_paid');
    } else {
        // Use the default template
        return $CI->mdl_settings->setting('email_invoice_template');
    }
}
