<?php

namespace Tests\Feature\SupplierInvoices;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Supplier_invoices;
use Tests\AbstractTestCase;
use Tests\Concerns\PerformsCsrfProtectedRequests;

#[Group('supplier_invoices')]
#[CoversClass(Supplier_invoices::class)]
final class SupplierInvoicesControllerTest extends AbstractTestCase
{
    use PerformsCsrfProtectedRequests;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    public function it_lists_and_filters_supplier_invoices(): void
    {
        /* Arrange */
        $supplierId = $this->seedSupplier('Acme Supplies');
        $this->seedSupplierInvoice($supplierId, 'ACME-001', '100.00');
        $otherSupplierId = $this->seedSupplier('Other Supplies');
        $this->seedSupplierInvoice($otherSupplierId, 'OTHER-001', '25.00');

        /* Act */
        $response = $this->get('/supplier_invoices', ['q' => 'Acme']);

        /* Assert */
        $this->assertResponseOk($response);
        $this->assertResponseBodyContains($response, 'ACME-001');
        $this->assertResponseBodyNotContains($response, 'OTHER-001');
    }

    #[Test]
    public function it_records_a_payment_and_marks_a_fully_paid_invoice(): void
    {
        /* Arrange */
        $supplierId = $this->seedSupplier('Payment Supplier');
        $invoiceId = $this->seedSupplierInvoice($supplierId, 'PAY-001', '100.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/payment/' . $invoiceId, [
            'payment_date' => '2026-10-04',
            'amount' => '100.00',
            'payment_method' => 'Bank transfer',
            'reference' => 'PAYMENT-001',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices/view/' . $invoiceId);
        $this->assertDatabaseHas('ip_supplier_invoices', [
            'supplier_invoice_id' => $invoiceId,
            'amount_paid' => '100.000000',
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('ip_supplier_invoice_payments', [
            'supplier_invoice_id' => $invoiceId,
            'reference' => 'PAYMENT-001',
        ]);
    }

    #[Test]
    public function it_archives_and_restores_an_invoice_without_deleting_it(): void
    {
        /* Arrange */
        $supplierId = $this->seedSupplier('Archive Supplier');
        $invoiceId = $this->seedSupplierInvoice($supplierId, 'ARCH-001', '50.00');
        $this->enableCsrfProtection();

        /* Act - Archive */
        $archive = $this->postWithValidCsrfToken('/supplier_invoices/archive/' . $invoiceId);

        /* Assert - Archive */
        $this->assertResponseRedirectsToRoute($archive, 'supplier_invoices/view/' . $invoiceId);
        $archived = $this->databaseFetchOne('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId]);
        self::assertNotNull($archived);
        self::assertNotNull($archived['archived_at']);

        /* Act - Restore */
        $restore = $this->postWithValidCsrfToken('/supplier_invoices/restore/' . $invoiceId);

        /* Assert - Restore */
        $this->assertResponseRedirectsToRoute($restore, 'supplier_invoices/view/' . $invoiceId);
        $restored = $this->databaseFetchOne('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId]);
        self::assertNotNull($restored);
        self::assertNull($restored['archived_at']);
    }

    #[Test]
    public function it_redirects_unauthenticated_users_to_login(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/supplier_invoices');

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'sessions/login');
    }

    private function seedSupplier(string $name): int
    {
        return $this->databaseInsert('ip_suppliers', [
            'supplier_name' => $name,
            'supplier_active' => 1,
            'supplier_date_created' => '2026-10-04',
            'supplier_date_modified' => '2026-10-04',
        ]);
    }

    private function seedSupplierInvoice(int $supplierId, string $number, string $total): int
    {
        return $this->databaseInsert('ip_supplier_invoices', [
            'supplier_id' => $supplierId,
            'incoming_response_id' => null,
            'supplier_invoice_number' => $number,
            'supplier_invoice_date' => '2026-10-04',
            'currency_code' => 'EUR',
            'subtotal' => $total,
            'tax_total' => '0.00',
            'total' => $total,
            'amount_paid' => '0.00',
            'status' => 'received',
            'created_at' => '2026-10-04 12:00:00',
            'updated_at' => '2026-10-04 12:00:00',
        ]);
    }
}
