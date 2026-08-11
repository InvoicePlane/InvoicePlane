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
        /* Arrange */
        $value = '4';

        /* Act */
        $normalized = $this->processor->validateAndNormalize($value, 0, 10);

        /* Assert */
        self::assertSame(4, $normalized);
    }

    #[Test]
    public function it_rejects_a_sql_injection_payload(): void
    {
        /* Arrange */

        /* Act */
        $this->expectException(InvalidArgumentException::class);

        /* Assert */
        $this->processor->validateAndNormalize('4); DROP TABLE ip_tax_rates; --', 0, 10);
    }

    #[Test]
    public function it_rejects_a_non_numeric_string(): void
    {
        /* Arrange */
        $value = 'abc';

        /* Act */
        $this->expectException(InvalidArgumentException::class);

        /* Assert */
        $this->processor->validateAndNormalize($value, 0, 10);
    }

    #[Test]
    public function it_rejects_a_value_above_the_max_range(): void
    {
        /* Arrange */

        /* Act */
        $this->expectException(InvalidArgumentException::class);

        /* Assert */
        $this->processor->validateAndNormalize(999, 0, 10);
    }

    #[Test]
    public function it_rejects_a_negative_value_below_the_min_range(): void
    {
        /* Arrange */
        $value = -1;

        /* Act */
        $this->expectException(InvalidArgumentException::class);

        /* Assert */
        $this->processor->validateAndNormalize($value, 0, 10);
    }

    #[Test]
    public function it_rejects_a_float_masquerading_as_an_integer(): void
    {
        /* Arrange */

        /* Act */
        $this->expectException(InvalidArgumentException::class);

        /* Assert */
        $this->processor->validateAndNormalize('2.5', 0, 10);
    }

    #[Test]
    public function it_reports_a_schema_change_only_when_the_value_actually_differs(): void
    {
        /* Arrange */
        $currentPrecision = 4;

        /* Act */
        $unchangedPrecisionRequiresAlter = $this->processor->shouldAlterSchema($currentPrecision, 4);
        $changedPrecisionRequiresAlter   = $this->processor->shouldAlterSchema($currentPrecision, 6);

        /* Assert */
        self::assertFalse($unchangedPrecisionRequiresAlter);
        self::assertTrue($changedPrecisionRequiresAlter);
    }
}
