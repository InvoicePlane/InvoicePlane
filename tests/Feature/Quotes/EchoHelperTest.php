<?php

namespace Tests\Feature\Quotes;

use Modules\Core\Support\ClientHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\Unit\UnitTestCase;

#[CoversClass(ClientHelper::class)]

class EchoHelperTest extends UnitTestCase
{
    public static function specialCharsProvider(): array
    {
        return [
            'less than'    => ['<test>', '&lt;'],
            'greater than' => ['test>', '&gt;'],
            'double quote' => ['"quoted"', '&quot;'],
            'single quote' => ["it's", '&#039;'],
            'ampersand'    => ['A & B', '&amp;'],
        ];
    }

    #[Test]
    public function it_escapes_html_special_chars(): void
    {
        $input = '<script>alert("xss")</script>';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $result);
    }

    #[Test]
    public function it_returns_null_for_null_input(): void
    {
        $result = EchoHelper::htmlsc(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_handles_quotes_in_htmlsc(): void
    {
        $input = "It's a \"test\"";

        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString('&#039;', $result);
        $this->assertStringContainsString('&quot;', $result);
    }

    #[Test]
    public function it_handles_ampersands(): void
    {
        $input = 'Johnson & Johnson';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame('Johnson &amp; Johnson', $result);
    }

    #[Test]
    #[DataProvider('specialCharsProvider')]
    public function it_escapes_various_special_chars(string $input, string $expectedContains): void
    {
        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString($expectedContains, $result);
    }

    #[Test]
    public function it_outputs_escaped_html_chars(): void
    {
        $input = '<b>Bold</b>';

        ob_start();
        EchoHelper::_htmlsc($input);
        $output = ob_get_clean();

        $this->assertSame('&lt;b&gt;Bold&lt;/b&gt;', $output);
    }

    #[Test]
    public function it_returns_empty_string_for_null_htmlsc_output(): void
    {
        ob_start();
        $result = EchoHelper::_htmlsc(null);
        $output = ob_get_clean();

        $this->assertSame('', $result);
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_outputs_html_entities(): void
    {
        $input = '<script>test</script>';

        ob_start();
        EchoHelper::_htmle($input);
        $output = ob_get_clean();

        $this->assertStringContainsString('&lt;', $output);
        $this->assertStringContainsString('&gt;', $output);
    }

    #[Test]
    public function it_returns_empty_string_for_null_htmle_output(): void
    {
        ob_start();
        $result = EchoHelper::_htmle(null);
        $output = ob_get_clean();

        $this->assertSame('', $result);
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_handles_empty_strings(): void
    {
        $result = EchoHelper::htmlsc('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_preserves_safe_text(): void
    {
        $input = 'This is safe text without special chars';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame($input, $result);
    }

    #[Test]
    public function it_handles_unicode_characters(): void
    {
        $input = 'Hello 世界 🌍';

        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString('Hello', $result);
        $this->assertStringContainsString('世界', $result);
    }

    #[Test]
    public function it_handles_numeric_strings(): void
    {
        $input = '12345.67';

        $result = EchoHelper::htmlsc($input);

        $this->assertSame('12345.67', $result);
    }

    #[Test]
    public function it_handles_multiple_special_chars(): void
    {
        $input = '<div class="test" id=\'myId\'>Content & more</div>';

        $result = EchoHelper::htmlsc($input);

        $this->assertStringContainsString('&lt;', $result);
        $this->assertStringContainsString('&gt;', $result);
        $this->assertStringContainsString('&quot;', $result);
        $this->assertStringContainsString('&#039;', $result);
        $this->assertStringContainsString('&amp;', $result);
    }
}
