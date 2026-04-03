<?php
/**
 * Test script to verify XSS fixes work correctly
 * 
 * This script tests the HTML sanitization function to ensure:
 * 1. Malicious JavaScript is removed
 * 2. Safe HTML tags are preserved
 * 3. Event handlers are stripped
 */

// Define BASEPATH for CodeIgniter
define('BASEPATH', true);
define('FCPATH', __DIR__ . '/');
define('APPPATH', __DIR__ . '/application/');

// Load the helper
require_once APPPATH . 'helpers/html_sanitizer_helper.php';

// Test cases
$test_cases = [
    [
        'name' => 'XSS with script tag',
        'input' => '<p>Hello</p><script>alert(document.cookie)</script>',
        'should_contain' => '<p>Hello</p>',
        'should_not_contain' => '<script>',
    ],
    [
        'name' => 'XSS with onerror event',
        'input' => '<img src=x onerror=alert(document.cookie)>',
        'should_contain' => '<img',
        'should_not_contain' => 'onerror',
    ],
    [
        'name' => 'XSS with textarea breakout',
        'input' => '</textarea><script>alert(document.cookie)</script><textarea>',
        'should_contain' => 'textarea',
        'should_not_contain' => '<script>',
    ],
    [
        'name' => 'Safe HTML content',
        'input' => '<p><strong>Invoice</strong> #<span style="color: blue;">12345</span></p>',
        'should_contain' => '<p><strong>Invoice</strong>',
        'should_not_contain' => null,
    ],
    [
        'name' => 'XSS with javascript: protocol',
        'input' => '<a href="javascript:alert(1)">Click me</a>',
        'should_contain' => '<a',
        'should_not_contain' => 'javascript:',
    ],
];

echo "Testing XSS fixes for email templates\n";
echo str_repeat('=', 70) . "\n\n";

$passed = 0;
$failed = 0;

foreach ($test_cases as $test) {
    echo "Test: {$test['name']}\n";
    echo "Input: " . htmlspecialchars($test['input']) . "\n";
    
    try {
        $output = sanitize_email_template_html($test['input']);
        echo "Output: " . htmlspecialchars($output) . "\n";
        
        $pass = true;
        
        // Use str_contains() if available (PHP 8+), otherwise fall back to strpos()
        $has_should_contain = function_exists('str_contains') 
            ? str_contains($output, $test['should_contain'])
            : ($test['should_contain'] !== null && strpos($output, $test['should_contain']) !== false);
            
        $has_should_not_contain = function_exists('str_contains')
            ? str_contains($output, $test['should_not_contain'])
            : ($test['should_not_contain'] !== null && strpos($output, $test['should_not_contain']) !== false);
        
        if ($test['should_contain'] !== null && !$has_should_contain) {
            echo "❌ FAILED: Output should contain '{$test['should_contain']}'\n";
            $pass = false;
        }
        
        if ($test['should_not_contain'] !== null && $has_should_not_contain) {
            echo "❌ FAILED: Output should NOT contain '{$test['should_not_contain']}'\n";
            $pass = false;
        }
        
        if ($pass) {
            echo "✅ PASSED\n";
            $passed++;
        } else {
            $failed++;
        }
    } catch (Exception $e) {
        echo "❌ FAILED: Exception: {$e->getMessage()}\n";
        $failed++;
    }
    
    echo "\n";
}

echo str_repeat('=', 70) . "\n";
echo "Results: {$passed} passed, {$failed} failed\n";

exit($failed > 0 ? 1 : 0);
