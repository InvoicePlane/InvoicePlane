<?php

/**
 * PHPUnit Bootstrap File
 * 
 * This file is loaded before running tests to set up the testing environment.
 */

// Define constants that CodeIgniter expects
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/../vendor/pocketarc/codeigniter/system/');
}

if (!defined('APPPATH')) {
    define('APPPATH', __DIR__ . '/../application/');
}

if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', 'testing');
}

// Mock log_message function for tests
if (!function_exists('log_message')) {
    function log_message($level, $message) {
        // Silently ignore during tests
        return true;
    }
}

// Mock html_escape function for tests
if (!function_exists('html_escape')) {
    function html_escape($var, $double_encode = true) {
        if (is_array($var)) {
            return array_map(function($v) use ($double_encode) {
                return html_escape($v, $double_encode);
            }, $var);
        }
        return htmlspecialchars($var, ENT_QUOTES, 'UTF-8', $double_encode);
    }
}

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';
