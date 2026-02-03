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

#[AllowDynamicProperties]
class Admin_Controller extends User_Controller
{
    public function __construct()
    {
        parent::__construct('user_type', 1);
        $this->setCacheHeaders();
    }

    protected function filter_input(): void
    {
        // Fields that should bypass XSS sanitization
        $bypass_fields = [
            'user_password',      // Password fields need to allow special characters
            'user_passwordv',     // Password verification field
            'email_template_body', // Email templates can contain HTML
        ];

        $input = $this->input->post();
        $xss_detected = false;
        $xss_log_entries = [];

        foreach ($input as $key => $value) {
            // Skip bypass fields
            if (in_array($key, $bypass_fields)) {
                continue;
            }

            // Recursively sanitize arrays
            if (is_array($value)) {
                $_POST[$key] = $this->sanitize_array($value);
                continue;
            }

            $original_value = $value;
            
            // Apply XSS cleaning and strip dangerous tags
            // Note: We don't use html_escape here to avoid double-encoding at output
            $cleaned_value = $this->security->xss_clean($value);
            $cleaned_value = strip_tags($cleaned_value);

            // Check if value was modified (XSS detected)
            if ($original_value !== $cleaned_value) {
                $xss_detected = true;
                $xss_log_entries[] = [
                    'field' => $key,
                    'original_length' => strlen($original_value),
                    'cleaned_length' => strlen($cleaned_value),
                    'ip_address' => $this->input->ip_address(),
                    'user_agent' => $this->input->user_agent(),
                ];
            }

            // Update the actual POST data
            // Note: Direct modification needed as Input class caches POST data
            $_POST[$key] = $cleaned_value;
        }

        // Log XSS detection
        if ($xss_detected) {
            log_message('error', 'XSS attempt detected and cleaned: ' . json_encode([
                'timestamp' => date('Y-m-d H:i:s'),
                'user_id' => $this->session->userdata('user_id'),
                'uri' => uri_string(),
                'fields' => $xss_log_entries,
            ]));
        }
    }

    /**
     * Recursively sanitize array values
     */
    private function sanitize_array(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize_array($value);
            } else {
                $data[$key] = strip_tags($this->security->xss_clean($value));
            }
        }
        return $data;
    }

    protected function setCacheHeaders()
    {
        $this->output
            ->set_header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0')
            ->set_header('Pragma: no-cache')
            ->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');

        $xFrameOptions = env('X_FRAME_OPTIONS');
        if ( ! empty($xFrameOptions)) {
            $this->output->set_header('X-Frame-Options: ' . $xFrameOptions);
        }

        if (env_bool('ENABLE_X_CONTENT_TYPE_OPTIONS', 'true')) {
            $this->output->set_header('X-Content-Type-Options: nosniff');
        }
    }
}
