<?php

if (defined('CI_KERNEL_BOOTED')) {
    return;
}

define('CI_KERNEL_BOOTED', true);

$base = dirname(__DIR__);

require_once $base . '/vendor/autoload.php';

if (file_exists($base . '/ipconfig.php')) {
    Dotenv\Dotenv::createImmutable($base, 'ipconfig.php')->safeLoad();
}

/*
|--------------------------------------------------------------------------
| CONSTANTS FIRST
|--------------------------------------------------------------------------
*/

defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

$basePath = $base;

defined('FCPATH') || define('FCPATH', $basePath . '/public/');
defined('APPPATH') || define('APPPATH', $basePath . '/application/');
defined('BASEPATH') || define('BASEPATH', $basePath . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

defined('IP_DEBUG') || define(
    'IP_DEBUG',
    filter_var($_ENV['ENABLE_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN)
);

/*
|--------------------------------------------------------------------------
| HELPERS (NO CI DEPENDENCY)
|--------------------------------------------------------------------------
*/

require_once $basePath . '/bootstrap/helpers.php';

/*
|--------------------------------------------------------------------------
| STEP 1 - CI CORE (MUST BE FIRST CI LOAD)
|--------------------------------------------------------------------------
*/

require_once BASEPATH . 'core/Common.php';
require_once BASEPATH . 'core/Controller.php';
require_once BASEPATH . 'core/Loader.php';
require_once BASEPATH . 'core/CodeIgniter.php';

/*
|--------------------------------------------------------------------------
| STEP 2 - NOW MX IS SAFE
|--------------------------------------------------------------------------
*/

require_once APPPATH . 'third_party/MX/Modules.php';
require_once APPPATH . 'third_party/MX/Loader.php';
require_once APPPATH . 'third_party/MX/Controller.php';
require_once APPPATH . 'third_party/MX/Router.php';

/*
|--------------------------------------------------------------------------
| STEP 3 - MODULE LOCATIONS
|--------------------------------------------------------------------------
*/

Modules::$locations = [
    APPPATH . 'modules/' => APPPATH . 'modules/',
];

/*
|--------------------------------------------------------------------------
| STEP 4 - CLI SAFETY
|--------------------------------------------------------------------------
*/

if (PHP_SAPI === 'cli') {
    $_SERVER['REQUEST_URI'] ??= '/clients/index';
    $_SERVER['PATH_INFO'] ??= '/clients/index';
    $_SERVER['REQUEST_METHOD'] ??= 'CLI';
}
