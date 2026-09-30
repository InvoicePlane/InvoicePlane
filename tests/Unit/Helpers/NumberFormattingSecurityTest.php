<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class NumberFormattingSecurityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_thousands_separator_in_format_currency(): void
    {
        $CI = & get_instance();

        // Simulate admin setting malicious thousands_separator
        $CI->mdl_settings->_data['thousands_separator'] = '<img src="http://attacker.com/x">';
        $CI->mdl_settings->_data['decimal_point'] = '.';
        $CI->mdl_settings->_data['currency_symbol'] = '$';
        $CI->mdl_settings->_data['currency_symbol_placement'] = 'after';
        $CI->mdl_settings->_data['tax_rate_decimal_places'] = '2';

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

        $CI->mdl_settings->_data['thousands_separator'] = ',';
        $CI->mdl_settings->_data['decimal_point'] = '<script>alert(1)</script>';
        $CI->mdl_settings->_data['currency_symbol'] = '$';
        $CI->mdl_settings->_data['currency_symbol_placement'] = 'after';
        $CI->mdl_settings->_data['tax_rate_decimal_places'] = '2';

        $result = format_currency(1.5);

        $this->assertStringNotContainsString('<script>', $result);
        $this->assertStringContainsString('&lt;script&gt;', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_thousands_separator_in_format_amount(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator'] = '<svg onload=alert(1)>';
        $CI->mdl_settings->_data['decimal_point'] = '.';
        $CI->mdl_settings->_data['tax_rate_decimal_places'] = '2';

        $result = format_amount(1500.00);

        $this->assertStringNotContainsString('<svg', $result);
        $this->assertStringContainsString('&lt;svg', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_decimal_point_in_format_amount(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator'] = ',';
        $CI->mdl_settings->_data['decimal_point'] = '<img src=x>';
        $CI->mdl_settings->_data['tax_rate_decimal_places'] = '2';

        $result = format_amount(1.5);

        $this->assertStringNotContainsString('<img', $result);
        $this->assertStringContainsString('&lt;img', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_thousands_separator_in_format_quantity(): void
    {
        $CI = & get_instance();

        $CI->mdl_settings->_data['thousands_separator'] = '<img src="http://169.254.169.254/">';
        $CI->mdl_settings->_data['decimal_point'] = '.';
        $CI->mdl_settings->_data['default_item_decimals'] = '2';

        $result = format_quantity(1500.00);

        $this->assertStringNotContainsString('<img src="http://169.254.169.254/">', $result);
        $this->assertStringContainsString('&lt;img', $result);
    }
}
