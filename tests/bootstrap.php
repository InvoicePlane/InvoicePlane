<?php

/**
 * PHPUnit bootstrap file for InvoicePlane
 * 
 * This file is loaded before running PHPUnit tests.
 * It sets up the CodeIgniter application bootstrap.
 */

// Define ROOT_PATH for use in tests
define('ROOT_PATH', __DIR__ . '/../');

// Define CI paths if not already defined
if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__ . '/../vendor/pocketarc/codeigniter/system' . DIRECTORY_SEPARATOR);
}
if (!defined('FCPATH')) {
    define('FCPATH', __DIR__ . '/../' . DIRECTORY_SEPARATOR);
}
if (!defined('APPPATH')) {
    define('APPPATH', __DIR__ . '/../application' . DIRECTORY_SEPARATOR);
}

// Load composer autoloader
require_once __DIR__ . '/../vendor/autoload.php';

// Load ipconfig.php settings if it exists
$ipconfig = __DIR__ . '/../ipconfig.php';
if (file_exists($ipconfig)) {
    require_once $ipconfig;
} else {
    // Try parent directory (for public/ index.php setup)
    $ipconfig = __DIR__ . '/../public/ipconfig.php';
    if (file_exists($ipconfig)) {
        require_once $ipconfig;
    }
}