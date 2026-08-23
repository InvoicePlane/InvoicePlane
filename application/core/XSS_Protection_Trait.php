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
 * XSS Protection Trait.
 *
 * Provides XSS filtering methods for controllers to prevent cross-site scripting attacks.
 * Used by Admin_Controller and Guest_Controller to ensure consistent input sanitization.
 */
trait XSS_Protection_Trait
{
    /**
     * Filter and sanitize POST input to prevent XSS attacks.
     *
     * This method processes all POST data and applies appropriate sanitization:
     * - HTML fields (email_template_body, body): Sanitized with HTML Purifier
     * - Bypass fields (passwords): No sanitization to allow special characters
     * - All other fields: XSS cleaned and tags stripped
     *
     * @return void
     */
    protected function filter_input(): void
    {
        // Load file_security helper early so sanitize_for_logging() is always available,
        // even when XSS modification is detected before the logging block is reached.
        $this->load->helper('file_security');

        // Fields that should bypass XSS sanitization
        $bypass_fields = [
            'user_password',      // User password fields need to allow special characters
            'user_passwordv',     // User password verification field
            // invoices/controllers/Ajax.php::save() runs its own stricter allowlist regex
            // on invoice_number and rejects the request outright (not silently modifies)
            // when it contains control characters or <>"'. xss_clean() HTML-entity-encodes
            // those same characters instead of stripping them, so by the time that check ran
            // it never saw a rejectable character and let payloads like "<script>" through
            // (still safe on output, which is _htmlsc()'d, but defeats the explicit-reject
            // design). Bypassing here restores that: the regex is the sole gate for this field.
            'invoice_number',
        ];

        // Fields that require special HTML sanitization (not bypass, but custom handling)
        $html_fields = [
            'email_template_body', // Email templates can contain HTML but need HTML Purifier
            'body',                // Email body when sending invoices/quotes
        ];

        $input           = $this->input->post();
        $xss_detected    = false;
        $xss_log_entries = [];

        // Load HTML sanitizer helper once before processing HTML fields
        $html_sanitizer_loaded = false;

        foreach ($input as $key => $value) {
            // Skip bypass fields
            if (in_array($key, $bypass_fields, true)) {
                continue;
            }

            // Handle HTML fields with HTML Purifier
            if (in_array($key, $html_fields, true)) {
                if ( ! $html_sanitizer_loaded) {
                    $this->load->helper('html_sanitizer');
                    $html_sanitizer_loaded = true;
                }

                $original_value = $value;
                $cleaned_value  = sanitize_email_template_html($value);

                // Check if value was modified (potential XSS detected)
                if ($original_value !== $cleaned_value) {
                    $xss_detected      = true;
                    $xss_log_entries[] = [
                        'field'           => $key,
                        'original_length' => mb_strlen($original_value),
                        'cleaned_length'  => mb_strlen($cleaned_value),
                        'type'            => 'html_purifier',
                    ];
                }

                $_POST[$key] = $cleaned_value;
                continue;
            }

            // Recursively sanitize arrays
            if (is_array($value)) {
                $_POST[$key] = $this->sanitize_array(
                    $value,
                    $bypass_fields,
                    $key,
                    $xss_detected,
                    $xss_log_entries
                );
                continue;
            }

            $original_value = $value;

            // Apply XSS cleaning
            // Note: We don't use html_escape here to avoid double-encoding at output.
            // No additional strip_tags() pass: xss_clean() already neutralizes dangerous
            // markup/attributes, and strip_tags() deletes any bracketed text (e.g. "<5cm>")
            // regardless of whether it's a real tag, corrupting legitimate input.
            $cleaned_value = $this->security->xss_clean($value);

            // Check if value was modified (XSS detected)
            if ($original_value !== $cleaned_value) {
                $xss_detected = true;
                // Sanitize field name to prevent log injection
                $xss_log_entries[] = [
                    'field'           => sanitize_for_logging($key),
                    'original_length' => mb_strlen($original_value),
                    'cleaned_length'  => mb_strlen($cleaned_value),
                ];
            }

            // Update the actual POST data
            // Note: Direct modification needed as Input class caches POST data
            $_POST[$key] = $cleaned_value;
        }

        // Log XSS detection
        if ($xss_detected) {
            $controller_type = $this instanceof Admin_Controller ? 'Admin' : 'Guest';

            // Sanitize user-influenced values to prevent log injection
            $log_context = [
                'timestamp'  => date('Y-m-d H:i:s'),
                'user_id'    => $this->session->userdata('user_id'),
                'uri'        => sanitize_for_logging(uri_string()),
                'ip_address' => $this->input->ip_address(),
                'user_agent' => sanitize_for_logging($this->input->user_agent()),
                'fields'     => $xss_log_entries,
            ];

            $json_flags = JSON_PARTIAL_OUTPUT_ON_ERROR;
            if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
                $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
            }

            $log_payload = json_encode($log_context, $json_flags);

            if ($log_payload === false) {
                $log_payload = 'JSON_ENCODE_ERROR: ' . json_last_error_msg() . ' | CONTEXT: ' . print_r($log_context, true);
            }

            log_message('error', 'XSS attempt detected and cleaned (' . $controller_type . '): ' . $log_payload);
        }
    }

    /**
     * Recursively sanitize array values.
     *
     * @param array  $data            The array to sanitize
     * @param array  $bypass_keys     Keys that should bypass sanitization
     * @param string $path_prefix     Prefix for tracking nested field paths
     * @param bool   $xss_detected    Reference to XSS detection flag
     * @param array  $xss_log_entries Reference to XSS log entries array
     *
     * @return array Sanitized array
     */
    private function sanitize_array(
        array $data,
        array $bypass_keys = [],
        string $path_prefix = '',
        bool &$xss_detected = false,
        array &$xss_log_entries = []
    ): array {
        foreach ($data as $key => $value) {
            // Skip bypass fields
            if (in_array($key, $bypass_keys, true)) {
                continue;
            }

            if (is_array($value)) {
                $data[$key] = $this->sanitize_array(
                    $value,
                    $bypass_keys,
                    $path_prefix === '' ? (string) $key : $path_prefix . '.' . $key,
                    $xss_detected,
                    $xss_log_entries
                );
            } else {
                $original_value = $value;
                $cleaned_value  = $this->security->xss_clean($value);
                if ($original_value !== $cleaned_value) {
                    $xss_detected = true;
                    // Sanitize field path to prevent log injection
                    $field_path        = $path_prefix === '' ? (string) $key : $path_prefix . '.' . $key;
                    $xss_log_entries[] = [
                        'field'           => sanitize_for_logging($field_path),
                        'original_length' => mb_strlen((string) $original_value),
                        'cleaned_length'  => mb_strlen((string) $cleaned_value),
                    ];
                }
                $data[$key] = $cleaned_value;
            }
        }

        return $data;
    }
}
