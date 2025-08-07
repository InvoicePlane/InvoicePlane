<?php

namespace App\Core;

use ErrorException;
use Throwable;

class ExceptionHandler
{
    public static function handle(Throwable $e): void
    {
        self::logException($e);
        self::renderException($e);
    }

    public static function handleError($severity, $message, $file, $line): void
    {
        $exception = new ErrorException($message, 0, $severity, $file, $line);
        self::handle($exception);
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();

        if ($error !== null) {
            $exception = new ErrorException(
                $error['message'],
                0,
                $error['type'],
                $error['file'],
                $error['line']
            );
            self::handle($exception);
        }
    }

    protected static function logException(Throwable $e): void
    {
        $date    = date('Y-m-d');
        $logFile = LOGS_FOLDER . "laravel-{$date}.log";

        $logEntry = '[' . date('Y-m-d H:i:s') . '] '
            . get_class($e) . ': ' . $e->getMessage()
            . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL
            . $e->getTraceAsString() . PHP_EOL . PHP_EOL;

        file_put_contents($logFile, $logEntry, FILE_APPEND);

        if (function_exists('log_message')) {
            log_message('error', $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        }
    }

    protected static function renderFallback(Throwable $e): void
    {
        // Fallback if show_error() is not available (early boot error)
        http_response_code(500);

        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            echo '<pre>' . htmlspecialchars((string) $e) . '</pre>';
        } else {
            echo 'An unexpected error occurred. Please check the logs.';
        }
    }

    /*private static function renderException(Throwable $e): void
    {
        // Optional: Render CI's show_error() for HTTP 500
        if (function_exists('show_error')) {
            http_response_code(500);
            show_error('An unexpected error occurred. Please try again later.', 500, 'Internal Server Error');
        } else {
            self::renderFallback($e);
        }
    }*/

    protected static function renderException(Throwable $e): void
    {
        http_response_code(500);

        if (defined('ENVIRONMENT') && ENVIRONMENT === 'development') {
            $data = [
                'severity' => method_exists($e, 'getSeverity') ? self::mapSeverity($e->getSeverity()) : 'Exception',
                'message'  => $e->getMessage(),
                'file'     => $e->getFile(),
                'line'     => $e->getLine(),
                'trace'    => $e->getTraceAsString(),
            ];

            extract($data);
            require VIEWPATH . 'errors/_error_box.php';
        } else {
            echo 'An unexpected error occurred. Please check the logs.';
        }
    }

    protected static function mapSeverity($severity): string
    {
        $levels = [
            E_ERROR             => 'Fatal Error',
            E_WARNING           => 'Warning',
            E_PARSE             => 'Parsing Error',
            E_NOTICE            => 'Notice',
            E_CORE_ERROR        => 'Core Error',
            E_CORE_WARNING      => 'Core Warning',
            E_COMPILE_ERROR     => 'Compile Error',
            E_COMPILE_WARNING   => 'Compile Warning',
            E_USER_ERROR        => 'User Error',
            E_USER_WARNING      => 'User Warning',
            E_USER_NOTICE       => 'User Notice',
            E_STRICT            => 'Runtime Notice',
            E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
            E_DEPRECATED        => 'Deprecated',
            E_USER_DEPRECATED   => 'User Deprecated',
        ];

        return $levels[$severity] ?? 'Error';
    }
}
