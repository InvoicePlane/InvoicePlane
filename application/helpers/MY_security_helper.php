<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2025 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

if ( ! function_exists('verify_csrf_token')) {
    /**
     * Verify the CSRF token submitted with the request.
     *
     * Checks that the request includes the application CSRF token matching the
     * value stored in the session (or cookie, depending on CI3 CSRF config).
     * Returns false when the token is absent or does not match, allowing the
     * caller to reject the request.
     */
    function verify_csrf_token(): bool
    {
        $CI = get_instance();

        // Honour the csrf_protection config flag (set to false in test subprocesses).
        if ($CI->config->item('csrf_protection') === false) {
            return true;
        }

        $token_name = $CI->config->item('csrf_token_name') ?: '_ip_csrf';

        $submitted = $CI->input->post($token_name)
            ?? $CI->input->get_request_header('X-CSRF-Token', true);

        if (empty($submitted)) {
            return false;
        }

        $expected = $CI->security->get_csrf_hash();

        return hash_equals((string) $expected, (string) $submitted);
    }
}
