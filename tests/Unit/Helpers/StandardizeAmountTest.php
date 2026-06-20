<?php

namespace Tests\Unit\Helpers;

use Tests\Support\CITestCase;

/**
 * Tests for standardize_amount() from number_helper.php.
 *
 * This function normalises locale-formatted strings (e.g. "1.234,56")
 * into plain floats — critical for correct invoice totals and the
 * eventual Peppol/UBL amount fields which must use a period decimal.
 */
class StandardizeAmountTest extends CITestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (! function_exists('standardize_amount')) {
            require_once APPPATH . 'helpers/number_helper.php';
        }
    }

    // -----------------------------------------------------------------
    // Period-decimal, comma-thousands  (US/EN locale)
    // -----------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_a_numeric_value_unchanged(): void
    {
        /* Arrange */
        $this->setSetting('thousands_separator', ',');
        $this->setSetting('decimal_point', '.');

        /* Act */
        $result = standardize_amount(1234.56);

        /* Assert */
        $this->assertSame(1234.56, $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_a_plain_numeric_string_unchanged(): void
    {
        /* Arrange */
        $this->setSetting('thousands_separator', ',');
        $this->setSetting('decimal_point', '.');

        /* Act */
        $result = standardize_amount('1234.56');

        /* Assert */
        $this->assertSame('1234.56', $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_strips_comma_thousands_separator_with_period_decimal(): void
    {
        /* Arrange */
        $this->setSetting('thousands_separator', ',');
        $this->setSetting('decimal_point', '.');

        /* Act */
        $result = standardize_amount('1,234.56');

        /* Assert */
        $this->assertSame('1234.56', (string) $result);
    }

    // -----------------------------------------------------------------
    // Period-thousands, comma-decimal  (EU / DE / NL locale)
    // -----------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_strips_period_thousands_separator_with_comma_decimal(): void
    {
        /* Arrange */
        $this->setSetting('thousands_separator', '.');
        $this->setSetting('decimal_point', ',');

        /* Act */
        $result = standardize_amount('1.234,56');

        /* Assert */
        $this->assertSame('1234.56', (string) $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_normalises_comma_decimal_when_no_thousands_separator_is_set(): void
    {
        /* Arrange */
        $this->setSetting('thousands_separator', '');
        $this->setSetting('decimal_point', ',');

        /* Act */
        $result = standardize_amount('1234,56');

        /* Assert */
        $this->assertSame('1234.56', (string) $result);
    }

    // -----------------------------------------------------------------
    // Edge cases
    // -----------------------------------------------------------------

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_null_unchanged(): void
    {
        /* Arrange */

        /* Act */
        $result = standardize_amount(null);

        /* Assert */
        $this->assertNull($result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_returns_zero_string_unchanged(): void
    {
        /* Arrange */

        /* Act */
        $result = standardize_amount('0');

        /* Assert */
        $this->assertSame('0', $result);
    }
}
