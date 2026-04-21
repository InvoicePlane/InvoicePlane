<?php

if (defined('CI_BOOTSTRAPPED')) {
    return;
}

define('CI_BOOTSTRAPPED', true);

require_once __DIR__ . '/../vendor/autoload.php';

$base = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| CONSTANTS FIRST (NO DEPENDENCIES)
|--------------------------------------------------------------------------
*/

defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');
defined('FCPATH') || define('FCPATH', $base . '/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

/*
|--------------------------------------------------------------------------
| ENV DEPENDENT CONSTANTS (MUST BE BEFORE CI LOAD)
|--------------------------------------------------------------------------
*/

if (!defined('IP_DEBUG')) {
    $env = $_ENV['ENABLE_DEBUG'] ?? false;

    define('IP_DEBUG', filter_var($env, FILTER_VALIDATE_BOOLEAN));
}

/*
|--------------------------------------------------------------------------
| HELPERS (AFTER CONSTANTS, BEFORE CI)
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/helpers.php';

/*
|--------------------------------------------------------------------------
| CI CORE LAST
|--------------------------------------------------------------------------
*/

require_once BASEPATH . 'core/CodeIgniter.php';
