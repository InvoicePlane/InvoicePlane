<?php

/**
 * Front-controller router for PHP's built-in web server, used by
 * playwright.config.js's `webServer` to serve InvoicePlane during E2E runs:
 *
 *   php -S 127.0.0.1:8000 -t . tests/E2E/router.php
 *
 * `php -S` serves files that exist on disk itself and only falls through to
 * this router for everything else. Real asset requests (assets/, uploads/,
 * favicon, ...) are returned as-is; every other path is dispatched through
 * public/index.php with a normalised REQUEST_URI so that both `/sessions/login`
 * and `/index.php/sessions/login` resolve regardless of the REMOVE_INDEXPHP
 * setting.
 */

$root = dirname(__DIR__, 2);
$uri  = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';

// Let the built-in server return static files that actually exist on disk,
// but never anything above the project root.
$candidate = realpath($root . $uri);
if (
    $uri !== '/'
    && $candidate !== false
    && is_file($candidate)
    && str_starts_with($candidate, $root . DIRECTORY_SEPARATOR)
) {
    return false;
}

// Normalise the path: strip a leading /index.php so clean and non-clean URLs
// both reach the CI3 router the same way.
$path  = preg_replace('#^/index\.php#', '', $uri);
$path  = ($path === '' || $path === false) ? '/' : $path;
$query = $_SERVER['QUERY_STRING'] ?? '';

$_SERVER['SCRIPT_NAME']     = '/index.php';
$_SERVER['SCRIPT_FILENAME'] = $root . '/public/index.php';
$_SERVER['PHP_SELF']        = '/index.php';
$_SERVER['REQUEST_URI']     = $path . ($query !== '' ? '?' . $query : '');

chdir($root . '/public');

require $root . '/public/index.php';
