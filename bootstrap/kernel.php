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

// Application environment + error reporting. Ported from the legacy root
// index.php (now removed) so the public/ front controller is the single boot
// path. CI_ENV is supplied by the web server / CLI; default to 'production'
// (errors hidden) so a misconfigured deploy fails safe rather than leaking.
if ( ! defined('ENVIRONMENT')) {
    define('ENVIRONMENT', $_SERVER['CI_ENV'] ?? 'production');
}

switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', '1');
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', '0');
        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;

    default:
        // Unknown environment: fail loud for web requests, keep going for CLI/tests.
        if (PHP_SAPI !== 'cli') {
            header('HTTP/1.1 503 Service Unavailable.', true, 503);
            echo 'The application environment is not set correctly.';
            exit(1);
        }
}

defined('FCPATH') || define('FCPATH', $base . '/public/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

// Legacy CI3 path constants. Some third-party/CI internals still reference
// these; define them to match the stock front controller so nothing that
// reads them breaks now that the root index.php is gone.
defined('SELF') || define('SELF', basename($_SERVER['SCRIPT_NAME'] ?? 'index.php'));
defined('SYSDIR') || define('SYSDIR', basename(rtrim(BASEPATH, '/\\')));

// Upload / filesystem path constants (previously defined in the legacy root index.php).
// uploads/ lives at the repo root (like resources/assets/ below), not under
// public/ — do not change this to FCPATH-relative, it would resolve to a
// public/uploads/ directory that doesn't exist.
defined('IPCONFIG_FILE') || define('IPCONFIG_FILE', $base . '/ipconfig.php');
defined('LOGS_FOLDER') || define('LOGS_FOLDER', APPPATH . 'logs/');
defined('UPLOADS_FOLDER') || define('UPLOADS_FOLDER', $base . '/uploads/');
defined('UPLOADS_ARCHIVE_FOLDER') || define('UPLOADS_ARCHIVE_FOLDER', UPLOADS_FOLDER . 'archive/');
defined('UPLOADS_CFILES_FOLDER') || define('UPLOADS_CFILES_FOLDER', UPLOADS_FOLDER . 'customer_files/');
defined('UPLOADS_TEMP_FOLDER') || define('UPLOADS_TEMP_FOLDER', UPLOADS_FOLDER . 'temp/');
defined('UPLOADS_TEMP_MPDF_FOLDER') || define('UPLOADS_TEMP_MPDF_FOLDER', UPLOADS_TEMP_FOLDER);
// storage/ also lives at the repo root, not under public/ — same reasoning as uploads/ above.
defined('STORAGE_TEMP_FOLDER') || define('STORAGE_TEMP_FOLDER', $base . '/storage/temp/');

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

defined('IP_DEBUG') || define('IP_DEBUG', env_bool('ENABLE_DEBUG', false));

require_once __DIR__ . '/constants.php';

// THEME_FOLDER points to the compiled/source asset tree under resources/assets/.
// Subdirectories there (core/, invoiceplane/, invoiceplane_blue/, …) are the
// available themes as listed by Settings. Do not change this to views/themes/.
defined('THEME_FOLDER') || define('THEME_FOLDER', $base . '/resources/assets/');

defined('SUMEX_SETTINGS') || define('SUMEX_SETTINGS', env_bool('SUMEX_SETTINGS', false));

// Endpoint the Sumex library POSTs XML to in order to get a PDF back
// (application/libraries/Sumex.php). Ported from the legacy root index.php.
defined('SUMEX_URL') || define('SUMEX_URL', env('SUMEX_URL'));

// ---------------------------------------------------------------------------
// PHP error / exception handlers
//
// In CI_TEST_SUBPROCESS: throw so the error surfaces as a real PHPUnit failure.
// With EXTENSIVE_LOGGING (or IP_DEBUG): log the error then continue normally.
// Default: PHP's built-in handler (CI3 registers its own later for web requests).
// ---------------------------------------------------------------------------
if (defined('CI_TEST_SUBPROCESS')) {
    set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
        if ( ! ($severity & error_reporting())) {
            return false;
        }
        throw new \ErrorException($message, 0, $severity, $file, $line);
    });

    set_exception_handler(static function (\Throwable $e): void {
        fwrite(STDERR, $e::class . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL);
        exit(1);
    });
} elseif (env_bool('EXTENSIVE_LOGGING', false) || IP_DEBUG) {
    set_error_handler(static function (int $severity, string $message, string $file, int $line): bool {
        if ( ! ($severity & error_reporting())) {
            return false;
        }
        $levels = [
            E_ERROR             => 'ERROR',
            E_WARNING           => 'WARNING',
            E_NOTICE            => 'NOTICE',
            E_DEPRECATED        => 'DEPRECATED',
            E_USER_ERROR        => 'ERROR',
            E_USER_WARNING      => 'WARNING',
            E_USER_NOTICE       => 'NOTICE',
            E_USER_DEPRECATED   => 'DEPRECATED',
            E_STRICT            => 'STRICT',
            E_RECOVERABLE_ERROR => 'ERROR',
        ];
        $level   = $levels[$severity] ?? 'ERROR';
        $logDir  = defined('FCPATH') ? dirname(FCPATH) . '/storage/logs/' : sys_get_temp_dir() . '/';
        $logFile = $logDir . 'log-' . date('Y-m-d') . '.php';
        $entry   = date('Y-m-d H:i:s') . ' ' . $level . ' -- ' . $message . ' (' . $file . ':' . $line . ')' . PHP_EOL;
        if ( ! file_exists($logFile)) {
            file_put_contents($logFile, "<?php defined('BASEPATH') || exit; ?>" . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
        file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

        // Return false so PHP's default handler also runs (shows error in output when display_errors=on).
        return false;
    });
}

// ---------------------------------------------------------------------------
// Writable temp dirs (ported from the legacy root index.php).
// Ensure storage/temp exists, then sweep stale generated PDF/XML temp files on
// every boot so they don't accumulate. Runs on the real web/CLI path only —
// the unit-test harness requires constants.php directly and never loads this.
// ---------------------------------------------------------------------------
if ( ! is_dir(STORAGE_TEMP_FOLDER)) {
    @mkdir(STORAGE_TEMP_FOLDER, 0755, true);
}

$__ip_temp_files = array_merge(
    glob(UPLOADS_TEMP_FOLDER . '*.pdf') ?: [],
    glob(UPLOADS_TEMP_FOLDER . '*.xml') ?: [],
    glob(STORAGE_TEMP_FOLDER . '*.xml') ?: []
);

array_map('unlink', array_filter($__ip_temp_files, 'is_file'));

unset($__ip_temp_files);
