<?php

namespace Tests\Unit\Payments;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CurrencyMinorUnitsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        require_once dirname(__DIR__, 3) . '/application/helpers/currency_helper.php';
    }

    #[Test]
    public function it_converts_standard_currency_amounts_to_minor_units(): void
    {
        /* Arrange */
        $amount = '12.34';

        /* Act */
        $minorUnits = amount_to_minor_units($amount, 100);

        /* Assert */
        self::assertSame(1234, $minorUnits);
    }

    #[Test]
    public function it_converts_zero_decimal_currency_amounts_without_scaling(): void
    {
        /* Arrange */
        $amount = '500';

        /* Act */
        $minorUnits = amount_to_minor_units($amount, 1);

        /* Assert */
        self::assertSame(500, $minorUnits);
    }

    #[Test]
    public function it_converts_minor_units_back_to_major_units(): void
    {
        /* Arrange */
        $minorUnits = 1234;

        /* Act */
        $amount = amount_from_minor_units($minorUnits, 100);

        /* Assert */
        self::assertSame(12.34, $amount);
    }

    #[Test]
    public function it_rejects_a_non_positive_minor_unit_multiplier(): void
    {
        /* Arrange */
        $amount = '12.34';

        /* Act */
        $this->expectException(InvalidArgumentException::class);
        amount_to_minor_units($amount, 0);

        /* Assert */
        self::fail('The invalid multiplier should have thrown an exception.');
    }
}
