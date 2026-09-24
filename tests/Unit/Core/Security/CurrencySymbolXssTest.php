<?php

namespace Tests\Unit\Security;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Regression coverage for the stored XSS in the shared public invoice/quote
 * template (CWE-79, reported by crypto-nidh).
 *
 * The `currency_symbol` setting is admin-controlled but rendered on the
 * unauthenticated guest invoice/quote pages through format_currency(). Every
 * other user-controlled value in those templates is escaped with htmlsc();
 * format_currency() must neutralise its symbol the same way so a payload such
 * as `<script>...</script>` cannot execute in a client's browser.
 */
class CurrencySymbolXssTest extends TestCase
{
    private const PAYLOAD = '<script>alert(document.cookie)</script>';

    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/helpers/echo_helper.php';
        require_once dirname(__DIR__, 3) . '/application/helpers/number_helper.php';
    }

    protected function tearDown(): void
    {
        unset($GLOBALS['unitCiInstance']);
    }

    #[Test]
    public function it_escapes_html_in_the_currency_symbol_for_before_placement(): void
    {
        /* Arrange */
        $this->fakeSettings(self::PAYLOAD, 'before');

        /* Act */
        $output = format_currency(1234.5);

        /* Assert */
        self::assertStringNotContainsString('<script>', $output);
        self::assertStringContainsString('&lt;script&gt;', $output);
        self::assertStringStartsWith('&lt;script&gt;', $output);
    }

    #[Test]
    public function it_escapes_html_in_the_currency_symbol_for_afterspace_placement(): void
    {
        /* Arrange */
        $this->fakeSettings(self::PAYLOAD, 'afterspace');

        /* Act */
        $output = format_currency(1234.5);

        /* Assert */
        self::assertStringNotContainsString('<script>', $output);
        self::assertStringContainsString('&lt;script&gt;alert(document.cookie)&lt;/script&gt;', $output);
    }

    #[Test]
    public function it_escapes_html_in_the_currency_symbol_for_after_placement(): void
    {
        /* Arrange */
        $this->fakeSettings(self::PAYLOAD, 'after');

        /* Act */
        $output = format_currency(1234.5);

        /* Assert */
        self::assertStringNotContainsString('<script>', $output);
        self::assertStringEndsWith('&lt;script&gt;alert(document.cookie)&lt;/script&gt;', $output);
    }

    #[Test]
    public function it_escapes_an_attribute_breaking_currency_symbol(): void
    {
        /* Arrange */
        $this->fakeSettings('"><img src=x onerror=alert(1)>', 'before');

        /* Act */
        $output = format_currency(10);

        /* Assert */
        self::assertStringNotContainsString('<img', $output);
        self::assertStringNotContainsString('">', $output);
        self::assertStringContainsString('&lt;img src=x onerror=alert(1)&gt;', $output);
    }

    #[Test]
    public function it_preserves_a_benign_currency_symbol(): void
    {
        /* Arrange */
        $this->fakeSettings('€', 'afterspace');

        /* Act */
        $output = format_currency(1234.5);

        /* Assert */
        self::assertSame('1,234.50&nbsp;€', $output);
    }

    private function fakeSettings(string $currencySymbol, string $placement): void
    {
        $settings = [
            'currency_symbol'           => $currencySymbol,
            'currency_symbol_placement' => $placement,
            'thousands_separator'       => ',',
            'decimal_point'             => '.',
            'tax_rate_decimal_places'   => '2',
        ];

        $GLOBALS['unitCiInstance'] = new class ($settings) {
            public object $load;

            public object $mdl_settings;

            public function __construct(array $settings)
            {
                $this->load = new class () {
                    public function helper(string|array $helper): void {}
                };

                $this->mdl_settings = new class ($settings) {
                    public function __construct(private array $settings) {}

                    public function setting(string $key): string
                    {
                        return (string) ($this->settings[$key] ?? '');
                    }
                };
            }
        };
    }
}
