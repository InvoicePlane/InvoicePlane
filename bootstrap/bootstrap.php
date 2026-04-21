<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

define('IS_TESTING', true);

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..', 'ipconfig.php');
$dotenv->safeLoad();

define('VIEWPATH', APPPATH . 'views/');

/*
|--------------------------------------------------------------------------
| ENV HELPERS ONLY
|--------------------------------------------------------------------------
*/

if ( ! function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if ( ! function_exists('env_bool')) {
    function env_bool(string $key, bool $default = false): bool
    {
        $value = $_ENV[$key] ?? null;

        return $value === null
            ? $default
            : filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

/*
|--------------------------------------------------------------------------
| CONSTANTS
|--------------------------------------------------------------------------
*/

defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

defined('IP_DEBUG') || define('IP_DEBUG', env_bool('ENABLE_DEBUG', false));

$base = dirname(__DIR__);

defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('FCPATH') || define('FCPATH', $base . '/');

/*
|--------------------------------------------------------------------------
| CRITICAL FIX: DO NOT LOAD MX BEFORE CI CORE
|--------------------------------------------------------------------------
*/

spl_autoload_register(static function ($class) {
    // block MX autoload BEFORE CI is ready
    if (str_starts_with($class, 'MX_')) {
        return;
    }
});

/*
|--------------------------------------------------------------------------
| LOAD CI CORE FIRST (THIS INITIALIZES CI_Controller)
|--------------------------------------------------------------------------
*/

require_once BASEPATH . 'core/CodeIgniter.php';

/*
|--------------------------------------------------------------------------
| ONLY AFTER THIS POINT MX IS SAFE
|--------------------------------------------------------------------------
*/

require_once APPPATH . 'third_party/MX/Loader.php';
require_once APPPATH . 'third_party/MX/Modules.php';
require_once APPPATH . 'third_party/MX/Router.php';
