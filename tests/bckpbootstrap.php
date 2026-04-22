<?php

/**
 * PHPUnit Bootstrap File.
 *
 * Initializes the testing environment for InvoicePlane
 * Sets up the Illuminate container and loads necessary components
 */

// Load Composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load application bootstrap for Illuminate components
require_once __DIR__ . '/../bootstrap/app.php';

// Set testing environment
define('ENVIRONMENT', 'testing');

// Ensure upload directories exist for tests
$testUploadDir = __DIR__ . '/../uploads/temp/test';
if ( ! is_dir($testUploadDir)) {
    mkdir($testUploadDir, 0777, true);
}
