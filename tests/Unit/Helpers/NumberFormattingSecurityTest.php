<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;
use Tests\Support\FakeCiSettings;

/**
 * GHSA-x9qq-6v8r-pfcf: SSRF via thousands_separator / decimal_point settings in mPDF number formatting.
 */
class NumberFormattingSecurityTest extends TestCase
{
    private mixed $previousCi = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->previousCi          = $GLOBALS['unitCiInstance'] ?? null;
        $GLOBALS['unitCiInstance'] = (object) ['mdl_settings' => new FakeCiSettings()];
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/number_helper.php';
    }

    protected function tearDown(): void
    {
        $GLOBALS['unitCiInstance'] = $this->previousCi;

        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_thousands_separator_in_format_currency(): void
    {
        $CI = & get_instance();

        // Simulate admin setting malicious thousands_separator
        $CI->mdl_settings->_data['thousands_separator']       = '<img src="http://attacker.com/x">';
        $CI->mdl_settings->_data['decimal_point']             = '.';
        $CI->mdl_settings->_data['currency_symbol']           = '$';
        $CI->mdl_settings->_data['currency_symbol_placement'] = 'after';
        $CI->mdl_settings->_data['tax_rate_decimal_places']   = '2';

        $result = format_currency(1500.00);

        // Should NOT contain unescaped img tag
        $this->assertStringNotContainsString('<img src="http://attacker.com/x">', $result);
        // Should contain escaped version
        $this->assertStringContainsString('&lt;img', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_decimal_point_in_format_currency(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator']       = ',';
        $CI->mdl_settings->_data['decimal_point']             = '<script>alert(1)</script>';
        $CI->mdl_settings->_data['currency_symbol']           = '$';
        $CI->mdl_settings->_data['currency_symbol_placement'] = 'after';
        $CI->mdl_settings->_data['tax_rate_decimal_places']   = '2';

        $result = format_currency(1.5);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_thousands_separator_in_format_amount(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator']     = '<svg onload=alert(1)>';
        $CI->mdl_settings->_data['decimal_point']           = '.';
        $CI->mdl_settings->_data['tax_rate_decimal_places'] = '2';

        $result = format_amount(1500.00);

        $this->assertStringNotContainsString('<svg', $result);
        $this->assertStringContainsString('&lt;svg', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_decimal_point_in_format_amount(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator']     = ',';
        $CI->mdl_settings->_data['decimal_point']           = '<img src=x>';
        $CI->mdl_settings->_data['tax_rate_decimal_places'] = '2';

        $result = format_amount(1.5);

        $this->assertStringNotContainsString('<img', $result);
        $this->assertStringContainsString('&lt;img', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_thousands_separator_in_format_quantity(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator']   = '<img src="http://169.254.169.254/">';
        $CI->mdl_settings->_data['decimal_point']         = '.';
        $CI->mdl_settings->_data['default_item_decimals'] = '2';

        $result = format_quantity(1500.00);

        $this->assertStringNotContainsString('<img src="http://169.254.169.254/">', $result);
        $this->assertStringContainsString('&lt;img', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_decimal_point_in_format_quantity(): void
    {
        /* Arrange: hostile decimal point; escaping happens before number_format() uses it */
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator']   = ',';
        $CI->mdl_settings->_data['decimal_point']         = '<img src=x>';
        $CI->mdl_settings->_data['default_item_decimals'] = '2';

        /* Act */
        $result = format_quantity(1.5);

        /* Assert: the markup is neutralised and the escaped separator is what got inserted */
        $this->assertStringNotContainsString('<img', $result);
        $this->assertStringContainsString('1&lt;img src=x&gt;5', $result);
    }

    /*
     * Mutation-check on number_helper.php (2026-10-11, scrutinizing PR #1742) found these six
     * behaviors had zero coverage anywhere in the suite. Not security-relevant to #1742's own
     * escaping fix, but real correctness gaps in the same functions — closing them here rather
     * than leaving them open.
     */

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_defaults_to_zero_decimals_in_format_currency_when_no_decimal_point_is_configured(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator']       = ',';
        $CI->mdl_settings->_data['currency_symbol']           = '$';
        $CI->mdl_settings->_data['currency_symbol_placement'] = 'after';
        // decimal_point left unset: FakeCiSettings::setting() then returns '' (falsy).

        $result = format_currency(1234.5);

        $this->assertSame('1,235$', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_omits_the_separator_for_after_placement_unlike_afterspace(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator']       = ',';
        $CI->mdl_settings->_data['decimal_point']             = '.';
        $CI->mdl_settings->_data['currency_symbol']           = '$';
        $CI->mdl_settings->_data['currency_symbol_placement'] = 'after';
        $CI->mdl_settings->_data['tax_rate_decimal_places']   = '2';

        $result = format_currency(10);

        $this->assertSame('10.00$', $result, '"after" must not insert the &nbsp; separator "afterspace" uses.');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_for_a_falsy_amount_in_format_amount(): void
    {
        $this->assertNull(format_amount(0));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_defaults_to_zero_decimals_in_format_amount_when_no_decimal_point_is_configured(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator'] = ',';

        $result = format_amount(1234.5);

        $this->assertSame('1,235', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_for_a_falsy_amount_in_format_quantity(): void
    {
        $this->assertNull(format_quantity(0));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_defaults_to_zero_decimals_in_format_quantity_when_no_decimal_point_is_configured(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator'] = ',';

        $result = format_quantity(1234.5);

        $this->assertSame('1,235', $result);
    }
}
