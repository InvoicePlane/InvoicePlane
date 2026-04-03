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

#[AllowDynamicProperties]
class Guest_Controller extends User_Controller
{
    /** @var array */
    public $user_clients = [];

    /**
     * Guest_Controller constructor.
     */
    public function __construct()
    {
        parent::__construct('user_type', 2);

        $this->load->model('user_clients/mdl_user_clients');

        $user_clients = $this->mdl_user_clients->assigned_to($this->session->userdata('user_id'))->get()->result();

        if ( ! $user_clients) {
            show_error(trans('guest_account_denied'), 403);
            exit;
        }

        foreach ($user_clients as $user_client) {
            $this->user_clients[$user_client->client_id] = $user_client->client_id;
        }
        
        // Automatically filter all POST input to prevent XSS attacks
        // This applies to all guest controllers
        if ($this->input->method() === 'post' && !empty($_POST)) {
            $this->filter_input();
        }
    }
    
    /**
     * Filter and sanitize POST input to prevent XSS attacks
     * Reuses the same logic from Admin_Controller
     */
    protected function filter_input(): void
    {
        // Fields that should bypass XSS sanitization
        $bypass_fields = [
            'user_password',      // User password fields need to allow special characters
            'user_passwordv',     // User password verification field
        ];

        $input = $this->input->post();
        $xss_detected = false;
        $xss_log_entries = [];

        foreach ($input as $key => $value) {
            // Skip bypass fields
            if (in_array($key, $bypass_fields, true)) {
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
            
            // Apply XSS cleaning and strip dangerous tags
            $cleaned_value = $this->security->xss_clean($value);
            $cleaned_value = strip_tags($cleaned_value);

            // Check if value was modified (XSS detected)
            if ($original_value !== $cleaned_value) {
                $xss_detected = true;
                $xss_log_entries[] = [
                    'field' => $key,
                    'original_length' => mb_strlen($original_value),
                    'cleaned_length' => mb_strlen($cleaned_value),
                ];
            }

            // Update the actual POST data
            $_POST[$key] = $cleaned_value;
        }

        // Log XSS detection
        if ($xss_detected) {
            $log_context = [
                'timestamp' => date('Y-m-d H:i:s'),
                'user_id'   => $this->session->userdata('user_id'),
                'uri'       => uri_string(),
                'ip_address' => $this->input->ip_address(),
                'user_agent' => $this->input->user_agent(),
                'fields'    => $xss_log_entries,
            ];

            $json_flags = JSON_PARTIAL_OUTPUT_ON_ERROR;
            if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
                $json_flags |= JSON_INVALID_UTF8_SUBSTITUTE;
            }

            $log_payload = json_encode($log_context, $json_flags);

            if ($log_payload === false) {
                $log_payload = 'JSON_ENCODE_ERROR: ' . json_last_error_msg() . ' | CONTEXT: ' . print_r($log_context, true);
            }

            log_message('error', 'XSS attempt detected and cleaned (Guest): ' . $log_payload);
        }
    }

    /**
     * Recursively sanitize array values
     * 
     * @param array $data The array to sanitize
     * @param array $bypass_keys Keys that should bypass sanitization
     * @param string $path_prefix Prefix for tracking nested field paths
     * @param bool $xss_detected Reference to XSS detection flag
     * @param array $xss_log_entries Reference to XSS log entries array
     */
    private function sanitize_array(
        array $data,
        array $bypass_keys = [],
        string $path_prefix = '',
        bool &$xss_detected = false,
        array &$xss_log_entries = []
    ): array
    {
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
                $cleaned_value = strip_tags($this->security->xss_clean($value));
                if ($original_value !== $cleaned_value) {
                    $xss_detected = true;
                    $xss_log_entries[] = [
                        'field' => $path_prefix === '' ? (string) $key : $path_prefix . '.' . $key,
                        'original_length' => mb_strlen((string) $original_value),
                        'cleaned_length' => mb_strlen((string) $cleaned_value),
                    ];
                }
                $data[$key] = $cleaned_value;
            }
        }
        return $data;
    }
}
