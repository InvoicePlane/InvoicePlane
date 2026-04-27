<?php

$base = dirname(__DIR__);

require_once $base . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| ENV
|--------------------------------------------------------------------------
*/
if (file_exists($base . '/ipconfig.php')) {
    Dotenv\Dotenv::createImmutable($base, 'ipconfig.php')->safeLoad();
}

/*
|--------------------------------------------------------------------------
| CONSTANTS FIRST
|--------------------------------------------------------------------------
*/

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
| CORE CI FIRST
|--------------------------------------------------------------------------
*/

require_once BASEPATH . 'core/Common.php';
require_once BASEPATH . 'core/Controller.php';
require_once BASEPATH . 'core/Loader.php';
require_once BASEPATH . 'core/CodeIgniter.php';

/*
|--------------------------------------------------------------------------
| MX ONLY AFTER CI CORE EXISTS
|--------------------------------------------------------------------------
*/

require_once APPPATH . 'third_party/MX/Modules.php';
require_once APPPATH . 'third_party/MX/Loader.php';
require_once APPPATH . 'third_party/MX/Controller.php';
require_once APPPATH . 'third_party/MX/Router.php';

Modules::$locations = [
    APPPATH . 'modules/' => APPPATH . 'modules/',
];

/*
|--------------------------------------------------------------------------
| BOOT CI SINGLETON (IMPORTANT)
|--------------------------------------------------------------------------
*/

$CI = & get_instance();

return $CI;
