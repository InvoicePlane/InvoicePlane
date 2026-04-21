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
| ENV FIRST
|--------------------------------------------------------------------------
*/
if (file_exists(__DIR__ . '/../ipconfig.php')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', 'ipconfig.php');
    $dotenv->safeLoad();
}

/*
|--------------------------------------------------------------------------
| SAFE HELPERS
|--------------------------------------------------------------------------
*/
require_once __DIR__ . '/helpers.php';

/*
|--------------------------------------------------------------------------
| BASE PATH
|--------------------------------------------------------------------------
*/
$base = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| CRITICAL CI CONSTANTS (MUST EXIST BEFORE CORE LOAD)
|--------------------------------------------------------------------------
*/
defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

defined('FCPATH') || define('FCPATH', $base . '/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

/*
|--------------------------------------------------------------------------
| APPLICATION CONSTANTS USED BY CONFIG
|--------------------------------------------------------------------------
| This is the missing piece causing your crash
|--------------------------------------------------------------------------
*/
defined('IP_DEBUG') || define('IP_DEBUG', env_bool('ENABLE_DEBUG', false));

if (PHP_SAPI === 'cli') {
    $_SERVER['REQUEST_URI'] = '/clients/index';
}

/*
|--------------------------------------------------------------------------
| CI CORE
|--------------------------------------------------------------------------
*/
require_once BASEPATH . 'core/Common.php';
require_once BASEPATH . 'core/CodeIgniter.php';
