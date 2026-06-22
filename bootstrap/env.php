<?php

if ( ! function_exists('ci_env')) {
    function ci_env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key] ?? $default;
    }
}

if ( ! function_exists('ci_env_bool')) {
    function ci_env_bool(string $key, string $default = 'false'): bool
    {
        return ci_env($key, $default) === 'true';
    }
}

if ( ! function_exists('env_bool')) {
    function env_bool(string $key, string $default = 'false'): bool
    {
        return ci_env_bool($key, $default);
    }
}
