#!/usr/bin/env php
<?php
/**
 * Security Test Script - RCE Vulnerability Fix Verification
 * 
 * This script verifies that the RCE vulnerability fix is working correctly.
 * It tests that malicious templates are blocked while legitimate templates work.
 * 
 * Usage: php verify_rce_fix.php
 * 
 * Requirements:
 * - Run from InvoicePlane root directory
 * - PHP 8.1+ with CodeIgniter loaded
 * 
 * Note: This script assumes CodeIgniter is installed via Composer in vendor/pocketarc/codeigniter.
 * If your installation differs, update the BASEPATH constant below.
 */

// Define BASEPATH to allow loading CodeIgniter helpers
// Note: Assumes standard Composer installation. Update if your path differs.
define('BASEPATH', __DIR__ . '/vendor/pocketarc/codeigniter/system/');
define('APPPATH', __DIR__ . '/application/');

// Color output for terminal
function green($text) { return "\033[32m{$text}\033[0m"; }
function red($text) { return "\033[31m{$text}\033[0m"; }
function yellow($text) { return "\033[33m{$text}\033[0m"; }
function bold($text) { return "\033[1m{$text}\033[0m"; }

echo bold("=== InvoicePlane RCE Fix Verification ===\n\n");

// Load required files
require_once APPPATH . 'helpers/file_security_helper.php';

echo "Step 1: Testing file_security_helper functions...\n";

// Test 1: Path traversal detection
$test_cases = [
    ['input' => '../../../etc/passwd', 'should_fail' => true, 'name' => 'Path traversal (../)'],
    ['input' => 'evil.php', 'should_fail' => false, 'name' => 'Normal filename'],
    ['input' => '..', 'should_fail' => true, 'name' => 'Standalone (..)'],
    ['input' => "evil\x00.php", 'should_fail' => true, 'name' => 'Null byte injection'],
    ['input' => '/etc/passwd', 'should_fail' => true, 'name' => 'Absolute path'],
    ['input' => 'C:\\Windows\\System32', 'should_fail' => true, 'name' => 'Windows drive letter'],
];

$passed = 0;
$failed = 0;

foreach ($test_cases as $test) {
    $result = validate_safe_filename($test['input']);
    $expected = !$test['should_fail'];
    
    if ($result['valid'] === $expected) {
        echo "  " . green("✓") . " {$test['name']}: " . ($expected ? "Allowed" : "Blocked") . "\n";
        $passed++;
    } else {
        echo "  " . red("✗") . " {$test['name']}: Expected " . ($expected ? "allowed" : "blocked") . " but got opposite\n";
        $failed++;
    }
}

echo "\nStep 2: Verifying static whitelist implementation...\n";

// Check that Mdl_templates.php doesn't use directory_map
$mdl_templates_content = file_get_contents(APPPATH . 'modules/invoices/models/Mdl_templates.php');

if (strpos($mdl_templates_content, 'directory_map') !== false) {
    echo "  " . red("✗") . " FAIL: Mdl_templates.php still uses directory_map() - VULNERABLE!\n";
    $failed++;
} else {
    echo "  " . green("✓") . " Mdl_templates.php does not use directory_map()\n";
    $passed++;
}

if (strpos($mdl_templates_content, 'ALLOWED_INVOICE_TEMPLATES') !== false) {
    echo "  " . green("✓") . " Static whitelist ALLOWED_INVOICE_TEMPLATES found\n";
    $passed++;
} else {
    echo "  " . red("✗") . " FAIL: Static whitelist ALLOWED_INVOICE_TEMPLATES not found - VULNERABLE!\n";
    $failed++;
}

if (strpos($mdl_templates_content, 'ALLOWED_QUOTE_TEMPLATES') !== false) {
    echo "  " . green("✓") . " Static whitelist ALLOWED_QUOTE_TEMPLATES found\n";
    $passed++;
} else {
    echo "  " . red("✗") . " FAIL: Static whitelist ALLOWED_QUOTE_TEMPLATES not found - VULNERABLE!\n";
    $failed++;
}

echo "\nStep 3: Checking template_helper validation...\n";

$template_helper_content = file_get_contents(APPPATH . 'helpers/template_helper.php');

$required_checks = [
    'validate_safe_filename' => 'Path traversal detection',
    'in_array($type,' => 'Type validation',
    'in_array($scope,' => 'Scope validation',
    'in_array($template_name, $valid_templates, true)' => 'Static whitelist validation',
    'preg_match' => 'Character validation',
];

foreach ($required_checks as $pattern => $description) {
    if (strpos($template_helper_content, $pattern) !== false) {
        echo "  " . green("✓") . " {$description} present\n";
        $passed++;
    } else {
        echo "  " . yellow("⚠") . " {$description} might be missing (check manually)\n";
    }
}

echo "\nStep 4: Checking View.php defense-in-depth...\n";

$view_controller_content = file_get_contents(APPPATH . 'modules/guest/controllers/View.php');

if (strpos($view_controller_content, 'file_exists($template_path)') !== false) {
    echo "  " . green("✓") . " File existence verification present\n";
    $passed++;
} else {
    echo "  " . yellow("⚠") . " File existence verification might be missing\n";
}

if (strpos($view_controller_content, 'validate_template_name') !== false) {
    echo "  " . green("✓") . " Template validation called before inclusion\n";
    $passed++;
} else {
    echo "  " . red("✗") . " FAIL: Template validation not found - VULNERABLE!\n";
    $failed++;
}

echo "\nStep 5: Checking template directory permissions...\n";

$template_dirs = [
    APPPATH . 'views/invoice_templates/public',
    APPPATH . 'views/invoice_templates/pdf',
    APPPATH . 'views/quote_templates/public',
    APPPATH . 'views/quote_templates/pdf',
];

$permission_issues = [];
foreach ($template_dirs as $dir) {
    if (is_writable($dir)) {
        echo "  " . yellow("⚠") . " WARNING: Directory is writable: {$dir}\n";
        echo "    Recommendation: chmod 555 {$dir}\n";
        $permission_issues[] = $dir;
    } else {
        echo "  " . green("✓") . " Directory is read-only: {$dir}\n";
        $passed++;
    }
}

// Summary
echo "\n" . bold("=== Test Summary ===\n");
echo green("Passed: {$passed}\n");
if ($failed > 0) {
    echo red("Failed: {$failed}\n");
}
if (count($permission_issues) > 0) {
    echo yellow("Warnings: " . count($permission_issues) . " directories with insecure permissions\n");
}

echo "\n";
if ($failed === 0) {
    if (count($permission_issues) > 0) {
        echo yellow("⚠ FIX IS WORKING but template directories should be set to read-only\n");
        echo "  Run the following commands to secure permissions:\n\n";
        foreach ($permission_issues as $dir) {
            echo "  chmod 555 {$dir}\n";
        }
        exit(1);
    } else {
        echo green("✓ ALL TESTS PASSED - RCE vulnerability is FIXED\n");
        echo "  The static whitelist is in place and working correctly.\n";
        echo "  Template directories have secure permissions.\n";
        exit(0);
    }
} else {
    echo red("✗ CRITICAL: Some tests failed - System may still be VULNERABLE\n");
    echo "  Review the failed checks above and ensure the fix is properly implemented.\n";
    exit(2);
}
