<?php

require_once __DIR__ . '/../bootstrap/kernel.php';

if ( ! defined('STDIN')) {
    define('STDIN', fopen('php://stdin', 'r'));
}

if ( ! function_exists('get_instance')) {
    function &get_instance()
    {
        return CI::$APP;
    }
}
