<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Helpers;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Feature\Quotes\DB;
use Tests\Feature\Quotes\NumberHelper;
use Tests\Feature\Quotes\Setting;

#[CoversClass(Tests\Helpers\NumberHelper::class)]

class NumberHelperTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Clean up settings table
        DB::table('ip_settings')->delete();
    }

    public static function currencyAmountProvider(): array
    {
        return [
            'zero'            => [0, '$0.00'],
            'small amount'    => [0.99, '$0.99'],
            'negative amount' => [-1234.56, '$-1,234.56'],
            'large amount'    => [1234567.89, '$1,234,567.89'],
            'string numeric'  => ['1234.56', '$1,234.56'],
        ];
    }

    #[Test]
    public function it_formats_currency_with_default_settings(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('$1,234.56', $result);
    }

    #[Test]
    public function it_formats_currency_with_symbol_after(): void
    {
        Setting::setValue('currency_symbol', '€');
        Setting::setValue('currency_symbol_placement', 'after');
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');
        Setting::setValue('tax_rate_decimal_places', '2');

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('1.234,56€', $result);
    }

    #[Test]
    public function it_formats_currency_with_symbol_after_space(): void
    {
        Setting::setValue('currency_symbol', '€');
        Setting::setValue('currency_symbol_placement', 'afterspace');
        Setting::setValue('thousands_separator', ' ');
        Setting::setValue('decimal_point', ',');
        Setting::setValue('tax_rate_decimal_places', '2');

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('1 234,56&nbsp;€', $result);
    }

    #[Test]
    public function it_formats_currency_with_zero_decimals(): void
    {
        Setting::setValue('currency_symbol', '$');
        Setting::setValue('currency_symbol_placement', 'before');
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '');
        Setting::setValue('tax_rate_decimal_places', '0');

        $result = NumberHelper::format_currency(1234.56);

        $this->assertSame('$1,235', $result);
    }

    #[Test]
    #[DataProvider('currencyAmountProvider')]
    public function it_formats_various_currency_amounts(int|float|string $amount, string $expected): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_currency($amount);

        $this->assertSame($expected, $result);
    }

    #[Test]
    public function it_formats_amount_with_default_settings(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_amount(1234.56);

        $this->assertSame('1,234.56', $result);
    }

    #[Test]
    public function it_returns_null_for_null_amount(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_amount(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_formats_amount_with_european_format(): void
    {
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');
        Setting::setValue('tax_rate_decimal_places', '2');

        $result = NumberHelper::format_amount(1234.56);

        $this->assertSame('1.234,56', $result);
    }

    #[Test]
    public function it_formats_quantity_with_default_settings(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');
        Setting::setValue('default_item_decimals', '2');

        $result = NumberHelper::format_quantity(123.456);

        $this->assertSame('123.46', $result);
    }

    #[Test]
    public function it_formats_quantity_with_higher_precision(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');
        Setting::setValue('default_item_decimals', '4');

        $result = NumberHelper::format_quantity(123.456789);

        $this->assertSame('123.4568', $result);
    }

    #[Test]
    public function it_returns_null_for_null_quantity(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::format_quantity(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_standardizes_amount_from_european_format(): void
    {
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');

        $result = NumberHelper::standardize_amount('1.234,56');

        $this->assertSame('1234.56', $result);
    }

    #[Test]
    public function it_standardizes_amount_from_us_format(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');

        $result = NumberHelper::standardize_amount('1,234.56');

        $this->assertSame('1234.56', $result);
    }

    #[Test]
    public function it_handles_numeric_amount_standardization(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::standardize_amount(1234.56);

        $this->assertSame(1234.56, $result);
    }

    #[Test]
    public function it_standardizes_amount_with_multiple_dots(): void
    {
        Setting::setValue('thousands_separator', '.');
        Setting::setValue('decimal_point', ',');

        // European format with multiple dots for thousands
        $result = NumberHelper::standardize_amount('1.234.567,89');

        $this->assertSame('1234567.89', $result);
    }

    #[Test]
    public function it_handles_empty_thousands_separator(): void
    {
        Setting::setValue('thousands_separator', '');
        Setting::setValue('decimal_point', ',');

        $result = NumberHelper::standardize_amount('1234,56');

        $this->assertSame('1234.56', $result);
    }

    #[Test]
    public function it_returns_null_for_null_standardize_amount(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::standardize_amount(null);

        $this->assertNull($result);
    }

    #[Test]
    public function it_standardizes_zero(): void
    {
        $this->setDefaultCurrencySettings();

        $result = NumberHelper::standardize_amount('0,00');

        $this->assertSame('0.00', $result);
    }

    #[Test]
    public function it_standardizes_negative_amounts(): void
    {
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');

        $result = NumberHelper::standardize_amount('-1,234.56');

        $this->assertSame('-1234.56', $result);
    }

    private function setDefaultCurrencySettings(): void
    {
        Setting::setValue('currency_symbol', '$');
        Setting::setValue('currency_symbol_placement', 'before');
        Setting::setValue('thousands_separator', ',');
        Setting::setValue('decimal_point', '.');
        Setting::setValue('tax_rate_decimal_places', '2');
        Setting::setValue('default_item_decimals', '2');
    }
}
