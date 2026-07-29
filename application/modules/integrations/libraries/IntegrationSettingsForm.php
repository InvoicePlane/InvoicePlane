<?php

defined('BASEPATH') || exit('No direct script access allowed');

final class IntegrationSettingsForm
{
    private const SUPPORTED_TYPES = [
        'checkbox',
        'password',
        'path',
        'select',
        'text',
        'url',
    ];

    public static function normalizeSchema(array $schema, array $defaults): array
    {
        $normalized = [];

        foreach ($schema as $name => $definition) {
            if (
                ! is_string($name)
                || preg_match('/^[a-z][a-z0-9_]*$/', $name) !== 1
                || ! is_array($definition)
            ) {
                throw new InvalidArgumentException('Invalid provider setting schema.');
            }

            $type = $definition['type'] ?? 'text';
            if ( ! is_string($type) || ! in_array($type, self::SUPPORTED_TYPES, true)) {
                throw new InvalidArgumentException('Unsupported provider setting type: ' . $name);
            }

            $options = $definition['options'] ?? [];
            if ($type === 'select' && ( ! is_array($options) || $options === [])) {
                throw new InvalidArgumentException('Select settings require options: ' . $name);
            }

            $normalized[$name] = [
                'type'        => $type,
                'label'       => is_string($definition['label'] ?? null) ? $definition['label'] : $name,
                'required'    => (bool) ($definition['required'] ?? false),
                'sensitive'   => (bool) ($definition['sensitive'] ?? $type === 'password'),
                'placeholder' => is_string($definition['placeholder'] ?? null)
                    ? $definition['placeholder']
                    : self::defaultPlaceholder($defaults[$name] ?? null),
                'default'     => $defaults[$name] ?? ($type === 'checkbox' ? false : ''),
                'options'     => $options,
            ];
        }

        return $normalized;
    }

    public static function collect(
        array $schema,
        array $existing,
        callable $input
    ): array {
        $settings = [];

        foreach ($schema as $name => $definition) {
            $rawValue = $input($name);

            if ($definition['type'] === 'checkbox') {
                $settings[$name] = ! empty($rawValue);
                continue;
            }

            if ($rawValue !== null && ! is_scalar($rawValue)) {
                throw new InvalidArgumentException('Invalid provider setting: ' . $name);
            }

            $value = (string) ($rawValue ?? '');
            if ($definition['type'] !== 'password') {
                $value = trim($value);
            }

            if ($definition['sensitive'] && $value === '' && array_key_exists($name, $existing)) {
                if ( ! is_scalar($existing[$name]) && $existing[$name] !== null) {
                    throw new InvalidArgumentException('Invalid stored provider setting: ' . $name);
                }

                $value = (string) ($existing[$name] ?? '');
            }

            if ($definition['required'] && $value === '') {
                throw new InvalidArgumentException('Missing required provider setting: ' . $name);
            }

            if ($value === '' && $definition['sensitive']) {
                continue;
            }

            self::validateValue($name, $value, $definition);
            $settings[$name] = $value;
        }

        return $settings;
    }

    private static function validateValue(string $name, string $value, array $definition): void
    {
        if (strlen($value) > 4096 || preg_match('/[\x00-\x1F\x7F]/', $value) === 1) {
            throw new InvalidArgumentException('Invalid provider setting: ' . $name);
        }

        if ($definition['type'] === 'url' && $value !== '' && ! self::isSafeHttpsUrl($value)) {
            throw new InvalidArgumentException('Invalid provider URL: ' . $name);
        }

        if ($definition['type'] === 'path' && $value !== '' && ! self::isRelativeApiPath($value)) {
            throw new InvalidArgumentException('Invalid provider endpoint: ' . $name);
        }

        if (
            $definition['type'] === 'select'
            && $value !== ''
            && ! array_key_exists($value, $definition['options'])
        ) {
            throw new InvalidArgumentException('Invalid provider option: ' . $name);
        }
    }

    private static function isSafeHttpsUrl(string $url): bool
    {
        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            return false;
        }

        $parsed = parse_url($url);
        if (($parsed['scheme'] ?? '') !== 'https' || empty($parsed['host'])) {
            return false;
        }

        $ip = filter_var($parsed['host'], FILTER_VALIDATE_IP);

        return ! (
            $ip !== false
            && ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)
        );
    }

    private static function isRelativeApiPath(string $path): bool
    {
        return str_starts_with($path, '/')
            && ! str_starts_with($path, '//')
            && filter_var($path, FILTER_VALIDATE_URL) === false;
    }

    private static function defaultPlaceholder(mixed $default): string
    {
        return is_scalar($default) && ! is_bool($default) ? (string) $default : '';
    }
}
