<?php

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

$root = dirname(__DIR__);

/*
|--------------------------------------------------------------------------
| AUTOLOAD
|--------------------------------------------------------------------------
*/

require_once $root . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| DOTENV (same as index.php)
|--------------------------------------------------------------------------
*/

if (file_exists($root . '/ipconfig.php')) {
    $dotenv = Dotenv\Dotenv::createImmutable($root, 'ipconfig.php');
    $dotenv->load();
}

/*
|--------------------------------------------------------------------------
| ENV HELPERS (MUST EXIST BEFORE CI)
|--------------------------------------------------------------------------
*/

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? getenv($key) ?: $default;
    }
}

if (!function_exists('env_bool')) {
    function env_bool(string $key, bool $default = false): bool
    {
        return filter_var(env($key, $default), FILTER_VALIDATE_BOOLEAN);
    }
}

/*
|--------------------------------------------------------------------------
| ENVIRONMENT (MATCH index.php)
|--------------------------------------------------------------------------
*/

define('ENVIRONMENT', $_SERVER['CI_ENV'] ?? 'development');

$_SERVER['CI_ENV'] = ENVIRONMENT;

/*
|--------------------------------------------------------------------------
| INDEX.PHP CONSTANTS (CRITICAL MISSING PIECE)
|--------------------------------------------------------------------------
|
| This is EXACTLY what you were missing.
| CI loads config BEFORE index.php runs in tests → so these MUST be here.
|
*/

define('IP_DEBUG', env_bool('ENABLE_DEBUG'));
define('SUMEX_SETTINGS', env_bool('SUMEX_SETTINGS'));
define('SUMEX_URL', env('SUMEX_URL'));

/*
|--------------------------------------------------------------------------
| ERROR HANDLING
|--------------------------------------------------------------------------
*/

error_reporting(E_ALL);

if (ENVIRONMENT === 'testing') {
    ini_set('display_errors', '1');

    set_error_handler(static function (
        int $severity,
        string $message,
        string $file,
        int $line
    ): never {
        throw new ErrorException($message, 0, $severity, $file, $line);
    });

    register_shutdown_function(static function (): void {
        $error = error_get_last();

        if ($error !== null) {
            throw new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
        }
    });
} else {
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

/*
|--------------------------------------------------------------------------
| PATHS
|--------------------------------------------------------------------------
*/

$systemPath = $root . '/vendor/pocketarc/codeigniter/system';
$applicationFolder = $root . '/application';

/*
|--------------------------------------------------------------------------
| VALIDATION (fail fast, no CI surprises)
|--------------------------------------------------------------------------
*/

if (!is_dir($systemPath)) {
    throw new RuntimeException("System path invalid: {$systemPath}");
}

if (!is_dir($applicationFolder)) {
    throw new RuntimeException("Application path invalid: {$applicationFolder}");
}

/*
|--------------------------------------------------------------------------
| CI CONSTANTS
|--------------------------------------------------------------------------
*/

define('SELF', basename($_SERVER['SCRIPT_FILENAME'] ?? 'index.php'));
define('BASEPATH', rtrim($systemPath, '/') . '/');
define('FCPATH', rtrim($root, '/') . '/');
define('SYSDIR', basename(BASEPATH));

define('APPPATH', rtrim($applicationFolder, '/') . '/');
define('VIEWPATH', APPPATH . 'views/');

/*
|--------------------------------------------------------------------------
| CI COMPAT LAYER (prevents Common.php crashes)
|--------------------------------------------------------------------------
*/

if (!function_exists('log_message')) {
    function log_message(string $level, string $message): void {}
}

if (!function_exists('is_cli')) {
    function is_cli(): bool
    {
        return PHP_SAPI === 'cli';
    }
}

if (!function_exists('show_error')) {
    function show_error(string $message, int $status_code = 500): void
    {
        throw new RuntimeException($message, $status_code);
    }
}

/*
|--------------------------------------------------------------------------
| DIRECTORY SAFETY
|--------------------------------------------------------------------------
*/

$dirs = [
    $root . '/uploads/temp',
    $root . '/uploads/temp/test',
    $root . '/application/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

/*
|--------------------------------------------------------------------------
| OPTIONAL CLEANUP (TEST ONLY)
|--------------------------------------------------------------------------
*/

if (ENVIRONMENT === 'testing') {
    foreach (glob($root . '/uploads/temp/*.{pdf,xml}', GLOB_BRACE) ?: [] as $file) {
        @unlink($file);
    }
}

/*
|--------------------------------------------------------------------------
| BOOT CI
|--------------------------------------------------------------------------
*/

require_once BASEPATH . 'core/CodeIgniter.php';
