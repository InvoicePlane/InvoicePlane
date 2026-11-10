<?php

/**
 * Minimal global CodeIgniter seams for isolated helper/library tests.
 *
 * Integration requests run in a separate PHP process and do not load this
 * file, so these stubs cannot replace the real application runtime.
 */
if ( ! function_exists('get_instance')) {
    function &get_instance(): object
    {
        return $GLOBALS['unitCiInstance'];
    }
}

if ( ! function_exists('config_item')) {
    function config_item(string $key): mixed
    {
        return $GLOBALS['unitCiConfig'][$key] ?? null;
    }
}

if ( ! function_exists('base_url')) {
    function base_url(): string
    {
        return (string) ($GLOBALS['unitBaseUrl'] ?? 'http://localhost/');
    }
}

if ( ! function_exists('parse_template')) {
    function parse_template(object $invoice, string $template): string
    {
        return $template;
    }
}
