<?php

if (defined('CI_BOOTSTRAP_CONSTANTS')) {
    return;
}

define('CI_BOOTSTRAP_CONSTANTS', true);

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
*/
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
*/
defined('FILE_READ_MODE') || define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') || define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE') || define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE') || define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
*/
defined('FOPEN_READ') || define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE') || define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE') || define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb');
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') || define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b');
defined('FOPEN_WRITE_CREATE') || define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE') || define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT') || define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT') || define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
*/
defined('EXIT_SUCCESS') || define('EXIT_SUCCESS', 0);
defined('EXIT_ERROR') || define('EXIT_ERROR', 1);
defined('EXIT_CONFIG') || define('EXIT_CONFIG', 3);
defined('EXIT_UNKNOWN_FILE') || define('EXIT_UNKNOWN_FILE', 4);
defined('EXIT_UNKNOWN_CLASS') || define('EXIT_UNKNOWN_CLASS', 5);
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6);
defined('EXIT_USER_INPUT') || define('EXIT_USER_INPUT', 7);
defined('EXIT_DATABASE') || define('EXIT_DATABASE', 8);
defined('EXIT__AUTO_MIN') || define('EXIT__AUTO_MIN', 9);
defined('EXIT__AUTO_MAX') || define('EXIT__AUTO_MAX', 125);

// Optional path for admin-supplied invoice/quote/credit templates, read from
// the CUSTOM_TEMPLATES_FOLDER ipconfig key (mirrors the legacy root index.php).
// null disables the feature; a set value is normalised to a trailing slash.
if ( ! defined('CUSTOM_TEMPLATES_FOLDER')) {
    $__custom_tpl = env('CUSTOM_TEMPLATES_FOLDER');
    define('CUSTOM_TEMPLATES_FOLDER', $__custom_tpl ? rtrim($__custom_tpl, '/\\') . DIRECTORY_SEPARATOR : null);
    unset($__custom_tpl);
}

// Explicit allowlists of custom template names, consumed by Mdl_Templates.
// env() is defined in kernel.php before this file is required; an unset key
// yields null, which Mdl_Templates::_merge_custom() treats as "no custom templates".
defined('CUSTOM_INVOICE_TEMPLATES_PDF') || define('CUSTOM_INVOICE_TEMPLATES_PDF', env('CUSTOM_INVOICE_TEMPLATES_PDF'));
defined('CUSTOM_INVOICE_TEMPLATES_PUBLIC') || define('CUSTOM_INVOICE_TEMPLATES_PUBLIC', env('CUSTOM_INVOICE_TEMPLATES_PUBLIC'));
defined('CUSTOM_QUOTE_TEMPLATES_PDF') || define('CUSTOM_QUOTE_TEMPLATES_PDF', env('CUSTOM_QUOTE_TEMPLATES_PDF'));
defined('CUSTOM_QUOTE_TEMPLATES_PUBLIC') || define('CUSTOM_QUOTE_TEMPLATES_PUBLIC', env('CUSTOM_QUOTE_TEMPLATES_PUBLIC'));

// THEME_FOLDER and SUMEX_SETTINGS are defined in kernel.php — not here.
