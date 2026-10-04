<?php

declare(strict_types=1);

namespace Tests\Unit\SupplierInvoices;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use SupplierInvoiceAccess;

require_once dirname(__DIR__, 3) . '/application/modules/supplier_invoices/libraries/SupplierInvoiceAccess.php';

final class SupplierInvoiceAccessTest extends TestCase
{
    #[Test]
    public function it_allows_supplier_invoice_access_only_to_administrators(): void
    {
        $access = new SupplierInvoiceAccess();

        self::assertTrue($access->canRead(SupplierInvoiceAccess::ADMINISTRATOR));
        self::assertTrue($access->canManagePayments(SupplierInvoiceAccess::ADMINISTRATOR));
        self::assertFalse($access->canRead(2));
        self::assertFalse($access->canManageAttachments(2));
    }
}
