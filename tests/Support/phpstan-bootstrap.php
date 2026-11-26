<?php

/**
 * PHPStan bootstrap for InvoicePlane (CodeIgniter 3 / HMVC).
 *
 * CI3 has no PSR-4 autoloading or classmap: controllers, models and helpers
 * are pulled in at runtime by the framework. Without this file PHPStan reports
 * thousands of "function not found" errors for the application helpers.
 *
 * We define the framework path constants (so the `defined('BASEPATH')` guards
 * at the top of each helper pass) and then require every application helper so
 * their function signatures are known to static analysis. Helpers only declare
 * functions at include time, so requiring them here has no side effects.
 */
defined('BASEPATH') || define('BASEPATH', __DIR__);
defined('APPPATH') || define('APPPATH', dirname(__DIR__, 2) . '/application/');
defined('FCPATH') || define('FCPATH', dirname(__DIR__, 2) . '/');
defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

foreach (glob(APPPATH . 'helpers/*_helper.php') ?: [] as $helper) {
    require_once $helper;
}

// CI3 *system* functions (site_url, redirect, log_message, …) are declared by
// the framework at runtime. Declaring their signatures here lets static analysis
// resolve calls to them without executing framework code.
require_once __DIR__ . '/phpstan-ci-stubs.php';
