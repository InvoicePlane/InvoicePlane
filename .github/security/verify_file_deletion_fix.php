#!/usr/bin/env php
<?php

/**
 * Verification Script for Arbitrary File Deletion Fix.
 *
 * This script verifies that the path traversal vulnerability in logo
 * file deletion has been properly fixed in InvoicePlane v1.7.2+.
 *
 * Usage: php verify_file_deletion_fix.php
 */
echo "=================================================================\n";
echo "InvoicePlane Arbitrary File Deletion Fix Verification\n";
echo "=================================================================\n\n";

// Check if we're in the InvoicePlane root directory
if ( ! file_exists('index.php') || ! file_exists('application/config/config.php')) {
    echo "ERROR: This script must be run from the InvoicePlane root directory\n";
    exit(1);
}

// Define the base path
define('BASEPATH', __DIR__ . '/system/');
define('APPPATH', __DIR__ . '/application/');

// Mock CodeIgniter functions that the helper uses
if ( ! function_exists('log_message')) {
    function log_message($level, $message)
    {
        // Mock implementation - does nothing in test context
        return true;
    }
}

// Load the file security helper
require_once __DIR__ . '/application/helpers/file_security_helper.php';

$all_tests_passed = true;

/**
 * Test helper function.
 */
function test($description, $callback)
{
    global $all_tests_passed;

    echo "Testing: {$description}\n";

    try {
        $result = $callback();
        if ($result) {
            echo "  ✓ PASS\n\n";
        } else {
            echo "  ✗ FAIL\n\n";
            $all_tests_passed = false;
        }
    } catch (Exception $e) {
        echo "  ✗ FAIL - Exception: {$e->getMessage()}\n\n";
        $all_tests_passed = false;
    }
}

// Test 1: Path traversal detection
test('Path traversal with ../ is blocked', function () {
    $result = validate_safe_filename('../../etc/passwd');

    return ! $result['valid'] && $result['error'] === 'path_traversal';
});

test('Path traversal with ..\\ is blocked', function () {
    $result = validate_safe_filename('..\\..\\windows\\system32\\config\\sam');

    return ! $result['valid'] && $result['error'] === 'path_traversal';
});

test('Path traversal with /.. is blocked', function () {
    $result = validate_safe_filename('/../../etc/passwd');

    return ! $result['valid'] && ($result['error'] === 'path_traversal' || $result['error'] === 'absolute_path');
});

test('Path traversal with \\..\\ is blocked', function () {
    $result = validate_safe_filename('\\..\\..\\test.txt');

    return ! $result['valid'] && ($result['error'] === 'path_traversal' || $result['error'] === 'absolute_path');
});

test('Standalone .. is blocked', function () {
    $result = validate_safe_filename('..');

    return ! $result['valid'] && $result['error'] === 'path_traversal';
});

// Test 2: Absolute path detection
test('Absolute Unix path is blocked', function () {
    $result = validate_safe_filename('/etc/passwd');

    return ! $result['valid'] && $result['error'] === 'absolute_path';
});

test('Absolute Windows path is blocked', function () {
    $result = validate_safe_filename('\\Windows\\System32\\config\\sam');

    return ! $result['valid'] && $result['error'] === 'absolute_path';
});

test('Windows drive letter path is blocked', function () {
    $result = validate_safe_filename('C:\\Windows\\System32\\cmd.exe');

    return ! $result['valid'] && $result['error'] === 'drive_letter';
});

// Test 3: Null byte detection
test('Null byte injection is blocked', function () {
    $result = validate_safe_filename('innocent.txt' . chr(0) . 'malicious.php');

    return ! $result['valid'] && $result['error'] === 'null_byte';
});

// Test 4: Empty filename
test('Empty filename is blocked', function () {
    $result = validate_safe_filename('');

    return ! $result['valid'] && $result['error'] === 'empty_filename';
});

// Test 5: Valid filenames
test('Simple filename is allowed', function () {
    $result = validate_safe_filename('logo.png');

    return $result['valid'];
});

test('Filename with numbers is allowed', function () {
    $result = validate_safe_filename('logo-2024.png');

    return $result['valid'];
});

test('Filename with underscores is allowed', function () {
    $result = validate_safe_filename('company_logo_new.jpg');

    return $result['valid'];
});

// Test 6: File access validation (if uploads directory exists)
if (is_dir('./uploads/')) {
    // Create a test file
    $test_file = './uploads/test-verification-' . uniqid() . '.txt';
    file_put_contents($test_file, 'Test file for verification');

    test('Valid file access is allowed', function () use ($test_file) {
        $result   = validate_file_access(basename($test_file), './uploads/');
        $is_valid = $result['valid'];

        // Cleanup
        if (file_exists($test_file)) {
            unlink($test_file);
        }

        return $is_valid;
    });

    test('Path traversal in file access is blocked', function () {
        $result = validate_file_access('../../config/database.php', './uploads/');

        return ! $result['valid'];
    });

    test('Non-existent file access returns proper error', function () {
        $result = validate_file_access('nonexistent-file-12345.txt', './uploads/');

        return ! $result['valid'] && $result['error'] === 'file_not_found';
    });
} else {
    echo "NOTE: ./uploads/ directory not found, skipping file access tests\n\n";
}

// Test 7: Settings controller exists and has the fix
test('Settings controller has validate_safe_filename check', function () {
    $settings_file = './application/modules/settings/controllers/Settings.php';

    if ( ! file_exists($settings_file)) {
        echo "  WARNING: Settings.php not found\n";

        return false;
    }

    $content = file_get_contents($settings_file);

    // Check for the fix markers
    $has_validation = str_contains($content, 'validate_safe_filename');
    $has_logo_check = str_contains($content, "key === 'invoice_logo' || \$key === 'login_logo'")
                      || str_contains($content, '$key === \'invoice_logo\' || $key === \'login_logo\'');

    if ( ! $has_validation) {
        echo "  WARNING: validate_safe_filename not found in Settings.php\n";
    }
    if ( ! $has_logo_check) {
        echo "  WARNING: Logo filename validation check not found in Settings.php\n";
    }

    return $has_validation && $has_logo_check;
});

test('Settings controller has validate_file_access check', function () {
    $settings_file = './application/modules/settings/controllers/Settings.php';

    if ( ! file_exists($settings_file)) {
        return false;
    }

    $content = file_get_contents($settings_file);

    // Check for the remove_logo fix
    $has_file_access_check = str_contains($content, 'validate_file_access');
    $has_allowed_types     = str_contains($content, "allowed_types = ['invoice', 'login']")
                        || str_contains($content, '$allowed_types = [\'invoice\', \'login\']');

    if ( ! $has_file_access_check) {
        echo "  WARNING: validate_file_access not found in Settings.php remove_logo function\n";
    }
    if ( ! $has_allowed_types) {
        echo "  WARNING: Logo type validation not found in Settings.php remove_logo function\n";
    }

    return $has_file_access_check && $has_allowed_types;
});

// Test 8: File security helper exists
test('File security helper exists', function () {
    $helper_file = './application/helpers/file_security_helper.php';

    if ( ! file_exists($helper_file)) {
        echo "  ERROR: file_security_helper.php not found\n";

        return false;
    }

    return true;
});

test('File security helper has required functions', function () {
    $required_functions = [
        'validate_safe_filename',
        'validate_file_in_directory',
        'validate_file_access',
    ];

    foreach ($required_functions as $func) {
        if ( ! function_exists($func)) {
            echo "  ERROR: Function {$func} not found\n";

            return false;
        }
    }

    return true;
});

// Summary
echo "=================================================================\n";
if ($all_tests_passed) {
    echo "✓ ALL TESTS PASSED\n";
    echo "\nThe arbitrary file deletion vulnerability fix is properly implemented.\n";
    echo "InvoicePlane is protected against path traversal attacks in logo deletion.\n";
    exit(0);
}
echo "✗ SOME TESTS FAILED\n";
echo "\nWARNING: The fix may not be properly implemented.\n";
echo "Please review the failed tests and ensure you are running v1.7.2 or later.\n";
exit(1);
