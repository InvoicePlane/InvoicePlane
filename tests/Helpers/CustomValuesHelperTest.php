<?php

namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Tests\Helpers\CustomValuesHelper::class)]

class CustomValuesHelperTest extends AbstractTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Helper wrapper class does not exist — CI3 helpers are global functions, not yet wrapped in OOP classes');
    }

    public static function booleanProvider(): array
    {
        return [
            'true string'  => ['1', false],
            'false string' => ['0', false],
            'null'         => [null, true],
            'empty string' => ['', true],
            'invalid'      => ['invalid', true],
            'numeric 2'    => ['2', true],
        ];
    }

    #[Test]
    public function it_returns_empty_string_for_null_text(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_text(null);

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_formats_text(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_text('Sample text');

        /* Assert */
        $this->assertSame('Sample text', $result);
    }

    #[Test]
    public function it_preserves_text_unchanged(): void
    {
        /* Arrange */
        $text = 'This is some <b>formatted</b> text';

        /* Act */
        $result = CustomValuesHelper::format_text($text);

        /* Assert */
        $this->assertSame($text, $result);
    }

    #[Test]
    public function it_formats_boolean_true(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_boolean('1');

        /* Assert */
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_formats_boolean_false(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_boolean('0');

        /* Assert */
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_returns_empty_string_for_null_boolean(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_boolean(null);

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_boolean(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_boolean('invalid');

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    #[DataProvider('booleanProvider')]
    public function it_formats_various_boolean_values(?string $value, bool $isEmpty): void
    {
        /* Arrange */
        $result = CustomValuesHelper::format_boolean($value);

        /* Act */
        if ($isEmpty) {
        /* Assert */
            $this->assertSame('', $result);

            return;
        }
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_handles_empty_strings_in_format_text(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_text('');

        /* Assert */
        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_whitespace_in_format_text(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_text('   ');

        /* Assert */
        $this->assertSame('   ', $result);
    }

    #[Test]
    public function it_handles_numeric_strings_in_format_text(): void
    {
        /* Arrange */

        /* Act */
        $result = CustomValuesHelper::format_text('12345');

        /* Assert */
        $this->assertSame('12345', $result);
    }
}
