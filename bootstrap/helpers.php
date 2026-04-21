<?php

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
