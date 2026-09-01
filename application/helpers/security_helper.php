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
 * When csrf_protection is enabled, CodeIgniter's Security::csrf_verify() has
 * already fully validated the request during bootstrap (see the vendored
 * system/core/Security.php): a forged or tokenless POST is aborted with a 403
 * before any controller code runs, and a *valid* POST has its token removed
 * from $_POST — and, with csrf_regenerate on, its cookie rotated too. Re-reading
 * $_POST / $_COOKIE here would then see an empty token and reject every
 * legitimate state-changing POST, which is exactly the "unable to delete
 * invoice" bug (issue #1694). So on an enabled-protection POST we trust the
 * framework check that already happened; the explicit double-submit comparison
 * below only guards the residual cases (protection disabled, or a non-POST
 * caller where CI3's csrf_verify() never ran).
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

    // CodeIgniter's global CSRF check already ran and passed for this POST
    // (it consumes $_POST[$token_name] on success). Anything that failed its
    // check never reaches a controller. Trust that instead of re-validating a
    // token the framework deliberately cleared.
    $request_method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? ''));
    if ($request_method === 'POST' && $submitted_token === null && isset($CI->security)) {
        return true;
    }

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
 * Verify a CSRF token supplied as a request (query-string) parameter.
 *
 * Security: gates state-changing side effects that ride along a route which must
 * stay GET-accessible (for example the "generate PDF" link that also marks an
 * invoice as sent). A forged cross-site request — an `<img src="…">` tag, a link
 * prefetch — cannot read the victim's CSRF cookie, so it cannot echo a matching
 * token in the query string and the side effect is skipped. Same-origin UI links
 * embed the token via `_csrf_query()` and pass the check. The read part of the
 * route (streaming the PDF) is left untouched.
 *
 * @return bool True if the query-string token matches the CSRF cookie, false otherwise
 */
function verify_get_csrf_token(): bool
{
    $CI = & get_instance();

    // Check if CSRF protection is enabled
    if ( ! config_item('csrf_protection')) {
        return true;
    }

    // Token echoed back by the same-origin link
    $token_name      = config_item('csrf_token_name');
    $submitted_token = $CI->input->get($token_name);

    // Token the browser holds in the CSRF cookie
    $cookie_name    = config_item('csrf_cookie_name');
    $expected_token = $CI->input->cookie($cookie_name);

    // Enforce non-empty string tokens to prevent bypass when both are null
    if ( ! is_string($submitted_token) || $submitted_token === '') {
        return false;
    }

    if ( ! is_string($expected_token) || $expected_token === '') {
        return false;
    }

    // Timing-safe comparison
    return hash_equals($expected_token, $submitted_token);
}
