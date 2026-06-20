<?php

if ( ! defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * MY_URI overrides CI_URI so that the integration-test subprocess (a CLI PHP
 * process that simulates HTTP via $_SERVER['REQUEST_URI']) uses the correct
 * route rather than argv-based routing which would always yield an empty URI
 * and therefore fall back to the default controller.
 */
#[AllowDynamicProperties]
class MY_URI extends CI_URI
{
    /**
     * When REQUEST_URI is populated (i.e. the test subprocess), derive the
     * URI string from it rather than from argv.  This overrides the parent's
     * _parse_argv(), which is called inside parent::__construct() via $this.
     */
    protected function _parse_argv(): string
    {
        if (isset($_SERVER['REQUEST_URI'])) {
            $uri = (string) $_SERVER['REQUEST_URI'];

            // Strip the front-controller script name if present.
            $script = $_SERVER['SCRIPT_NAME'] ?? '/index.php';
            if (str_starts_with($uri, $script)) {
                $uri = substr($uri, strlen($script));
            }

            // Drop the query string.
            if (($q = strpos($uri, '?')) !== false) {
                $uri = substr($uri, 0, $q);
            }

            return ltrim($uri, '/');
        }

        $args = array_slice($_SERVER['argv'] ?? [], 1);

        return $args ? implode('/', $args) : '';
    }
}
