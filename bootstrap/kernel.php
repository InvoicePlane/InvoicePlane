<?php

if (defined('CI_KERNEL_BOOTED')) {
    return;
}

define('CI_KERNEL_BOOTED', true);

$base = dirname(__DIR__);

require_once $base . '/vendor/autoload.php';

if (file_exists($base . '/ipconfig.php')) {
    $dotenv = Dotenv\Dotenv::createImmutable($base, 'ipconfig.php');
    $dotenv->safeLoad();
}

defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

defined('FCPATH') || define('FCPATH', $base . '/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

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
