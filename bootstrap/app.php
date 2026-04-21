<?php

if (defined('CI_APP_BOOTED')) {
    return $GLOBALS['CI'] ?? null;
}

define('CI_APP_BOOTED', true);

require_once __DIR__ . '/../vendor/autoload.php';

if (file_exists(__DIR__ . '/../ipconfig.php')) {
    Dotenv\Dotenv::createImmutable(__DIR__ . '/..', 'ipconfig.php')->safeLoad();
}

require_once __DIR__ . '/helpers.php';

$base = dirname(__DIR__);

defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

defined('FCPATH') || define('FCPATH', $base . '/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

defined('IP_DEBUG') || define(
    'IP_DEBUG',
    filter_var($_ENV['ENABLE_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN)
);

/*
|--------------------------------------------------------------------------
| CORE LOAD ONLY (SAFE)
|--------------------------------------------------------------------------
*/
require_once BASEPATH . 'core/Common.php';
require_once BASEPATH . 'core/Controller.php';
require_once BASEPATH . 'core/Loader.php';

/*
|--------------------------------------------------------------------------
| TEST MODE: DO NOT TOUCH load_class('Controller')
|--------------------------------------------------------------------------
*/
if (PHP_SAPI === 'cli' && defined('PHPUNIT_RUNNING') && PHPUNIT_RUNNING === true) {
    // IMPORTANT: CI will auto-assign CI::$APP internally when CodeIgniter is not run
    $CI = new stdClass();
    $GLOBALS['CI'] = $CI;

    return $CI;
}

/*
|--------------------------------------------------------------------------
| FULL WEB BOOT ONLY
|--------------------------------------------------------------------------
*/
require_once BASEPATH . 'core/CodeIgniter.php';

return $GLOBALS['CI'] ?? null;
