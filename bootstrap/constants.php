<?php

$base = dirname(__DIR__);

defined('ENVIRONMENT') || define('ENVIRONMENT', 'testing');

defined('FCPATH')   || define('FCPATH', $base . '/');
defined('APPPATH')  || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

if (!function_exists('env')) {
    $_ENV = $_ENV ?? [];
}

defined('IP_DEBUG') || define(
    'IP_DEBUG',
    filter_var($_ENV['ENABLE_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN)
);
