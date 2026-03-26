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
 * Security Helper.
 *
 * Provides cryptographically secure functions for token generation, password reset tokens,
 * and other security-critical operations.
 */

/**
 * Generate a cryptographically secure random token.
 *
 * Uses PHP's random_bytes() with fallback to paragonie/random_compat for older PHP versions.
 * Provides 128+ bits of entropy for security-critical operations like password resets.
 *
 * @param int $length The length of the raw token in bytes (default: 32 bytes = 256 bits)
 *
 * @return string The token as a hexadecimal string (twice the byte length)
 */
function generate_secure_token(int $length = 32): string
{
    try {
        // Generate cryptographically secure random bytes
        $randomBytes = random_bytes($length);

        // Convert to hexadecimal for safe storage and transmission
        return bin2hex($randomBytes);
    } catch (Exception $e) {
        // This should never happen with PHP 7.0+ or random_compat library
        log_message('error', 'Failed to generate secure random token: ' . $e->getMessage());
        throw new RuntimeException('Unable to generate secure random token');
    }
}

/**
 * Generate a cryptographically secure password reset token.
 *
 * Creates a token with 256 bits of entropy (32 bytes), suitable for password reset operations.
 * This replaces the previous insecure implementation that used md5(time() + email + mt_rand()).
 *
 * @return string A 64-character hexadecimal token
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

        // Encode to base64 and take first 22 characters for bcrypt compatibility
        // bcrypt requires exactly 22 characters of base64-encoded salt
        $base64 = base64_encode($randomBytes);

        return substr($base64, 0, 22);
    } catch (Exception $e) {
        log_message('error', 'Failed to generate secure salt: ' . $e->getMessage());
        throw new RuntimeException('Unable to generate secure salt');
    }
}
