<?php

namespace Tests\Unit\Helpers;

use PHPUnit\Framework\TestCase;

class JavaScriptEncodingSecurityTest extends TestCase
{
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_single_quotes()
    {
        $this->assertStringContainsString("\'", encode_for_javascript_string("it's"));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_double_quotes()
    {
        $this->assertStringContainsString('\"', encode_for_javascript_string('say "hello"'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_backslashes()
    {
        $this->assertStringContainsString('\\\\', encode_for_javascript_string('path\\to\\file'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_carriage_returns()
    {
        $this->assertStringContainsString('\r', encode_for_javascript_string("line\rreturn"));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_newlines()
    {
        $this->assertStringContainsString('\n', encode_for_javascript_string("line\nbreak"));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_forward_slashes()
    {
        $this->assertStringContainsString('\/', encode_for_javascript_string('</script>'));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_javascript_injection_via_quote_breakout()
    {
        $xss_payload = "1' + window['ale'+'rt']('XSS '+document.domain) + '";
        $encoded = encode_for_javascript_string($xss_payload);

        $this->assertStringNotContainsString("alert('XSS", $encoded);
        $this->assertStringContainsString("\\'", $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_prevents_javascript_injection_via_newline_injection()
    {
        $xss_payload = "1\nalert('XSS')//";
        $encoded = encode_for_javascript_string($xss_payload);

        $this->assertStringNotContainsString("\nalert", $encoded);
        $this->assertStringContainsString('\n', $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_integer_input()
    {
        $encoded = encode_for_javascript_string(0);
        $this->assertEquals('0', $encoded);

        $encoded = encode_for_javascript_string(5);
        $this->assertEquals('5', $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_handles_empty_string()
    {
        $encoded = encode_for_javascript_string('');
        $this->assertEquals('', $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_allows_safe_characters()
    {
        $safe = 'hello123';
        $encoded = encode_for_javascript_string($safe);
        $this->assertEquals($safe, $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_unicode_line_separator()
    {
        $payload = "value\u{2028}alert('xss')";
        $encoded = encode_for_javascript_string($payload);

        $this->assertStringContainsString(' ', $encoded);
        $this->assertStringNotContainsString("\u{2028}", $encoded);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_escapes_unicode_paragraph_separator()
    {
        $payload = "value\u{2029}alert('xss')";
        $encoded = encode_for_javascript_string($payload);

        $this->assertStringContainsString(' ', $encoded);
        $this->assertStringNotContainsString("\u{2029}", $encoded);
    }
}
