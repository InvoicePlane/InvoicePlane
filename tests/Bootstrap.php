<?php


/*
 * ---------------------------------------------------------------
 * Load the IP Bootstrap file and setup error reporting
 * ---------------------------------------------------------------
 */
require_once(__DIR__ . '/../bootstrap/ip.php');

ini_set('display_errors', 1);
error_reporting(E_ALL | E_STRICT);

/*
 * ---------------------------------------------------------------
 * Setup path constants
 * ---------------------------------------------------------------
 */
$dir = realpath(dirname(__FILE__));

defined('PROJECT_BASE') or define('PROJECT_BASE', realpath($dir . '/../') . '/');
defined('SYSTEM_PATH') or define('SYSTEM_PATH', PROJECT_BASE . 'vendor/codeigniter/framework/system/');
define('APPPATH', PROJECT_BASE . DIRECTORY_SEPARATOR . 'application' .  DIRECTORY_SEPARATOR);

define('IPPATH', PROJECT_BASE);

define('IP_ENV', env('APP_ENVIRONMENT', 'production'));
define('ENVIRONMENT', IP_ENV);

define('IP_DEBUG', env('ENABLE_DEBUG'));

define('IPCONFIG_FILE', IPPATH . 'ipconfig');

define('LOGS_FOLDER', APPPATH . 'logs' . DIRECTORY_SEPARATOR);

define('UPLOADS_FOLDER', IPPATH . 'uploads' . DIRECTORY_SEPARATOR);
define('UPLOADS_ARCHIVE_FOLDER', UPLOADS_FOLDER . 'archive' . DIRECTORY_SEPARATOR);
define('UPLOADS_CFILES_FOLDER', UPLOADS_FOLDER . 'customer_files' . DIRECTORY_SEPARATOR);
define('UPLOADS_TEMP_FOLDER', UPLOADS_FOLDER . 'temp' . DIRECTORY_SEPARATOR);
define('UPLOADS_TEMP_MPDF_FOLDER', UPLOADS_TEMP_FOLDER . 'mpdf' . DIRECTORY_SEPARATOR);

/*
 * ---------------------------------------------------------------
 * Setup vfsStream
 * ---------------------------------------------------------------
 */
class_alias('org\bovigo\vfs\vfsStream', 'vfsStream');
class_alias('org\bovigo\vfs\vfsStreamDirectory', 'vfsStreamDirectory');
class_alias('org\bovigo\vfs\vfsStreamWrapper', 'vfsStreamWrapper');


/*
 * ---------------------------------------------------------------
 * Define CI path constants to VFS (filesystem setup in CI_TestCase::setUp)
 * ---------------------------------------------------------------
 */
defined('BASEPATH') or define('BASEPATH', vfsStream::url('system/'));
defined('APPPATH') or define('APPPATH', vfsStream::url('application/'));
defined('VIEWPATH') or define('VIEWPATH', APPPATH . 'views/');
defined('ENVIRONMENT') or define('ENVIRONMENT', 'development');

/*
 * ---------------------------------------------------------------
 * Prep our test environment
 * ---------------------------------------------------------------
 */

// Set localhost "remote" IP
isset($_SERVER['REMOTE_ADDR']) or $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

// Prep our test environment
include_once $dir . '/mocks/core/common.php';
include_once SYSTEM_PATH . 'core/Common.php';

ini_set('default_charset', 'UTF-8');

if (extension_loaded('mbstring')) {
    defined('MB_ENABLED') or define('MB_ENABLED', TRUE);
    @ini_set('mbstring.internal_encoding', 'UTF-8');
    mb_substitute_character('none');
} else {
    defined('MB_ENABLED') or define('MB_ENABLED', FALSE);
}

if (extension_loaded('iconv')) {
    defined('ICONV_ENABLED') or define('ICONV_ENABLED', TRUE);
    @ini_set('iconv.internal_encoding', 'UTF-8');
} else {
    defined('ICONV_ENABLED') or define('ICONV_ENABLED', FALSE);
}

is_php('5.6') && ini_set('php.internal_encoding', 'UTF-8');

include_once SYSTEM_PATH . 'core/compat/mbstring.php';
include_once SYSTEM_PATH . 'core/compat/hash.php';
include_once SYSTEM_PATH . 'core/compat/password.php';
include_once SYSTEM_PATH . 'core/compat/standard.php';

include_once $dir . '/mocks/autoloader.php';
spl_autoload_register('autoload');

unset($dir);
