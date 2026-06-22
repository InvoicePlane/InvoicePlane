<?php

defined('BASEPATH') || exit('No direct script access allowed');

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
defined('FILE_READ_MODE')  || define('FILE_READ_MODE',  0644);
defined('FILE_WRITE_MODE') || define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   || define('DIR_READ_MODE',   0755);
defined('DIR_WRITE_MODE')  || define('DIR_WRITE_MODE',  0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
*/
defined('FOPEN_READ')                          || define('FOPEN_READ',                          'rb');
defined('FOPEN_READ_WRITE')                    || define('FOPEN_READ_WRITE',                    'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')      || define('FOPEN_WRITE_CREATE_DESTRUCTIVE',      'wb');
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE') || define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b');
defined('FOPEN_WRITE_CREATE')                  || define('FOPEN_WRITE_CREATE',                  'ab');
defined('FOPEN_READ_WRITE_CREATE')             || define('FOPEN_READ_WRITE_CREATE',             'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')           || define('FOPEN_WRITE_CREATE_STRICT',           'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')      || define('FOPEN_READ_WRITE_CREATE_STRICT',      'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
*/
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS',        0);
defined('EXIT_ERROR')          || define('EXIT_ERROR',          1);
defined('EXIT_CONFIG')         || define('EXIT_CONFIG',         3);
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE',   4);
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS',  5);
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6);
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT',     7);
defined('EXIT_DATABASE')       || define('EXIT_DATABASE',       8);
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN',      9);
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX',      125);

// Optional path for admin-supplied invoice/quote/credit templates.
// Empty string disables the custom template folder feature.
defined('CUSTOM_TEMPLATES_FOLDER') || define('CUSTOM_TEMPLATES_FOLDER', '');
