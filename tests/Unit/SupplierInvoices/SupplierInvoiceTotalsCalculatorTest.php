<?php

declare(strict_types=1);

namespace Tests\Unit\SupplierInvoices;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SupplierInvoiceTotalsCalculator;

require_once dirname(__DIR__, 3) . '/application/modules/supplier_invoices/libraries/SupplierInvoiceTotalsCalculator.php';

final class SupplierInvoiceTotalsCalculatorTest extends TestCase
{
    #[Test]
    public function it_calculates_line_taxes_and_invoice_totals(): void
    {
        $result = (new SupplierInvoiceTotalsCalculator())->calculate([
            ['item_name' => 'Consulting', 'quantity' => '2', 'unit_price' => '100', 'tax_rate' => '20'],
            ['item_name' => 'Hosting', 'quantity' => '1', 'unit_price' => '50', 'tax_rate' => '10'],
        ]);

        self::assertSame(250.0, $result['subtotal']);
        self::assertSame(45.0, $result['tax_total']);
        self::assertSame(295.0, $result['total']);
        self::assertSame(40.0, $result['items'][0]['tax_total']);
    }

    #[Test]
    public function it_ignores_empty_lines_and_rounds_money_values(): void
    {
        $result = (new SupplierInvoiceTotalsCalculator())->calculate([
            ['item_name' => '', 'quantity' => 1, 'unit_price' => 10, 'tax_rate' => 20],
            ['item_name' => 'Rounded', 'quantity' => 3, 'unit_price' => 0.333, 'tax_rate' => 20],
        ]);

        self::assertCount(1, $result['items']);
        self::assertSame(1.0, $result['subtotal']);
        self::assertSame(0.2, $result['tax_total']);
        self::assertSame(1.2, $result['total']);
    }
}
