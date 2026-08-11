<?php

namespace Tests\Unit;

use Error;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class InvoiceHelperTest extends TestCase
{
    protected function setUp(): void
    {
        require_once dirname(__DIR__, 2) . '/application/helpers/invoice_helper.php';
    }

    #[Test]
    public function it_calculates_recursive_mod10_checksums(): void
    {
        /* Arrange */
        $inputs = ['1234567890', '0000000000', '12345'];

        /* Act */
        $checksums = array_map('invoice_recMod10', $inputs);

        /* Assert */
        self::assertSame([3, 0, 7], $checksums);
    }

    #[Test]
    public function it_builds_a_valid_isr_code_line(): void
    {
        /* Arrange */
        $slipType = '11';

        /* Act */
        $codeLine = invoice_genCodeline($slipType, '12.50', '123456', '12-345678-9');

        /* Assert */
        self::assertStringStartsWith('11', $codeLine);
        self::assertStringEndsWith('>123456+ 123456789>', $codeLine);
        self::assertMatchesRegularExpression('/\A\d{13}>123456\+ 123456789>\z/', $codeLine);
    }

    #[Test]
    public function it_rejects_an_invalid_subscriber_number(): void
    {
        /* Arrange */
        $subscriberNumber = 'invalid';

        /* Act */
        try {
            invoice_genCodeline('11', '12.50', '123456', $subscriberNumber);
            $exception = null;
        } catch (Error $error) {
            $exception = $error;
        }

        /* Assert */
        self::assertInstanceOf(Error::class, $exception);
        self::assertSame('Invalid subscriber number', $exception->getMessage());
    }

    #[Test]
    public function it_rejects_an_amount_above_the_isr_limit(): void
    {
        /* Arrange */
        $amount = '100000000.00';

        /* Act */
        try {
            invoice_genCodeline('11', $amount, '123456', '12-345678-9');
            $exception = null;
        } catch (Error $error) {
            $exception = $error;
        }

        /* Assert */
        self::assertInstanceOf(Error::class, $exception);
        self::assertSame('Invalid amount', $exception->getMessage());
    }
}
