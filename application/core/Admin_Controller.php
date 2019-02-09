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
    use XSS_Protection_Trait;

    public function __construct()
    {
        parent::__construct('user_type', 1);
        $this->setCacheHeaders();
        $this->check_setup_security();

        // Automatically filter all POST input to prevent XSS attacks
        // This applies to all admin controllers and prevents the need to call filter_input() manually
        if ($this->input->method() === 'post' && ! empty($_POST)) {
            $this->filter_input();
        }
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

    /**
     * Check if setup wizard is properly disabled and warn admins if not.
     * This security check helps detect misconfigurations that could allow
     * unauthorized access to the setup wizard.
     */
    private function check_setup_security(): void
    {
        // Only check once per session to avoid repeated warnings
        if ($this->session->userdata('setup_security_checked')) {
            return;
        }

        // Default to false (insecure) if flags are not set - this ensures we warn about missing config
        $setup_completed = env_bool('SETUP_COMPLETED', false);
        $disable_setup   = env_bool('DISABLE_SETUP', false);

        // If either flag is not properly set, show a security warning
        if ( ! $setup_completed || ! $disable_setup) {
            $warning_parts = [];

            if ( ! $setup_completed) {
                $warning_parts[] = trans('setup_completed_flag_false');
            }

            if ( ! $disable_setup) {
                $warning_parts[] = trans('disable_setup_flag_false');
            }

            // Format: "Security Warning: [flags]. [description] [instructions]"
            $warning_message = sprintf('%s', trans('security_warning'));

            $this->session->set_flashdata('alert_warning', $warning_message);
        }

        // Mark as checked for this session
        $this->session->set_userdata('setup_security_checked', true);
    }

    /**
     * Recursively sanitize array values.
     *
     * @param array  $data            The array to sanitize
     * @param array  $bypass_keys     Keys that should bypass sanitization
     * @param string $path_prefix     Prefix for tracking nested field paths
     * @param bool   $xss_detected    Reference to XSS detection flag
     * @param array  $xss_log_entries Reference to XSS log entries array
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
                $cleaned_value  = strip_tags($this->security->xss_clean($value));
                if ($original_value !== $cleaned_value) {
                    $xss_detected      = true;
                    $xss_log_entries[] = [
                        'field'           => $path_prefix === '' ? (string) $key : $path_prefix . '.' . $key,
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
