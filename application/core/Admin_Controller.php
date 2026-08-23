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
            ->set_header('Expires: Sat, 26 Jul 1997 05:00:00 GMT')
            ->set_header('Referrer-Policy: strict-origin-when-cross-origin')
            ->set_header('X-Frame-Options: ' . env('X_FRAME_OPTIONS', 'SAMEORIGIN'))
            // X- csrf token header on all response for XHR (server-side AJAX helper, cookie replacement)
            ->set_header('X-' . config_item('csrf_token_name') . ': ' . $this->security->get_csrf_hash());

        if (env_bool('ENABLE_X_CONTENT_TYPE_OPTIONS', 'true')) {
            $this->output->set_header('X-Content-Type-Options: nosniff');
        }
    }

    protected function ensure_valid_post_request(string $redirect_url): bool
    {
        if ($this->input->method(true) !== 'POST') {
            $this->session->set_flashdata('alert_error', trans('invalid_request'));
            redirect($redirect_url);

            return false;
        }

        if ( ! function_exists('verify_csrf_token')) {
            $this->load->helper('security');
        }

        if ( ! verify_csrf_token()) {
            $this->session->set_flashdata('alert_error', trans('invalid_request'));
            redirect($redirect_url);

            return false;
        }

        return true;
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
        $disable_setup = env_bool('DISABLE_SETUP', false);

        // If either flag is not properly set, show a security warning
        if ( ! $setup_completed || ! $disable_setup) {
            $warning_parts = [];

            if ( ! $setup_completed) {
                $warning_parts[] = trans('setup_completed_flag_false');
            }

            if ( ! $disable_setup) {
                $warning_parts[] = trans('disable_setup_flag_false');
            }

            $warning_message = sprintf(
                '%s - %s. %s %s',
                trans('security_warning'),
                implode(', ', $warning_parts),
                trans('setup_wizard_accessible'),
                trans('please_update_ipconfig')
            );

            $this->session->set_flashdata('alert_warning', $warning_message);
        }

        // Mark as checked for this session
        $this->session->set_userdata('setup_security_checked', true);
    }
}
