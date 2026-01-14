<?php

if (!defined('BASEPATH')) {
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
 * File Security Helper
 *
 * Provides reusable security functions for safe file access operations.
 * Protects against path traversal, injection attacks, and other file-related vulnerabilities.
 */

/**
 * Validate that a filename is safe and doesn't contain path traversal sequences
 *
 * @param string $filename The filename to validate
 * @return array Array with 'valid' (bool) and 'hash' (string) keys
 */
function validate_safe_filename(string $filename): array
{
    // Compute hash once for secure logging
    $hash = hash('sha256', $filename);

    // Check for empty filename
    if (empty($filename)) {
        return ['valid' => false, 'hash' => $hash, 'error' => 'empty_filename'];
    }

    // Check for path traversal sequences (including standalone ..)
    // Matches: ../, ..\, /.. and standalone ..
    if (str_contains($filename, '../') || 
        str_contains($filename, '..\\') || 
        str_contains($filename, '/..') ||
        str_contains($filename, '\\..') ||
        $filename === '..') {
        return ['valid' => false, 'hash' => $hash, 'error' => 'path_traversal'];
    }

    // Check for null bytes (DoS in PHP 8.1+)
    if (str_contains($filename, "\0")) {
        return ['valid' => false, 'hash' => $hash, 'error' => 'null_byte'];
    }

    // Check for absolute paths
    if (str_starts_with($filename, '/') || str_starts_with($filename, '\\')) {
        return ['valid' => false, 'hash' => $hash, 'error' => 'absolute_path'];
    }

    // Check for Windows drive letters
    if (preg_match('/^[a-zA-Z]:/', $filename)) {
        return ['valid' => false, 'hash' => $hash, 'error' => 'drive_letter'];
    }

    return ['valid' => true, 'hash' => $hash];
}

/**
 * Validate that a resolved file path is within the allowed base directory
 *
 * @param string $filePath The full file path to validate
 * @param string $baseDirectory The base directory that should contain the file
 * @return bool True if the file is within the base directory, false otherwise
 */
function validate_file_in_directory(string $filePath, string $baseDirectory): bool
{
    $realBase = realpath($baseDirectory);
    $realFile = realpath($filePath);

    // Check if both paths resolved successfully
    if ($realBase === false || $realFile === false) {
        return false;
    }

    // Ensure base directory ends with separator for exact matching
    $realBaseWithSep = rtrim($realBase, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

    // Validate file is within base directory
    return str_starts_with($realFile, $realBaseWithSep);
}

/**
 * Sanitize a filename for use in HTTP headers to prevent header injection
 *
 * @param string $filename The filename to sanitize
 * @return string The sanitized filename
 */
function sanitize_filename_for_header(string $filename): string
{
    // Remove all control characters (0x00-0x1F, 0x7F), quotes, and backslashes
    // This prevents CRLF injection and response splitting attacks, including backslash-based bypasses
    $sanitized = preg_replace('/[\x00-\x1F\x7F"\\\\]/', '', $filename);
    
    // If sanitization results in empty string, use safe fallback
    if (empty($sanitized)) {
        return 'attachment.bin';
    }
    
    return $sanitized;
}

/**
 * Get a safe hash of a filename for logging purposes
 *
 * Prevents log injection attacks by never logging untrusted data directly
 *
 * @param string $filename The filename to hash
 * @return string SHA256 hash of the filename
 */
function hash_for_logging(string $filename): string
{
    return hash('sha256', $filename);
}

/**
 * Extract safe filename using basename and validate it
 *
 * @param string $filename The filename to extract
 * @return array Array with 'valid' (bool), 'filename' (string), and 'hash' (string) keys
 */
function extract_safe_basename(string $filename): array
{
    $hash = hash('sha256', $filename);
    $safeFilename = basename($filename);

    if (empty($safeFilename)) {
        return ['valid' => false, 'filename' => '', 'hash' => $hash, 'error' => 'empty_basename'];
    }

    return ['valid' => true, 'filename' => $safeFilename, 'hash' => $hash];
}

/**
 * Comprehensive file security validation
 *
 * Performs all security checks in one function for convenience.
 * Use this for the most common case: validate filename and ensure it's in the target directory.
 *
 * @param string $filename The user-provided filename
 * @param string $baseDirectory The base directory that should contain files
 * @return array Array with 'valid' (bool), 'path' (string), 'hash' (string), and optional 'error' keys
 */
function validate_file_access(string $filename, string $baseDirectory): array
{
    // Step 1: Validate filename doesn't contain malicious sequences
    $validation = validate_safe_filename($filename);
    if (!$validation['valid']) {
        log_message('error', sprintf('File security: Invalid filename detected (hash: %s, error: %s)', $validation['hash'], $validation['error']));
        return $validation;
    }

    // Step 2: Extract basename to remove directory components
    $basename = extract_safe_basename($filename);
    if (!$basename['valid']) {
        log_message('error', sprintf('File security: Basename extraction failed (hash: %s)', $basename['hash']));
        return $basename;
    }

    // Step 3: Construct full path
    $fullPath = rtrim($baseDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $basename['filename'];

    // Step 4: Check if file exists
    if (!file_exists($fullPath)) {
        log_message('debug', 'File security: File not found in allowed directory');
        return ['valid' => false, 'hash' => $validation['hash'], 'error' => 'file_not_found'];
    }

    // Step 5: Validate resolved path is within base directory
    if (!validate_file_in_directory($fullPath, $baseDirectory)) {
        log_message('error', sprintf('File security: Path traversal detected - file outside base directory (hash: %s)', $validation['hash']));
        return ['valid' => false, 'hash' => $validation['hash'], 'error' => 'path_outside_directory'];
    }

    // All checks passed
    return [
        'valid' => true,
        'path' => realpath($fullPath),
        'basename' => $basename['filename'],
        'hash' => $validation['hash']
    ];
}
