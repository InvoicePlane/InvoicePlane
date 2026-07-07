<?php

/**
 * PHPUnit Bootstrap File
 *
 * Sets up the environment for running unit tests.
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define base path
define('BASEPATH', dirname(__DIR__) . '/application/');
define('APPPATH', dirname(__DIR__) . '/application/');
define('DOCROOT', dirname(__DIR__) . '/');

// Load composer autoloader if available
$composer_autoload = dirname(__DIR__) . '/vendor/autoload.php';
if (file_exists($composer_autoload)) {
    require_once $composer_autoload;
}

// Set timezone to avoid warnings
date_default_timezone_set('UTC');
