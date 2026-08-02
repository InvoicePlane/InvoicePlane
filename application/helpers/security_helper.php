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
 *
 * Compatibility note:
 * This application helper shares its name with CodeIgniter's core `security`
 * helper. To avoid changing the behavior of `$this->load->helper('security')`,
 * load the core helper explicitly before defining the custom helper functions
 * in this file.
 */
$core_security_helper = BASEPATH . 'helpers/security_helper.php';

if (is_file($core_security_helper)) {
    require_once $core_security_helper;
}
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
 * @param string $referer     Optional referer URL to validate (defaults to HTTP_REFERER)
 * @param string $default_url Optional default URL to use if referer is invalid
 *
 * @return string Safe referer URL (internal to application) or default URL
 */
function get_safe_referer($referer = '', $default_url = '')
{
    $CI = & get_instance();
    $CI->load->helper('file_security');

    // Use provided referer or HTTP_REFERER
    $referer = empty($referer) ? ($_SERVER['HTTP_REFERER'] ?? '') : $referer;

    // If no referer, use default
    if (empty($referer)) {
        return empty($default_url) ? base_url() : $default_url;
    }

    // Additional validation: ensure no control characters or XSS attempts
    if (preg_match('/[\x00-\x1F\x7F<>]/', $referer)) {
        log_message('error', 'Invalid characters in referer URL (hash: ' . hash('sha256', $referer) . ')');

        return empty($default_url) ? base_url() : $default_url;
    }

    // Parse the base URL and referer to compare host and port properly
    $base_url      = base_url();
    $base_parts    = parse_url($base_url);
    $referer_parts = parse_url($referer);

    // Validate parse results
    if ($base_parts === false || $referer_parts === false) {
        log_message('error', 'Failed to parse URL for validation (hash: ' . hash('sha256', $referer) . ')');

        return empty($default_url) ? base_url() : $default_url;
    }

    // Extract and validate host
    $base_host    = $base_parts['host'] ?? '';
    $referer_host = $referer_parts['host'] ?? '';

    if (empty($base_host) || empty($referer_host)) {
        log_message('error', 'Missing host in URL validation (hash: ' . hash('sha256', $referer) . ')');

        return empty($default_url) ? base_url() : $default_url;
    }

    // Security: Compare host and scheme to ensure same domain
    // This prevents false positives from prefix matching and handles scheme/port differences
    $base_scheme    = $base_parts['scheme'] ?? 'http';
    $referer_scheme = $referer_parts['scheme'] ?? 'http';
    $base_port      = $base_parts['port'] ?? ($base_scheme === 'https' ? 443 : 80);
    $referer_port   = $referer_parts['port'] ?? ($referer_scheme === 'https' ? 443 : 80);

    // Check if host, scheme, and port match
    if ($base_host === $referer_host && $base_scheme === $referer_scheme && $base_port === $referer_port) {
        return $referer;
    }

    // Referer is external or invalid, use safe default
    // Sanitize the host before logging to prevent log injection
    $safe_host = sanitize_for_logging($referer_host);
    log_message('debug', 'External referer blocked: ' . $safe_host);

    return empty($default_url) ? base_url() : $default_url;
}

/**
 * Validate and sanitize a redirect URL parameter.
 *
 * Security: Ensures redirect URLs are internal only and properly encoded.
 * Prevents open redirect vulnerabilities.
 *
 * @param string $url         The URL to validate
 * @param string $default_url Optional default URL if validation fails
 *
 * @return string Safe URL for redirect
 */
function validate_redirect_url($url, $default_url = '')
{
    $CI = & get_instance();
    $CI->load->helper('file_security');

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

    // External URL - reject and sanitize host before logging
    $url_host  = parse_url($url, PHP_URL_HOST);
    $safe_host = is_string($url_host) ? sanitize_for_logging($url_host) : 'invalid';
    log_message('debug', 'External redirect URL blocked: ' . $safe_host);

    return empty($default_url) ? base_url() : $default_url;
}

/**
 * Escape a URL for safe output in HTML context.
 *
 * Security: Prevents XSS attacks when outputting URLs in HTML attributes or JavaScript.
 * Should be used whenever outputting a URL that might contain user input.
 *
 * @param string $url The URL to escape
 *
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
 *
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
    if ( ! config_item('csrf_protection')) {
        return true;
    }

    // Get CSRF token from POST data
    $token_name      = config_item('csrf_token_name');
    $submitted_token = $CI->input->post($token_name);

    // Get CSRF token from cookie
    $cookie_name    = config_item('csrf_cookie_name');
    $expected_token = $CI->input->cookie($cookie_name);

    // Security: Enforce non-empty string tokens to prevent bypass when both are null
    if ( ! is_string($submitted_token) || empty($submitted_token)) {
        log_message('error', 'CSRF validation failed: Missing or invalid submitted token');

        return false;
    }

    if ( ! is_string($expected_token) || empty($expected_token)) {
        log_message('error', 'CSRF validation failed: Missing or invalid expected token');

        return false;
    }

    // Security: Use timing-safe comparison to prevent timing attacks
    if (hash_equals($expected_token, $submitted_token)) {
        return true;
    }

    // Token mismatch - sanitize IP before logging to prevent log injection
    $ip_address = $CI->input->ip_address();
    $safe_ip    = filter_var($ip_address, FILTER_VALIDATE_IP);
    if ($safe_ip === false) {
        $safe_ip = 'invalid-ip';
    }
    log_message('error', 'CSRF token mismatch from IP: ' . $safe_ip);

    return false;
}

/**
 * Generate a cryptographically secure random token.
 *
 * Security: Uses random_bytes() (a CSPRNG) rather than random_string('alnum', ...),
 * whose alnum mode is backed by str_shuffle()/mt_rand() (the Mersenne Twister PRNG)
 * and is NOT cryptographically secure. These tokens (invoice_url_key, quote_url_key,
 * cron_key) are the sole access-control mechanism for unauthenticated access to
 * sensitive financial data, so they must be unpredictable (CWE-338 / CWE-330).
 *
 * The result is lowercase hexadecimal of exactly $length characters, giving
 * 4 bits of entropy per character (128 bits for the default 32-character token).
 *
 * @param int $length Number of hexadecimal characters to return (default 32).
 *
 * @return string Lowercase hexadecimal token of exactly $length characters.
 */
function generate_secure_token(int $length = 32): string
{
    if ($length < 1) {
        $length = 32;
    }

    // Each byte yields two hex characters; generate enough bytes then truncate.
    $bytes = random_bytes((int) ceil($length / 2));

    return substr(bin2hex($bytes), 0, $length);
}
