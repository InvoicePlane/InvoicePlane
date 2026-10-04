<?php

declare(strict_types=1);

namespace Tests\Unit\SupplierInvoices;

use FrenchSupplierInvoiceDataValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 3) . '/application/modules/supplier_invoices/libraries/FrenchSupplierInvoiceDataValidator.php';

final class FrenchSupplierInvoiceDataValidatorTest extends TestCase
{
    #[Test]
    public function it_accepts_a_consistent_french_supplier_invoice(): void
    {
        $errors = (new FrenchSupplierInvoiceDataValidator())->validate([
            'supplier' => [
                'supplier_name' => 'French Supplier',
                'supplier_address_1' => '1 Rue de Paris',
                'supplier_country' => 'FR',
                'supplier_tax_code' => '123456789',
                'supplier_vat_id' => 'FR32123456789',
            ],
            'invoice' => [
                'supplier_invoice_number' => 'INV-2026/001',
                'supplier_invoice_date' => '2026-10-04',
                'currency_code' => 'EUR',
                'subtotal' => 100,
                'tax_total' => 20,
                'total' => 120,
            ],
            'items' => [['item_name' => 'Consulting']],
        ]);

        self::assertSame([], $errors);
    }

    #[Test]
    public function it_rejects_invalid_french_identifiers_and_totals(): void
    {
        $errors = (new FrenchSupplierInvoiceDataValidator())->validate([
            'supplier' => [
                'supplier_country' => 'FR',
                'supplier_tax_code' => '123',
                'supplier_vat_id' => 'FR00123456789',
            ],
            'invoice' => [
                'supplier_invoice_number' => 'INV 001',
                'supplier_invoice_date' => '2026-02-30',
                'currency_code' => 'EURO',
                'subtotal' => 100,
                'tax_total' => 20,
                'total' => 125,
            ],
            'items' => [],
        ]);

        self::assertContains('France: supplier SIREN must contain exactly 9 digits.', $errors);
        self::assertContains('France: supplier VAT ID key does not match the SIREN.', $errors);
        self::assertContains('France: invoice number must use only letters, digits, +, -, _, or /.', $errors);
        self::assertContains('France: invoice date must be a valid ISO date.', $errors);
        self::assertContains('France: invoice currency must be a three-letter ISO 4217 code.', $errors);
        self::assertContains('France: at least one invoice line is required for structured supplier invoice import.', $errors);
        self::assertContains('France: invoice total must equal subtotal plus VAT within two cents.', $errors);
    }

    #[Test]
    public function it_does_not_apply_french_rules_to_foreign_suppliers(): void
    {
        $errors = (new FrenchSupplierInvoiceDataValidator())->validate([
            'supplier' => ['supplier_country' => 'DE'],
            'invoice' => [],
            'items' => [],
        ]);

        self::assertSame([], $errors);
    }
}
