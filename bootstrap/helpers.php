<?php

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        return $_ENV[$key]
            ?? $_SERVER[$key]
            ?? getenv($key)
            ?? $default;
    }
}

if (!function_exists('env_bool')) {
    function env_bool(string $key, bool $default = false): bool
    {
        $value = $_ENV[$key] ?? null;

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        static $basePath = null;

        if ($basePath === null) {
            $dir = __DIR__;

            while ($dir !== dirname($dir)) {
                if (file_exists($dir . '/composer.json')) {
                    $basePath = $dir;
                    break;
                }

                $dir = dirname($dir);
            }

            if ($basePath === null) {
                $basePath = realpath(__DIR__ . '/../../..');
            }
        }

        return $path === ''
            ? $basePath
            : $basePath . '/' . ltrim($path, '/');
    }
}
