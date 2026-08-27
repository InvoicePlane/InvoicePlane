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
 * Load a template view, checking the custom templates folder first.
 *
 * When CUSTOM_TEMPLATES_FOLDER is configured, files in that directory take
 * precedence over the built-in views for the given sub-path.  Falls back to
 * CodeIgniter's standard view loader when no custom file is found.
 *
 * @param string $template_subpath Relative path used with $CI->load->view(),
 *                                 e.g. 'invoice_templates/pdf/MyTemplate'
 * @param array  $data             Variables to make available inside the template
 * @param bool   $return           Return rendered HTML instead of appending to output
 *
 * @return string|void
 */
function render_template_view(string $template_subpath, array $data, bool $return = false)
{
    if (CUSTOM_TEMPLATES_FOLDER) {
        $has_ext   = (bool) pathinfo($template_subpath, PATHINFO_EXTENSION);
        $file_path = CUSTOM_TEMPLATES_FOLDER . $template_subpath . ($has_ext ? '' : '.php');
        if (file_exists($file_path)) {
            extract($data);
            ob_start();
            include $file_path;
            $output = ob_get_clean();
            if ($return) {
                return $output;
            }
            $CI = &get_instance();
            $CI->output->append_output($output);

            return;
        }
    }

    $CI = &get_instance();

    return $CI->load->view($template_subpath, $data, $return);
}

/**
 * Parse a template by predefined template tags.
 *
 * $escape_values controls whether each substituted value is HTML-escaped
 * before being inserted. Defaults to false to preserve this function's other
 * callers: QrCode.php substitutes into plain SEPA/EPC remittance text (escaping
 * would corrupt the QR payload), and the two template views that call this
 * already wrap the whole result in _htmlsc() themselves, which would double-
 * escape if this also escaped. Pass true only when $body is genuinely
 * rendered as HTML and isn't already escaped by the caller — e.g. the HTML
 * email body in email_invoice()/email_quote(), where these substituted
 * values (client_name, custom field values, etc.) previously went in raw.
 *
 * @param $object
 * @param $body
 *
 * @return mixed
 */
function parse_template($object, $body, bool $escape_values = false)
{
    $allowed_properties = [
        'client_name',
        'client_surname',
        'client_address_1',
        'client_address_2',
        'client_city',
        'client_state',
        'client_zip',
        'client_country',
        'client_phone',
        'client_fax',
        'client_mobile',
        'client_email',
        'client_web',
        'client_vat_id',
        'client_tax_code',
        'client_avs',
        'client_insurednumber',
        'client_weka',
        'user_name',
        'user_company',
        'user_address_1',
        'user_address_2',
        'user_city',
        'user_state',
        'user_zip',
        'user_country',
        'user_phone',
        'user_fax',
        'user_mobile',
        'user_email',
        'user_web',
        'user_vat_id',
        'user_tax_code',
        'user_bank',
        'user_iban',
        'user_bic',
        'user_subscribernumber',
        'user_gln',
        'user_rcc',
        'invoice_number',
        'invoice_terms',
        'quote_number',
        'sumex_reason',
        'sumex_diagnosis',
        'sumex_observations',
        'sumex_treatmentstart',
        'sumex_treatmentend',
        'sumex_casenumber',
    ];

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
                        $replace = in_array($var, $allowed_properties, true) && property_exists($object, $var)
                            ? $object->{$var}
                            : '';
                    }
            }

            if ($escape_values) {
                $replace = htmlspecialchars((string) $replace, ENT_QUOTES, 'UTF-8');
            }

            $body = str_replace('{{{' . $var . '}}}', $replace, $body);
        }
    }

    return $body;
}

/**
 * Returns the translated invoice status.
 *
 * @param $id
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
 * Validates and sanitizes a template name to prevent Local File Inclusion (LFI) and Remote Code Execution (RCE) attacks.
 *
 * Security: This function provides defense-in-depth protection against template-based attacks:
 *
 * 1. Static Whitelist Validation: Only templates in the hardcoded whitelist are allowed.
 *    The whitelist is NEVER constructed by scanning the filesystem at runtime.
 *    This prevents attackers from bypassing validation by writing malicious PHP files to the templates directory.
 *
 * 2. Path Traversal Protection: Rejects any template name containing directory traversal sequences.
 *
 * 3. Filename Sanitization: Validates that the template name contains only safe characters.
 *
 * Attack Prevention:
 * - Prevents RCE by rejecting templates not in the static whitelist
 * - Prevents LFI/path traversal attacks (../, ..\, etc.)
 * - Prevents null byte injection
 * - Prevents absolute path attacks
 *
 * @param string $template_name The template name from settings or user input
 * @param string $type          The template type ('invoice' or 'quote')
 * @param string $scope         The template scope ('public' or 'pdf')
 *
 * @return string|false Returns the validated template name or false if validation fails
 */
function validate_template_name($template_name, $type = 'invoice', $scope = 'pdf')
{
    // Load necessary dependencies
    $CI = & get_instance();
    $CI->load->helper('file_security');
    $CI->load->model('invoices/mdl_templates');

    // Security Layer 1: Reject empty or non-string values
    if (empty($template_name) || ! is_string($template_name)) {
        log_message('error', 'Template validation failed: Empty or invalid template name');

        return false;
    }

    // Security Layer 2: Use file_security_helper to detect path traversal attacks
    $validation = validate_safe_filename($template_name);
    if ( ! $validation['valid']) {
        log_message('error', 'Template validation failed: Unsafe filename detected (hash: ' . $validation['hash'] . ', error: ' . $validation['error'] . ')');

        return false;
    }

    // Security Layer 3: Validate type parameter
    if ( ! in_array($type, ['invoice', 'quote'], true)) {
        $safe_type = sanitize_for_logging((string) $type);
        log_message('error', 'Template validation failed: Invalid template type: ' . $safe_type);

        return false;
    }

    // Security Layer 4: Validate scope parameter
    if ( ! in_array($scope, ['pdf', 'public'], true)) {
        $safe_scope = sanitize_for_logging((string) $scope);
        log_message('error', 'Template validation failed: Invalid template scope: ' . $safe_scope);

        return false;
    }

    // Security Layer 5: Get the STATIC whitelist (NEVER scans filesystem)
    if ($type === 'invoice') {
        $valid_templates = $CI->mdl_templates->get_invoice_templates($scope);
    } else { // $type === 'quote'
        $valid_templates = $CI->mdl_templates->get_quote_templates($scope);
    }

    // Security Layer 6: Strict whitelist validation - CRITICAL SECURITY CONTROL
    // This is the primary defense against RCE. The template name MUST be in the static whitelist.
    // Even if an attacker writes evil.php to the templates directory, it will NOT be in this
    // whitelist and will be rejected.
    if ( ! in_array($template_name, $valid_templates, true)) {
        $safe_template_name = sanitize_for_logging($template_name);
        log_message('error', 'Template validation failed: Template not in static whitelist: ' . $safe_template_name . ' (type: ' . $type . ', scope: ' . $scope . ')');

        return false;
    }

    // Security Layer 7: Additional character validation - only allow safe characters
    // Template names should only contain alphanumeric, spaces, hyphens, and underscores
    // Note: Spaces are allowed to support existing templates like "InvoicePlane - paid"
    // While spaces in filenames can be problematic in some environments, they are safe here
    // because: (1) template names are validated against a static whitelist, (2) they are
    // never used in shell commands, and (3) they match existing production templates
    if ( ! preg_match('/^[a-zA-Z0-9_\- ]+$/', $template_name)) {
        $safe_template_name = sanitize_for_logging($template_name);
        log_message('error', 'Template validation failed: Template name contains invalid characters: ' . $safe_template_name);

        return false;
    }

    // All security layers passed - template name is safe
    return $template_name;
}

/**
 * Constructs and validates a template path with defense-in-depth security.
 *
 * Security: This helper function centralizes template path construction and validation,
 * ensuring consistent security checks across all template loading operations.
 *
 * @param string $template_name    The validated template name (without .php extension)
 * @param string $type             The template type ('invoice' or 'quote')
 * @param string $scope            The template scope ('public' or 'pdf')
 * @param string $default_template The default template to use if validation fails
 *
 * @return array Returns ['path' => string, 'name' => string] with validated path and name
 */
function get_validated_template_path($template_name, $type = 'invoice', $scope = 'public', $default_template = 'InvoicePlane_Web')
{
    // Load file_security helper if not already loaded
    $CI = & get_instance();
    $CI->load->helper('file_security');

    // Security: Validate default template upfront to avoid using it if it's also invalid
    $safe_default_for_log = sanitize_for_logging((string) $default_template);
    $validated_default    = validate_template_name($default_template, $type, $scope);
    if ($validated_default === false) {
        log_message('error', 'Critical: Default template also invalid: ' . $safe_default_for_log);
        show_error('Template system error. Please contact administrator.', 500);
    }

    // Validate the template name
    $validated_name = validate_template_name($template_name, $type, $scope);
    if ($validated_name === false) {
        // Sanitize for logging
        $safe_template_for_log = sanitize_for_logging((string) $template_name);
        log_message('error', 'Invalid template setting: ' . $safe_template_for_log . ', using default: ' . $safe_default_for_log);
        $validated_name = $validated_default;
    }

    // Construct the template path
    // Security: Both $type and $scope have been validated in layers 3-4 above (lines 242-251)
    // to ensure they only contain the values 'invoice'/'quote' and 'pdf'/'public' respectively.
    // This prevents path traversal attacks through the type/scope parameters.
    $template_dir  = $type . '_templates/' . $scope;
    $template_path = APPPATH . 'views/' . $template_dir . '/' . $validated_name . '.php';

    // Defense-in-depth: Validate template path is within allowed directory before checking existence
    $base_directory = APPPATH . 'views/' . $template_dir;
    if ( ! validate_file_in_directory($template_path, $base_directory)) {
        log_message('error', 'Template path validation failed: ' . sanitize_for_logging($validated_name));
        show_error('Template system error. Please contact administrator.', 500);
    }

    // Defense-in-depth: Verify template file exists
    if ( ! file_exists($template_path)) {
        log_message('error', 'Template file not found: ' . sanitize_for_logging($validated_name) . ', using default: ' . $safe_default_for_log);
        $validated_name = $validated_default;
        $template_path  = APPPATH . 'views/' . $template_dir . '/' . $validated_name . '.php';

        // Validate fallback template path is within allowed directory before checking existence
        if ( ! validate_file_in_directory($template_path, $base_directory)) {
            log_message('error', 'Default template path validation failed: ' . sanitize_for_logging($validated_name));
            show_error('Template system error. Please contact administrator.', 500);
        }

        // Critical: If even default doesn't exist, throw error
        if ( ! file_exists($template_path)) {
            log_message('error', 'Critical: Default template file not found for ' . $type . '/' . $scope);
            show_error('Template system error. Please contact administrator.', 500);
        }
    }

    return [
        'path' => $template_dir . '/' . $validated_name . '.php',
        'name' => $validated_name,
    ];
}

/**
 * Validates a PDF template name and returns a safe default if validation fails.
 *
 * Security: This function is specifically for PDF templates loaded from settings or URL parameters.
 * It validates the template name and falls back to the appropriate default template.
 *
 * @param string|null $template_name   The template name to validate
 * @param string      $type            The template type ('invoice' or 'quote')
 * @param string      $default_setting The setting key for the default template (optional)
 *
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
