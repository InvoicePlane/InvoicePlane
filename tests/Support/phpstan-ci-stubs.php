<?php

/**
 * PHPStan stub signatures for CodeIgniter 3 *system* functions.
 *
 * These live in vendor/pocketarc/codeigniter/system/ (Common.php, url_helper.php,
 * language_helper.php, …) and are loaded by the framework at runtime, not by an
 * autoloader. Declaring their signatures here lets static analysis resolve calls
 * to them without executing framework code. This file is referenced via
 * `stubFiles` in phpstan.neon and is never executed.
 */

/** @return object */
function &get_instance() {}

function site_url(string|array $uri = '', ?string $protocol = null): string
{
}

function base_url(string|array $uri = '', ?string $protocol = null): string
{
}

function anchor(string|array $uri = '', string $title = '', string|array $attributes = ''): string
{
}

function redirect(string $uri = '', string $method = 'auto', ?int $code = null): void
{
}

/** @return mixed */
function config_item(string $item)
{
}

function log_message(string $level, string $message): void
{
}

function show_error(string|array $message, int $status_code = 500, string $heading = 'An Error Was Encountered'): void {}

function show_404(string $page = '', bool $log_error = true): void {}

function html_escape(mixed $var, bool $double_encode = true): mixed {}

function lang(string $line, string $for = '', array $attributes = []): string|false {}

function form_error(string $field = '', string $prefix = '', string $suffix = ''): string {}

function write_file(string $path, string $data, string $mode = 'wb'): bool {}

/** @return array<mixed> */
function directory_map(string $source_dir, int $directory_depth = 0, bool $hidden = false): array {}

function is_cli(): bool {}

function random_string(string $type = 'alnum', int $len = 8): string {}

/** @return mixed */
function env(string $key, mixed $default = null) {}

function env_bool(string $key, bool $default = false): bool {}
