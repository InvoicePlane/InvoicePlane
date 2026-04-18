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

/**
 * InvoicePlane Security Helper.
 *
 * Provides cryptographically secure functions for token generation, password reset tokens,
 * and other security-critical operations. This helper uses PHP's random_bytes() for CSPRNG.
 *
 * Note: This helper is named 'ip_security' to avoid conflicts with CodeIgniter's core
 * 'security' helper which provides different functionality (XSS filtering, CSRF, etc).
 */

/**
 * Sanitize exception message for logging to prevent log injection.
 *
 * @param string $message The exception message to sanitize
 *
 * @return string The sanitized message
 */
function sanitize_exception_for_logging(string $message): string
{
    // Try to use file_security_helper if loaded
    if (function_exists('sanitize_for_logging')) {
        return sanitize_for_logging($message);
    }

    // Fallback: Remove control characters to prevent log injection
    return str_replace(["\r", "\n"], '', $message);
}

/**
 * Generate a cryptographically secure random token.
 *
 * Uses PHP's random_bytes() with fallback to paragonie/random_compat for older PHP versions.
 * Provides 128+ bits of entropy for security-critical operations like password resets.
 *
 * @param int $length The length of the raw token in bytes (default: 32 bytes = 256 bits)
 *                    Must be greater than 0. The returned hex string will be twice this length.
 *
 * @return string The token as a hexadecimal string (twice the byte length)
 *
 * @throws InvalidArgumentException If $length is less than or equal to 0
 * @throws RuntimeException         If random_bytes() fails (should never happen with PHP 7.0+)
 */
function generate_secure_token(int $length = 32): string
{
    // Validate input: length must be positive
    if ($length <= 0) {
        throw new InvalidArgumentException('Token length must be greater than 0, got: ' . $length);
    }

    try {
        // Generate cryptographically secure random bytes
        $randomBytes = random_bytes($length);

        // Convert to hexadecimal for safe storage and transmission
        return bin2hex($randomBytes);
    } catch (Exception|Error $e) {
        // This should never happen with PHP 7.0+ or random_compat library
        // Catch both Exception and Error for comprehensive coverage
        $safeMessage = sanitize_exception_for_logging($e->getMessage());
        log_message('error', 'Failed to generate secure random token: ' . $safeMessage);
        throw new RuntimeException('Unable to generate secure random token', $e->getCode(), $e);
    }
}

/**
 * Generate a cryptographically secure password reset token.
 *
 * Creates a token with 256 bits of entropy (32 bytes), suitable for password reset operations.
 * This replaces the previous insecure implementation that used md5(time() + email + mt_rand()).
 *
 * Note: The old implementation generated 32-character tokens (md5 output).
 *       This new implementation generates 64-character hexadecimal tokens for increased security.
 *
 * @return string A 64-character hexadecimal token (32 bytes of randomness = 256 bits entropy)
 */
function generate_password_reset_token(): string
{
    // Generate 32 bytes (256 bits) of entropy
    // This provides sufficient security against brute force attacks
    return generate_secure_token(32);
}

/**
 * Generate a cryptographically secure salt for password hashing.
 *
 * Creates a 22-character base64-encoded salt suitable for bcrypt password hashing.
 * This replaces the previous insecure implementation that used sha1(mt_rand()).
 *
 * @return string A 22-character base64-encoded salt
 */
function generate_secure_salt(): string
{
    try {
        // Generate 16 bytes (128 bits) of random data
        $randomBytes = random_bytes(16);

        // Encode to base64, normalize to bcrypt's allowed alphabet, and take first 22 characters
        // bcrypt expects the "crypt" base64 alphabet: ./0-9A-Za-z and no '=' padding
        $base64 = base64_encode($randomBytes);
        // Translate '+' to '.' for crypt-style base64 and remove '=' padding
        $base64 = rtrim(strtr($base64, '+', '.'), '=');

        return mb_substr($base64, 0, 22);
    } catch (Exception|Error $e) {
        // Catch both Exception and Error for comprehensive coverage
        $safeMessage = sanitize_exception_for_logging($e->getMessage());
        log_message('error', 'Failed to generate secure salt: ' . $safeMessage);
        throw new RuntimeException('Unable to generate secure salt', $e->getCode(), $e);
    }
}
