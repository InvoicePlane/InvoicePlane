<?php

namespace Tests\Helpers;

use Modules\Core\Support\ClientHelper;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Feature\Quotes\CustomValuesHelper;

#[CoversClass(ClientHelper::class)]

class CustomValuesHelperTest extends AbstractTestCase
{
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
        $result = CustomValuesHelper::format_text(null);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_formats_text(): void
    {
        $result = CustomValuesHelper::format_text('Sample text');

        $this->assertSame('Sample text', $result);
    }

    #[Test]
    public function it_preserves_text_unchanged(): void
    {
        $text = 'This is some <b>formatted</b> text';

        $result = CustomValuesHelper::format_text($text);

        $this->assertSame($text, $result);
    }

    #[Test]
    public function it_formats_boolean_true(): void
    {
        $result = CustomValuesHelper::format_boolean('1');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_formats_boolean_false(): void
    {
        $result = CustomValuesHelper::format_boolean('0');

        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_returns_empty_string_for_null_boolean(): void
    {
        $result = CustomValuesHelper::format_boolean(null);

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_returns_empty_string_for_invalid_boolean(): void
    {
        $result = CustomValuesHelper::format_boolean('invalid');

        $this->assertSame('', $result);
    }

    #[Test]
    #[DataProvider('booleanProvider')]
    public function it_formats_various_boolean_values($value, bool $isEmpty): void
    {
        $result = CustomValuesHelper::format_boolean($value);

        if ($isEmpty) {
            $this->assertSame('', $result);

            return;
        }
        $this->assertNotEmpty($result);
    }

    #[Test]
    public function it_handles_empty_strings_in_format_text(): void
    {
        $result = CustomValuesHelper::format_text('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function it_handles_whitespace_in_format_text(): void
    {
        $result = CustomValuesHelper::format_text('   ');

        $this->assertSame('   ', $result);
    }

    #[Test]
    public function it_handles_numeric_strings_in_format_text(): void
    {
        $result = CustomValuesHelper::format_text('12345');

        $this->assertSame('12345', $result);
    }
}
