<?php

namespace Tests\Support;

/**
 * Stand-in for Mdl_settings in isolated unit tests: setting() semantics match
 * the real model (empty or unset values fall back to the default).
 */
final class FakeCiSettings
{
    /** @var array<string, string> */
    public array $_data = [];

    public function setting(string $key, string $default = ''): string
    {
        return (isset($this->_data[$key]) && $this->_data[$key] !== '') ? $this->_data[$key] : $default;
    }
}
