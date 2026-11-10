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

// Serve static files that actually exist on disk: first the web root (public/, where "yarn build"
// writes assets/ and where favicon.ico and robots.txt live), then the project root (uploads/ logos,
// as the production web server maps them, and only under /uploads/). Never anything above it, never PHP
// and never a dotfile.
//
// The file is sent from here instead of returning false: returning false makes PHP's built-in server
// look the file up under its own document root, which is whatever -t says ("-t ." in the E2E workflow,
// so /assets/... would be sought in ./assets, where the sources no longer live) and not public/.
foreach ([$root . '/public', $root] as $base) {
    $candidate = realpath($base . $uri);
    if (
        $uri !== '/'
        && $candidate !== false
        && is_file($candidate)
        && str_starts_with($candidate, $base . DIRECTORY_SEPARATOR)
        && ! str_ends_with($candidate, '.php')
        && ! str_starts_with(basename($candidate), '.')
        && ($base !== $root || str_starts_with($uri, '/uploads/'))
    ) {
        send_static_file($candidate);

        return true;
    }
}

function send_static_file(string $file): void
{
    $types = [
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'application/javascript; charset=utf-8',
        'json'  => 'application/json',
        'map'   => 'application/json',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'eot'   => 'application/vnd.ms-fontobject',
        'txt'   => 'text/plain; charset=utf-8',
        'html'  => 'text/html; charset=utf-8',
        'pdf'   => 'application/pdf',
    ];
    $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));

    header('Content-Type: ' . ($types[$extension] ?? (mime_content_type($file) ?: 'application/octet-stream')));
    header('Content-Length: ' . filesize($file));
    readfile($file);
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
