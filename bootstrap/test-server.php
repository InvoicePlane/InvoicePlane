<?php

/**
 * Unified test server for E2E testing.
 *
 * Front-controller for PHP's built-in web server, used by Playwright:
 *   php -S 127.0.0.1:8000 -t . bootstrap/test-server.php
 *
 * Serves static files directly; routes all other requests through public/index.php
 * with normalized REQUEST_URI so clean URLs work in tests.
 */
$root = dirname(__DIR__);
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
