<?php

if (!function_exists('env_bool')) {
    function env_bool(string $key, bool $default = false): bool
    {
        $value = getenv($key);

        if ($value === false) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

$root = dirname(__DIR__);

define('ENVIRONMENT', getenv('CI_ENV') ?: 'production');

$_SERVER['CI_ENV'] = ENVIRONMENT;

/*
|--------------------------------------------------------------------------
| Core Paths
|--------------------------------------------------------------------------
*/

define('ROOTPATH', $root . '/');
define('APPPATH', $root . '/application/');
$systemPath = $root . '/vendor/pocketarc/codeigniter/system';

define('BASEPATH', $systemPath . '/');

/*
|--------------------------------------------------------------------------
| Composer Autoload
|--------------------------------------------------------------------------
*/

require_once $systemPath . '/core/CodeIgniter.php';

/*
|--------------------------------------------------------------------------
| Error Handling (safe for CI + PHPUnit + production)
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
| Ensure required directories
|--------------------------------------------------------------------------
*/

$dirs = [
    $root . '/uploads/temp/test',
    $root . '/storage/logs',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

/*
|--------------------------------------------------------------------------
| Boot CodeIgniter
|--------------------------------------------------------------------------
*/

require_once $systemPath . '/core/CodeIgniter.php';
