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

defined('FCPATH') || define('FCPATH', $base . '/public/');
defined('APPPATH') || define('APPPATH', $base . '/application/');
defined('BASEPATH') || define('BASEPATH', $base . '/vendor/pocketarc/codeigniter/system/');
defined('VIEWPATH') || define('VIEWPATH', APPPATH . 'views/');

// Upload / filesystem path constants (previously defined in the legacy root index.php).
defined('IPCONFIG_FILE') || define('IPCONFIG_FILE', $base . '/ipconfig.php');
defined('LOGS_FOLDER') || define('LOGS_FOLDER', APPPATH . 'logs/');
defined('UPLOADS_FOLDER') || define('UPLOADS_FOLDER', FCPATH . 'uploads/');
defined('UPLOADS_ARCHIVE_FOLDER') || define('UPLOADS_ARCHIVE_FOLDER', UPLOADS_FOLDER . 'archive/');
defined('UPLOADS_CFILES_FOLDER') || define('UPLOADS_CFILES_FOLDER', UPLOADS_FOLDER . 'customer_files/');
defined('UPLOADS_TEMP_FOLDER') || define('UPLOADS_TEMP_FOLDER', UPLOADS_FOLDER . 'temp/');
defined('UPLOADS_TEMP_MPDF_FOLDER') || define('UPLOADS_TEMP_MPDF_FOLDER', UPLOADS_TEMP_FOLDER . 'mpdf/');

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
