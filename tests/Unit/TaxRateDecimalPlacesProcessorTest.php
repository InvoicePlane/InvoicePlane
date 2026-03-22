<?php

namespace Tests\Unit;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TaxRateDecimalPlacesProcessor;

if (!defined('BASEPATH')) {
    define('BASEPATH', __DIR__);
}

require_once __DIR__ . '/../../application/modules/settings/libraries/TaxRateDecimalPlacesProcessor.php';

class TaxRateDecimalPlacesProcessorTest extends TestCase
{
    private const MIN_DECIMALS = 2;
    private const MAX_DECIMALS = 3;

    #[Test]
    public function it_accepts_valid_decimal_places(): void
    {
        // Arrange
        $processor = new TaxRateDecimalPlacesProcessor();

        // Act
        $result = $processor->validateAndNormalize('3', self::MIN_DECIMALS, self::MAX_DECIMALS);

        // Assert
        $this->assertSame(3, $result);
    }

    #[Test]
    public function it_rejects_invalid_decimal_places(): void
    {
        // Arrange
        $processor = new TaxRateDecimalPlacesProcessor();

        // Assert
        $this->expectException(InvalidArgumentException::class);

        // Act
        $processor->validateAndNormalize('99; DROP TABLE ip_tax_rates', self::MIN_DECIMALS, self::MAX_DECIMALS);
    }

    #[Test]
    public function it_detects_when_schema_change_is_needed(): void
    {
        // Arrange
        $processor = new TaxRateDecimalPlacesProcessor();

        // Act
        $shouldAlter = $processor->shouldAlterSchema(2, 3);

        // Assert
        $this->assertTrue($shouldAlter);
    }

    #[Test]
    public function it_detects_when_schema_change_is_not_needed(): void
    {
        // Arrange
        $processor = new TaxRateDecimalPlacesProcessor();

        // Act
        $shouldAlter = $processor->shouldAlterSchema(2, 2);

        // Assert
        $this->assertFalse($shouldAlter);
    }
}
