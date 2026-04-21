<?php

if (defined('CI_BOOTSTRAPPED')) {
    return;
}

define('CI_BOOTSTRAPPED', true);

/*
|--------------------------------------------------------------------------
| COMPOSER
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| ENV LOAD (optional but early)
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__ . '/../ipconfig.php')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', 'ipconfig.php');
    $dotenv->safeLoad();
}

/*
|--------------------------------------------------------------------------
| HELPERS (NO CI DEPENDENCIES)
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/helpers.php';

/*
|--------------------------------------------------------------------------
| BASE PATHS
|--------------------------------------------------------------------------
*/
$base = dirname(__DIR__);

defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

defined('FCPATH')  || define('FCPATH', $base . '/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

/*
|--------------------------------------------------------------------------
| APPLICATION CONSTANTS (used by CI config early)
|--------------------------------------------------------------------------
*/
defined('IP_DEBUG') || define('IP_DEBUG', env_bool('ENABLE_DEBUG', false));

/*
|--------------------------------------------------------------------------
| STEP 1: LOAD CI CORE (MUST BE FIRST)
|--------------------------------------------------------------------------
| This ensures CI_Loader, CI_Controller, load_class(), etc exist.
|--------------------------------------------------------------------------
*/
require_once BASEPATH . 'core/Common.php';
require_once BASEPATH . 'core/Controller.php';
require_once BASEPATH . 'core/Loader.php';
require_once BASEPATH . 'core/CodeIgniter.php';

/*
|--------------------------------------------------------------------------
| STEP 2: MX EXTENSION (ONLY AFTER CI CORE EXISTS)
|--------------------------------------------------------------------------
*/
require_once APPPATH . 'third_party/MX/Modules.php';
require_once APPPATH . 'third_party/MX/Loader.php';
require_once APPPATH . 'third_party/MX/Controller.php';
require_once APPPATH . 'third_party/MX/Router.php';

/*
|--------------------------------------------------------------------------
| STEP 3: MX MODULE LOCATIONS
|--------------------------------------------------------------------------
*/
Modules::$locations = [
    APPPATH . 'modules/' => APPPATH . 'modules/',
];

/*
|--------------------------------------------------------------------------
| CLI SAFETY ROUTE (PHPUNIT)
|--------------------------------------------------------------------------
*/
if (PHP_SAPI === 'cli') {
    $_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/clients/index';
    $_SERVER['PATH_INFO']   = $_SERVER['PATH_INFO'] ?? '/clients/index';
    $_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'CLI';
}
