<?php

/**
 * Mock for mdl_settings used by models and helpers.
 *
 * Tests can call MockSettings::set('key', 'value') before exercising
 * code that reads settings via get_setting() / $CI->mdl_settings->setting().
 */
class MockSettings
{
    private static array $store = [
        'tax_rate_decimal_places' => '2',
        'default_list_limit'      => '15',
        'default_item_decimals'   => '2',
        'currency_symbol'         => '€',
        'currency_symbol_placement' => 'after',
        'thousands_separator'     => '.',
        'decimal_point'           => ',',
        'default_language'        => 'english',
    ];

    public static function set(string $key, mixed $value): void
    {
        self::$store[$key] = $value;
    }

    public static function reset(): void
    {
        self::$store = [];
    }

    public function setting(string $key): mixed
    {
        return self::$store[$key] ?? null;
    }

    /** Called by Base_Controller — safe to no-op in tests. */
    public function load_settings(): void {}
}
