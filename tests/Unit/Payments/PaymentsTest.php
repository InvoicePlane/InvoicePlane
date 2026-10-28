<?php

declare(strict_types=1);

namespace Tests\Unit\Payments;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PaymentsTest extends TestCase
{
    #[Test]
    public function it_converts_standard_currency_amounts_to_minor_units(): void
    {
        $this->setUpCurrencyMinorUnits();

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
        $this->setUpCurrencyMinorUnits();

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
        $this->setUpCurrencyMinorUnits();

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
        $this->setUpCurrencyMinorUnits();

        /* Arrange */

        $amount = '12.34';

        /* Act */

        $this->expectException(InvalidArgumentException::class);

        amount_to_minor_units($amount, 0);

        /* Assert */

        self::fail('The invalid multiplier should have thrown an exception.');
    }

    protected function setUpCurrencyMinorUnits(): void
    {
        require_once ROOT_PATH . '/application/helpers/currency_helper.php';
    }
}
