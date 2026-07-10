<?php

namespace Tests\Unit\Settings;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TaxRateDecimalPlacesProcessor;

/**
 * Regression tests for the tax rate decimal-places DDL/SQL injection fix
 * (PRs #1481 / #1488, CVSS 7.2).
 *
 * The setting is used to build an ALTER TABLE ... DECIMAL(10, {n}) statement,
 * so it must be strictly validated as an in-range integer before it ever
 * reaches schema-altering code.
 */
class TaxRateDecimalPlacesProcessorTest extends TestCase
{
    private TaxRateDecimalPlacesProcessor $processor;

    protected function setUp(): void
    {
        require_once dirname(__DIR__, 3) . '/application/modules/settings/libraries/TaxRateDecimalPlacesProcessor.php';
        $this->processor = new TaxRateDecimalPlacesProcessor();
    }

    #[Test]
    public function it_accepts_an_integer_within_range(): void
    {
        self::assertSame(4, $this->processor->validateAndNormalize('4', 0, 10));
    }

    #[Test]
    public function it_rejects_a_sql_injection_payload(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->processor->validateAndNormalize('4); DROP TABLE ip_tax_rates; --', 0, 10);
    }

    #[Test]
    public function it_rejects_a_non_numeric_string(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->processor->validateAndNormalize('abc', 0, 10);
    }

    #[Test]
    public function it_rejects_a_value_above_the_max_range(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->processor->validateAndNormalize(999, 0, 10);
    }

    #[Test]
    public function it_rejects_a_negative_value_below_the_min_range(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->processor->validateAndNormalize(-1, 0, 10);
    }

    #[Test]
    public function it_rejects_a_float_masquerading_as_an_integer(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->processor->validateAndNormalize('2.5', 0, 10);
    }

    #[Test]
    public function it_reports_a_schema_change_only_when_the_value_actually_differs(): void
    {
        self::assertFalse($this->processor->shouldAlterSchema(4, 4));
        self::assertTrue($this->processor->shouldAlterSchema(4, 6));
    }
}
