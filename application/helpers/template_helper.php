<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2018 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
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
                    if (preg_match('/ip_cf_(\d.*)/', $var, $cf_id)) {
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
        $CI->load->model('invoices/mdl_invoices');
    }

    $statuses = $CI->mdl_invoices->statuses();

    return $statuses[$id]['label'];
}

/**
 * Returns the appropriate PDF template for the given invoice.
 *
 * Security: This function now validates template names from settings to prevent LFI attacks.
 *
 * @param $invoice
 *
 * @return string
 */
function select_pdf_invoice_template($invoice)
{
    $CI = & get_instance();
    
    if ($invoice->is_overdue) {
        // Use the overdue template
        $template_name = $CI->mdl_settings->setting('pdf_invoice_template_overdue');
    } elseif ($invoice->invoice_status_id == 4) {
        // Use the paid template
        $template_name = $CI->mdl_settings->setting('pdf_invoice_template_paid');
    } else {
        // Use the default template
        $template_name = $CI->mdl_settings->setting('pdf_invoice_template');
    }
    
    // Security: Validate the template name
    $validated = validate_template_name($template_name, 'invoice', 'pdf');
    if ($validated === false) {
        // Sanitize template name before logging to avoid log injection
        $safe_template_name = preg_replace('/[\x00-\x1F\x7F]/', '', (string) $template_name);
        log_message('error', 'Invalid PDF invoice template from settings: ' . $safe_template_name . ', using default');
        return 'InvoicePlane'; // Safe default
    }
    
    return $validated;
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

/**
 * Validates and sanitizes a template name to prevent Local File Inclusion (LFI) attacks.
 * 
 * Security: This function ensures that only legitimate template files from the allowed
 * directory can be loaded, preventing path traversal and arbitrary file inclusion.
 *
 * @param string $template_name The template name from settings
 * @param string $type The template type ('invoice' or 'quote')
 * @param string $scope The template scope ('public' or 'pdf')
 * @return string|false Returns the validated template name or false if validation fails
 */
function validate_template_name($template_name, $type = 'invoice', $scope = 'pdf')
{
    // Load necessary dependencies
    $CI = & get_instance();
    $CI->load->helper('file_security');
    $CI->load->model('invoices/mdl_templates');
    
    // Get the list of valid templates for the requested type and scope
    if ($type === 'invoice') {
        $valid_templates = $CI->mdl_templates->get_invoice_templates($scope);
    } elseif ($type === 'quote') {
        $valid_templates = $CI->mdl_templates->get_quote_templates($scope);
    } else {
        // Security: Sanitize type parameter before logging to prevent log injection
        $safe_type = sanitize_for_logging((string) $type);
        log_message('error', 'Template validation failed: Invalid template type: ' . $safe_type);
        return false;
    }
    
    // Security: Verify the template exists in the allowed list
    // Note: get_*_templates() returns an array of template names without .php extension
    if (!in_array($template_name, $valid_templates, true)) {
        // Security: Sanitize template name before logging to prevent log injection
        $safe_template_name = sanitize_for_logging((string) $template_name);
        log_message('error', 'Template validation failed: Template not in allowed list: ' . $safe_template_name);
        return false;
    }
    
    return $template_name;
}

/**
 * Validates a PDF template name and returns a safe default if validation fails.
 * 
 * Security: This function is specifically for PDF templates loaded from settings or URL parameters.
 * It validates the template name and falls back to the appropriate default template.
 *
 * @param string|null $template_name The template name to validate
 * @param string $type The template type ('invoice' or 'quote')
 * @param string $default_setting The setting key for the default template (optional)
 * @return string Returns the validated template name or a safe default
 */
function validate_pdf_template($template_name, $type = 'invoice', $default_setting = null)
{
    // Load file_security_helper to access sanitize_for_logging function
    $CI = & get_instance();
    $CI->load->helper('file_security');
    
    // If no template provided, use the setting or default
    if (empty($template_name)) {
        if ($default_setting) {
            $template_name = $CI->mdl_settings->setting($default_setting);
        } else {
            // Use default template name (InvoicePlane is the default for both types)
            return 'InvoicePlane';
        }
    }
    
    // Validate the template name
    $validated = validate_template_name($template_name, $type, 'pdf');
    
    if ($validated === false) {
        $safe_template_name = sanitize_for_logging((string) $template_name);
        log_message('error', 'Invalid PDF template: ' . $safe_template_name . ', using default');
        // Return safe default (InvoicePlane is the default template for both invoice and quote PDFs)
        return 'InvoicePlane';
    }
    
    return $validated;
}
