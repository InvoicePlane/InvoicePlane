<?php

namespace Tests\Unit\Libraries\Gateways;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Unit coverage for the amount arithmetic behind the Stripe checkout flow.
 *
 * guest/gateways/Stripe.php converts the invoice balance to Stripe's integer
 * minor-unit amount with
 *   amount_to_minor_units($balance, stripe_minor_unit_multiplier($currency))
 * on the way out, and back with amount_from_minor_units(...) when validating the
 * callback. Both helpers are pure; this pins the zero-decimal-currency handling
 * (JPY et al. use a multiplier of 1, everything else 100) and the rounding the
 * checkout session and the callback amount check depend on.
 *
 * The end-to-end behaviour is exercised in
 * tests/Feature/Payments/StripeFlowTest.php.
 */
class StripeMinorUnitTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 4) . '/application/helpers/stripe_helper.php';
        require_once dirname(__DIR__, 4) . '/application/helpers/currency_helper.php';
    }

    /**
     * Every currency stripe_minor_unit_multiplier() treats as zero-decimal.
     * Keep this in lock-step with the helper's list.
     *
     * @return array<string, array{0: string}>
     */
    public static function zeroDecimalCurrencyProvider(): array
    {
        $codes = ['BIF', 'CLP', 'DJF', 'GNF', 'JPY', 'KMF', 'KRW', 'MGA', 'PYG', 'RWF', 'VND', 'VUV', 'XAF', 'XOF', 'XPF'];

        $cases = [];
        foreach ($codes as $code) {
            $cases[$code] = [$code];
        }
        $cases['lowercase jpy'] = ['jpy'];

        return $cases;
    }

    /** @return array<string, array{0: string}> */
    public static function decimalCurrencyProvider(): array
    {
        return [
            'EUR'           => ['EUR'],
            'USD'           => ['USD'],
            'GBP'           => ['GBP'],
            'lowercase eur' => ['eur'],
            'unknown code'  => ['ZZZ'],
            'empty string'  => [''],
        ];
    }

    // -------------------------------------------------------------------------
    // stripe_minor_unit_multiplier
    // -------------------------------------------------------------------------

    #[Test]
    #[DataProvider('zeroDecimalCurrencyProvider')]
    public function it_uses_a_multiplier_of_one_for_zero_decimal_currencies(string $currency): void
    {
        self::assertSame(1, stripe_minor_unit_multiplier($currency));
    }

    #[Test]
    #[DataProvider('decimalCurrencyProvider')]
    public function it_uses_a_multiplier_of_one_hundred_for_decimal_currencies(string $currency): void
    {
        self::assertSame(100, stripe_minor_unit_multiplier($currency));
    }

    // -------------------------------------------------------------------------
    // amount_to_minor_units — the checkout session line-item amount
    // -------------------------------------------------------------------------

    #[Test]
    public function it_converts_a_decimal_currency_balance_to_integer_cents(): void
    {
        self::assertSame(10_000, amount_to_minor_units('100.00', stripe_minor_unit_multiplier('EUR')));
        self::assertSame(1_999, amount_to_minor_units('19.99', stripe_minor_unit_multiplier('EUR')));
    }

    #[Test]
    public function it_sends_a_zero_decimal_currency_balance_unscaled(): void
    {
        self::assertSame(100, amount_to_minor_units('100.00', stripe_minor_unit_multiplier('JPY')));
    }

    #[Test]
    public function it_rounds_to_the_nearest_minor_unit(): void
    {
        self::assertSame(2_000, amount_to_minor_units('19.999', stripe_minor_unit_multiplier('EUR')));
    }

    #[Test]
    public function it_rejects_a_multiplier_below_one(): void
    {
        $this->expectException(InvalidArgumentException::class);

        amount_to_minor_units('10.00', 0);
    }

    // -------------------------------------------------------------------------
    // amount_from_minor_units — the callback amount check
    // -------------------------------------------------------------------------

    #[Test]
    public function it_converts_stripe_cents_back_to_a_major_amount(): void
    {
        self::assertSame(50.0, amount_from_minor_units(5_000, stripe_minor_unit_multiplier('EUR')));
    }

    #[Test]
    public function it_leaves_a_zero_decimal_currency_amount_unscaled_on_the_way_back(): void
    {
        self::assertSame(100.0, amount_from_minor_units(100, stripe_minor_unit_multiplier('JPY')));
    }

    #[Test]
    public function it_round_trips_a_decimal_currency_balance(): void
    {
        $multiplier = stripe_minor_unit_multiplier('EUR');

        self::assertSame(19.99, amount_from_minor_units(amount_to_minor_units('19.99', $multiplier), $multiplier));
    }

    #[Test]
    public function it_rejects_a_multiplier_below_one_on_the_way_back(): void
    {
        $this->expectException(InvalidArgumentException::class);

        amount_from_minor_units(1_000, 0);
    }
}
