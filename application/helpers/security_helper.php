<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/*
 * InvoicePlane
 *
 * @author      InvoicePlane Developers & Contributors
 * @copyright   Copyright (c) 2012 - 2026 InvoicePlane.com
 * @license     https://invoiceplane.com/license.txt
 * @link        https://invoiceplane.com
 */

/**
 * Security Helper.
 *
 * Provides reusable security functions for common security operations.
 * Protects against open redirects, XSS, and other web vulnerabilities.
 */

/**
 * Get a safe referer URL, preventing open redirect attacks.
 * 
 * Security: Only allows referer URLs that belong to the same application domain.
 * External URLs are rejected and replaced with a safe default URL.
 * 
 * This prevents open redirect attacks (CWE-601) where an attacker could craft
 * a malicious link that redirects users to a phishing site after performing
 * an action on the legitimate site.
 *
 * @param string $referer Optional referer URL to validate (defaults to HTTP_REFERER)
 * @param string $default_url Optional default URL to use if referer is invalid
 * @return string Safe referer URL (internal to application) or default URL
 */
function get_safe_referer($referer = '', $default_url = '')
{
    $CI = & get_instance();
    
    // Use provided referer or HTTP_REFERER
    $referer = empty($referer) ? ($_SERVER['HTTP_REFERER'] ?? '') : $referer;
    
    // If no referer, use default
    if (empty($referer)) {
        return empty($default_url) ? base_url() : $default_url;
    }
    
    // Get base URL
    $base_url = base_url();
    
    // Security: Check if referer starts with base URL (same domain)
    // This prevents open redirect attacks by only allowing internal URLs
    if (str_starts_with($referer, $base_url)) {
        // Additional validation: ensure no control characters or XSS attempts
        if (preg_match('/[\x00-\x1F\x7F<>]/', $referer)) {
            log_message('error', 'Invalid characters in referer URL (hash: ' . hash('sha256', $referer) . ')');
            return empty($default_url) ? base_url() : $default_url;
        }
        
        return $referer;
    }
    
    // Referer is external or invalid, use safe default
    log_message('debug', 'External referer blocked: ' . parse_url($referer, PHP_URL_HOST));
    return empty($default_url) ? base_url() : $default_url;
}

/**
 * Validate and sanitize a redirect URL parameter.
 * 
 * Security: Ensures redirect URLs are internal only and properly encoded.
 * Prevents open redirect vulnerabilities.
 *
 * @param string $url The URL to validate
 * @param string $default_url Optional default URL if validation fails
 * @return string Safe URL for redirect
 */
function validate_redirect_url($url, $default_url = '')
{
    // Empty URL - use default
    if (empty($url)) {
        return empty($default_url) ? base_url() : $default_url;
    }
    
    // Check if it's a relative URL (starts with /)
    if (str_starts_with($url, '/')) {
        // Relative URL - prepend base_url
        return base_url(ltrim($url, '/'));
    }
    
    // Check if it's an absolute URL on same domain
    $base_url = base_url();
    if (str_starts_with($url, $base_url)) {
        return $url;
    }
    
    // External URL - reject
    log_message('debug', 'External redirect URL blocked: ' . parse_url($url, PHP_URL_HOST));
    return empty($default_url) ? base_url() : $default_url;
}

/**
 * Escape a URL for safe output in HTML context.
 * 
 * Security: Prevents XSS attacks when outputting URLs in HTML attributes or JavaScript.
 * Should be used whenever outputting a URL that might contain user input.
 *
 * @param string $url The URL to escape
 * @return string HTML-safe URL
 */
function escape_url_for_output($url)
{
    // Validate it's a safe URL first
    $safe_url = get_safe_referer($url, base_url());
    
    // HTML-escape for safe output
    return htmlspecialchars($safe_url, ENT_QUOTES, 'UTF-8');
}

/**
 * Escape a URL for safe output in JavaScript context.
 * 
 * Security: Prevents XSS attacks when embedding URLs in JavaScript code.
 * Uses JavaScript-safe encoding.
 *
 * @param string $url The URL to escape
 * @return string JavaScript-safe URL
 */
function escape_url_for_javascript($url)
{
    // Validate it's a safe URL first
    $safe_url = get_safe_referer($url, base_url());
    
    // JavaScript-escape for safe output
    return json_encode($safe_url, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
}

/**
 * Check if current user has access to a specific invoice.
 * 
 * Security: Prevents IDOR (Insecure Direct Object Reference) vulnerabilities
 * by verifying the user owns or has access to the requested invoice.
 *
 * @param int $invoice_id The invoice ID to check
 * @return bool True if user has access, false otherwise
 */
function user_has_invoice_access($invoice_id)
{
    $CI = & get_instance();

    // Admin users have access to all invoices
    if ($CI->session->userdata('user_type') === 1) {
        return true;
    }

    // Guest users - check if invoice belongs to their assigned clients
    if ($CI->session->userdata('user_type') === 2) {
        $CI->load->model('invoices/mdl_invoices');
        $CI->load->model('user_clients/mdl_user_clients');

        $invoice = $CI->mdl_invoices->get_by_id($invoice_id);
        if (!$invoice) {
            return false;
        }

        // Get user's assigned clients
        $user_clients = $CI->mdl_user_clients->assigned_to($CI->session->userdata('user_id'))->get()->result();
        $client_ids = array_column($user_clients, 'client_id');

        return in_array($invoice->client_id, $client_ids, true);
    }

    // Regular users - check if they created the invoice
    $CI->load->model('invoices/mdl_invoices');
    $invoice = $CI->mdl_invoices->get_by_id($invoice_id);

    if (!$invoice) {
        return false;
    }

    return $invoice->user_id === $CI->session->userdata('user_id');
}

/**
 * Check if current user has access to a specific quote.
 * 
 * Security: Prevents IDOR vulnerabilities for quote access.
 *
 * @param int $quote_id The quote ID to check
 * @return bool True if user has access, false otherwise
 */
function user_has_quote_access($quote_id)
{
    $CI = & get_instance();

    // Admin users have access to all quotes
    if ($CI->session->userdata('user_type') === 1) {
        return true;
    }

    // Guest users - check if quote belongs to their assigned clients
    if ($CI->session->userdata('user_type') === 2) {
        $CI->load->model('quotes/mdl_quotes');
        $CI->load->model('user_clients/mdl_user_clients');

        $quote = $CI->mdl_quotes->get_by_id($quote_id);
        if (!$quote) {
            return false;
        }

        // Get user's assigned clients
        $user_clients = $CI->mdl_user_clients->assigned_to($CI->session->userdata('user_id'))->get()->result();
        $client_ids = array_column($user_clients, 'client_id');

        return in_array($quote->client_id, $client_ids, true);
    }

    // Regular users - check if they created the quote
    $CI->load->model('quotes/mdl_quotes');
    $quote = $CI->mdl_quotes->get_by_id($quote_id);

    if (!$quote) {
        return false;
    }

    return $quote->user_id === $CI->session->userdata('user_id');
}

/**
 * Verify CSRF token for state-changing operations.
 *
 * Security: Protects against Cross-Site Request Forgery attacks.
 * Should be called at the beginning of any POST/PUT/DELETE controller action.
 *
 * @return bool True if CSRF token is valid, false otherwise
 */
function verify_csrf_token(): bool
{
    $CI = & get_instance();

    // Check if CSRF protection is enabled
    if (!config_item('csrf_protection')) {
        return true;
    }

    // Get CSRF token from POST data
    $token_name = config_item('csrf_token_name');
    $submitted_token = $CI->input->post($token_name);

    // Get CSRF token from cookie
    $cookie_name = config_item('csrf_cookie_name');
    $expected_token = $CI->input->cookie($cookie_name);

    // Security: Enforce non-empty string tokens to prevent bypass when both are null
    if (!is_string($submitted_token) || empty($submitted_token)) {
        log_message('error', 'CSRF validation failed: Missing or invalid submitted token');
        return false;
    }

    if (!is_string($expected_token) || empty($expected_token)) {
        log_message('error', 'CSRF validation failed: Missing or invalid expected token');
        return false;
    }

    // Security: Use timing-safe comparison to prevent timing attacks
    if (hash_equals($expected_token, $submitted_token)) {
        return true;
    }

    // Token mismatch - sanitize IP before logging to prevent log injection
    $ip_address = $CI->input->ip_address();
    $safe_ip = filter_var($ip_address, FILTER_VALIDATE_IP);
    if ($safe_ip === false) {
        $safe_ip = 'invalid-ip';
    }
    log_message('error', 'CSRF token mismatch from IP: ' . $safe_ip);
    return false;
}