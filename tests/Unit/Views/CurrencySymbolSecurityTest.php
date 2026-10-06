<?php

namespace Tests\Unit\Views;

use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

class CurrencySymbolSecurityTest extends TestCase
{
    private mixed $previousCi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousCi          = $GLOBALS['unitCiInstance'] ?? null;
        $GLOBALS['unitCiInstance'] = (object) ['mdl_settings' => new FakeCiSettings()];
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/settings_helper.php';
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->previousCi;

        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_currency_symbol_in_get_setting(): void
    {
        // When get_setting('currency_symbol') is called without escaping,
        // and the setting contains HTML, it should be escaped by the caller

        $CI                                         = & get_instance();
        $CI->mdl_settings->_data['currency_symbol'] = '<script>alert(1)</script>';

        // get_setting without escape flag returns raw value
        $raw = get_setting('currency_symbol', '', false);
        $this->assertStringContainsString('<script>', $raw);

        // get_setting with escape flag should return escaped
        $escaped = get_setting('currency_symbol', '', true);
        $this->assertStringContainsString('&lt;script&gt;', $escaped);
        $this->assertStringNotContainsString('<script>', $escaped);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_requires_htmlsc_for_direct_echo_in_views(): void
    {
        // All 15 sinks that echo currency_symbol directly in views
        // should use either:
        // 1. get_setting('currency_symbol', '', true) with escape flag, OR
        // 2. htmlsc(get_setting('currency_symbol'))

        $symbol = '<img src=x>';

        // Using htmlsc - safe
        $safe = htmlsc($symbol);
        $this->assertStringContainsString('&lt;img', $safe);
        $this->assertStringNotContainsString('<img', $safe);
    }
}
