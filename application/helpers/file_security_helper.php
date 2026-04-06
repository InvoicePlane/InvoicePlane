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
 * File Security Helper.
 *
 * Provides reusable security functions for safe file access operations.
 * Protects against path traversal, injection attacks, and other file-related vulnerabilities.
 */

/**
 * Validate that a filename is safe and doesn't contain path traversal sequences.
 *
 * @param string $filename The filename to validate
 *
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
    if (str_contains($filename, '../')
        || str_contains($filename, '..\\')
        || str_contains($filename, '/..')
        || str_contains($filename, '\\..')
        || $filename === '..') {
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
 * Validate that a resolved file path is within the allowed base directory.
 *
 * @param string $filePath      The full file path to validate
 * @param string $baseDirectory The base directory that should contain the file
 *
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
 * Sanitize a filename for use in HTTP headers to prevent header injection.
 *
 * @param string $filename The filename to sanitize
 *
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
 * Get a safe hash of a filename for logging purposes.
 *
 * Prevents log injection attacks by never logging untrusted data directly
 *
 * @param string $filename The filename to hash
 *
 * @return string SHA256 hash of the filename
 */
function hash_for_logging(string $filename): string
{
    return hash('sha256', $filename);
}

/**
 * Extract safe filename using basename and validate it.
 *
 * @param string $filename The filename to extract
 *
 * @return array Array with 'valid' (bool), 'filename' (string), and 'hash' (string) keys
 */
function extract_safe_basename(string $filename): array
{
    $hash         = hash('sha256', $filename);
    $safeFilename = basename($filename);

    if (empty($safeFilename)) {
        return ['valid' => false, 'filename' => '', 'hash' => $hash, 'error' => 'empty_basename'];
    }

    return ['valid' => true, 'filename' => $safeFilename, 'hash' => $hash];
}

/**
 * Sanitize a string for logging by removing control characters.
 *
 * Prevents log injection attacks by stripping newlines and other control characters
 * while preserving the original content for debugging purposes.
 *
 * @param string $value The value to sanitize for logging
 *
 * @return string The sanitized string safe for logging
 */
function sanitize_for_logging(string $value): string
{
    // Remove carriage return and line feed characters to prevent log injection
    return str_replace(["\r", "\n"], '', $value);
}

/**
 * Comprehensive file security validation.
 *
 * Performs all security checks in one function for convenience.
 * Use this for the most common case: validate filename and ensure it's in the target directory.
 *
 * @param string $filename      The user-provided filename
 * @param string $baseDirectory The base directory that should contain files
 *
 * @return array Array with 'valid' (bool), 'path' (string), 'hash' (string), and optional 'error' keys
 */
function validate_file_access(string $filename, string $baseDirectory): array
{
    // Step 1: Validate filename doesn't contain malicious sequences
    $validation = validate_safe_filename($filename);
    if ( ! $validation['valid']) {
        log_message('error', sprintf('File security: Invalid filename detected (hash: %s, error: %s)', $validation['hash'], $validation['error']));

        return $validation;
    }

    // Step 2: Extract basename to remove directory components
    $basename = extract_safe_basename($filename);
    if ( ! $basename['valid']) {
        log_message('error', sprintf('File security: Basename extraction failed (hash: %s)', $basename['hash']));

        return $basename;
    }

    // Step 3: Construct full path
    $fullPath = rtrim($baseDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $basename['filename'];

    // Step 4: Check if file exists
    if ( ! file_exists($fullPath)) {
        log_message('debug', 'File security: File not found in allowed directory');

        return ['valid' => false, 'hash' => $validation['hash'], 'error' => 'file_not_found'];
    }

    // Step 5: Validate resolved path is within base directory
    if ( ! validate_file_in_directory($fullPath, $baseDirectory)) {
        log_message('error', sprintf('File security: Path traversal detected - file outside base directory (hash: %s)', $validation['hash']));

        return ['valid' => false, 'hash' => $validation['hash'], 'error' => 'path_outside_directory'];
    }

    // All checks passed
    return [
        'valid'    => true,
        'path'     => realpath($fullPath),
        'basename' => $basename['filename'],
        'hash'     => $validation['hash'],
    ];
}

/**
 * Respond with an HTTP message and exit.
 *
 * Centralized function for handling file operation responses (success or error).
 * Logs the message, sets the HTTP status code, outputs the translated message, and exits.
 *
 * @param int    $httpCode         HTTP status code (e.g., 200, 400, 404, 403)
 * @param string $messageKey       Translation key for the response message
 * @param string $dynamicLogValue  Optional additional context for logging (default: '')
 * @param string $contextPrefix    Optional prefix for log messages (e.g., 'guest/get: ', 'upload: ')
 * @param string $additionalOutput Optional additional output for specific status codes (default: '')
 *
 * @return void This function never returns (calls exit)
 */
function respond_file_message(int $httpCode, string $messageKey, string $dynamicLogValue = '', string $contextPrefix = '', string $additionalOutput = ''): void
{
    // Security: Sanitize dynamic log value to prevent log injection
    $sanitizedLogValue = sanitize_for_logging($dynamicLogValue);
    log_message('debug', $contextPrefix . trans($messageKey) . ': (status ' . $httpCode . ') ' . $sanitizedLogValue);
    http_response_code($httpCode);
    _trans($messageKey);

    if ( ! empty($additionalOutput)) {
        echo $additionalOutput;
    }

    exit;
}

/**
 * Strip EXIF metadata from an image file to protect user privacy.
 *
 * Removes GPS coordinates, timestamps, device information, and other metadata
 * that could expose sensitive information. Supports JPEG, PNG, GIF, and WEBP formats.
 *
 * This function respects the SEC_STRIP_EXIF_FROM_IMAGES configuration setting.
 * If the setting is disabled (false), the function returns success without processing.
 *
 * @param string $filePath The full path to the image file to process
 *
 * @return array Array with 'success' (bool), 'message' (string), and optional 'error' keys
 */
function strip_exif_metadata(string $filePath): array
{
    // Check if EXIF stripping is enabled via configuration
    // Default to false if not set
    $stripExifEnabled = env_bool('SEC_STRIP_EXIF_FROM_IMAGES', 'false');

    if ( ! $stripExifEnabled) {
        // Feature is disabled, return success without processing
        return [
            'success' => true,
            'message' => 'EXIF stripping is disabled',
            'skipped' => true,
        ];
    }

    // Validate file exists
    if ( ! file_exists($filePath)) {
        return [
            'success' => false,
            'message' => 'File does not exist',
            'error'   => 'file_not_found',
        ];
    }

    // Get file extension
    $extension = mb_strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

    // Only process image files that can contain EXIF data
    $processableFormats = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if ( ! in_array($extension, $processableFormats, true)) {
        // Not an image or not a format that typically contains EXIF - skip processing
        return [
            'success' => true,
            'message' => 'File format does not require EXIF stripping',
            'skipped' => true,
        ];
    }

    // Check if GD extension is available
    if ( ! extension_loaded('gd')) {
        log_message('warning', 'GD extension not available for EXIF stripping');

        return [
            'success' => false,
            'message' => 'GD extension not available',
            'error'   => 'gd_not_available',
        ];
    }

    try {
        // Load the image based on its type
        $image = false;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $image = @imagecreatefromjpeg($filePath);
                break;
            case 'png':
                $image = @imagecreatefrompng($filePath);
                break;
            case 'gif':
                $image = @imagecreatefromgif($filePath);
                break;
            case 'webp':
                if (function_exists('imagecreatefromwebp')) {
                    $image = @imagecreatefromwebp($filePath);
                }
                break;
        }

        if ($image === false) {
            log_message('error', 'Failed to load image for EXIF stripping: ' . sanitize_for_logging(basename($filePath)));

            return [
                'success' => false,
                'message' => 'Failed to load image',
                'error'   => 'image_load_failed',
            ];
        }

        // Save the image back to the same file, which strips EXIF metadata
        $saveSuccess = false;
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                $saveSuccess = @imagejpeg($image, $filePath, 90); // 90 quality to maintain good image quality
                break;
            case 'png':
                $saveSuccess = @imagepng($image, $filePath, 6); // 6 compression level (balanced)
                break;
            case 'gif':
                $saveSuccess = @imagegif($image, $filePath);
                break;
            case 'webp':
                if (function_exists('imagewebp')) {
                    $saveSuccess = @imagewebp($image, $filePath, 90); // 90 quality
                }
                break;
        }

        // Free up memory
        imagedestroy($image);

        if ( ! $saveSuccess) {
            log_message('error', 'Failed to save image after EXIF stripping: ' . sanitize_for_logging(basename($filePath)));

            return [
                'success' => false,
                'message' => 'Failed to save processed image',
                'error'   => 'image_save_failed',
            ];
        }

        return [
            'success' => true,
            'message' => 'EXIF metadata stripped successfully',
        ];
    } catch (Exception $e) {
        log_message('error', 'Exception during EXIF stripping: ' . sanitize_for_logging($e->getMessage()));

        return [
            'success' => false,
            'message' => 'Exception during processing',
            'error'   => 'exception',
        ];
    }
}
