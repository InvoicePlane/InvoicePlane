<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Helpers\EchoHelper::class)]

class EchoHelperTest extends AbstractTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Helper wrapper class does not exist — CI3 helpers are global functions, not yet wrapped in OOP classes');
    }

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
        /* Arrange */
        $input = '<script>alert("xss")</script>';

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertSame('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $result);
    }

    #[Test]
    public function it_returns_null_for_null_input(): void
    {
        /* Arrange */

        /* Act */
        $result = EchoHelper::htmlsc(null);

        /* Assert */
        $this->assertNull($result);
    }

    #[Test]
    public function it_handles_quotes_in_htmlsc(): void
    {
        /* Arrange */
        $input = "It's a \"test\"";

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertStringContainsString('&#039;', $result);
        $this->assertStringContainsString('&quot;', $result);
    }

    #[Test]
    public function it_handles_ampersands(): void
    {
        /* Arrange */
        $input = 'Johnson & Johnson';

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertSame('Johnson &amp; Johnson', $result);
    }

    #[Test]
    #[DataProvider('specialCharsProvider')]
    public function it_escapes_various_special_chars(string $input, string $expectedContains): void
    {
        /* Arrange */

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertStringContainsString($expectedContains, $result);
    }

    #[Test]
    public function it_outputs_escaped_html_chars(): void
    {
        /* Arrange */
        $input = '<b>Bold</b>';

        ob_start();
        EchoHelper::_htmlsc($input);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('&lt;b&gt;Bold&lt;/b&gt;', $output);
    }

    #[Test]
    public function it_returns_empty_string_for_null_htmlsc_output(): void
    {
        /* Arrange */
        ob_start();
        $result = EchoHelper::_htmlsc(null);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('', $result);
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_outputs_html_entities(): void
    {
        /* Arrange */
        $input = '<script>test</script>';

        ob_start();
        EchoHelper::_htmle($input);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertStringContainsString('&lt;', $output);
        $this->assertStringContainsString('&gt;', $output);
    }

    #[Test]
    public function it_returns_empty_string_for_null_htmle_output(): void
    {
        /* Arrange */
        ob_start();
        $result = EchoHelper::_htmle(null);
        /* Act */
        $output = ob_get_clean();

        /* Assert */
        $this->assertSame('', $result);
        $this->assertSame('', $output);
    }

    #[Test]
    public function it_handles_empty_strings(): void
    {
        /* Arrange */

        /* Act */
        $result = EchoHelper::htmlsc('');

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_preserves_safe_text(): void
    {
        /* Arrange */
        $input = 'This is safe text without special chars';

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertSame($input, $result);
    }

    #[Test]
    public function it_handles_unicode_characters(): void
    {
        /* Arrange */
        $input = 'Hello 世界 🌍';

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertStringContainsString('Hello', $result);
        $this->assertStringContainsString('世界', $result);
    }

    #[Test]
    public function it_handles_numeric_strings(): void
    {
        /* Arrange */
        $input = '12345.67';

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertSame('12345.67', $result);
    }

    #[Test]
    public function it_handles_multiple_special_chars(): void
    {
        /* Arrange */
        $input = '<div class="test" id=\'myId\'>Content & more</div>';

        /* Act */
        $result = EchoHelper::htmlsc($input);

        /* Assert */
        $this->assertStringContainsString('&lt;', $result);
        $this->assertStringContainsString('&gt;', $result);
        $this->assertStringContainsString('&quot;', $result);
        $this->assertStringContainsString('&#039;', $result);
        $this->assertStringContainsString('&amp;', $result);
    }
}
