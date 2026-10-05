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

    #[Test]
    public function it_changes_an_invoice_status_and_records_the_history(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Status Supplier'), 'STA-001', '80.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/status/' . $invoiceId, [
            'status'  => 'approved',
            'comment' => 'Checked against the purchase order',
        ]);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices/view/' . $invoiceId);
        $this->assertDatabaseHas('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId, 'status' => 'approved']);
        $this->assertDatabaseHas('ip_supplier_invoice_status_history', [
            'supplier_invoice_id' => $invoiceId,
            'old_status'          => 'received',
            'new_status'          => 'approved',
            'comment'             => 'Checked against the purchase order',
        ]);
    }

    #[Test]
    public function it_rejects_an_unknown_status_without_changing_the_invoice(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Bogus Status Supplier'), 'STA-002', '80.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/status/' . $invoiceId, ['status' => 'bogus']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices/view/' . $invoiceId);
        $this->assertDatabaseHas('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId, 'status' => 'received']);
        $this->assertDatabaseMissing('ip_supplier_invoice_status_history', ['supplier_invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_refuses_a_status_change_without_a_csrf_token(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('No Token Supplier'), 'STA-003', '80.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithoutCsrfToken('/supplier_invoices/status/' . $invoiceId, ['status' => 'approved']);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less status change must not reach the controller.');
        $this->assertDatabaseHas('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId, 'status' => 'received']);
    }

    #[Test]
    public function it_registers_no_attachment_when_the_upload_carries_no_file(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Attachment Supplier'), 'ATT-001', '40.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/upload_attachment/' . $invoiceId);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices/view/' . $invoiceId);
        $this->assertDatabaseMissing('ip_supplier_invoice_attachments', ['supplier_invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_refuses_an_attachment_upload_without_a_csrf_token(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Attachment No Token'), 'ATT-002', '40.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithoutCsrfToken('/supplier_invoices/upload_attachment/' . $invoiceId);

        /* Assert */
        self::assertFalse($response->isRedirect(), 'A token-less upload must not reach the controller.');
        $this->assertDatabaseMissing('ip_supplier_invoice_attachments', ['supplier_invoice_id' => $invoiceId]);
    }

    // -------------------------------------------------------------------------
    // payments — money rules
    // -------------------------------------------------------------------------

    #[Test]
    public function it_records_a_partial_payment_without_settling_the_invoice(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Partial Supplier'), 'PART-001', '100.00');
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/supplier_invoices/payment/' . $invoiceId, ['payment_date' => '2026-10-04', 'amount' => '40,50']);

        /* Assert: comma decimals are normalised, balance moves, status does not */
        $this->assertDatabaseHas('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId, 'amount_paid' => '40.500000', 'status' => 'received']);
        $this->assertDatabaseHas('ip_supplier_invoice_payments', ['supplier_invoice_id' => $invoiceId, 'amount' => '40.500000']);
        $this->assertDatabaseMissing('ip_supplier_invoice_status_history', ['supplier_invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_settles_the_invoice_when_partial_payments_add_up_to_the_total(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Two Step Supplier'), 'PART-002', '100.00');
        $this->enableCsrfProtection();

        /* Act */
        $this->postWithValidCsrfToken('/supplier_invoices/payment/' . $invoiceId, ['payment_date' => '2026-10-04', 'amount' => '60']);
        $this->postWithValidCsrfToken('/supplier_invoices/payment/' . $invoiceId, ['payment_date' => '2026-10-05', 'amount' => '40']);

        /* Assert */
        $this->assertDatabaseHas('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId, 'amount_paid' => '100.000000', 'status' => 'paid']);
        $this->assertDatabaseCount('ip_supplier_invoice_payments', 2, ['supplier_invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_supplier_invoice_status_history', ['supplier_invoice_id' => $invoiceId, 'new_status' => 'paid']);
    }

    /** @return array<string, array{0: array<string,string>}> */
    public static function invalidPayments(): array
    {
        return [
            'overpayment'       => [['payment_date' => '2026-10-04', 'amount' => '100.01']],
            'zero amount'       => [['payment_date' => '2026-10-04', 'amount' => '0']],
            'negative amount'   => [['payment_date' => '2026-10-04', 'amount' => '-5']],
            'non-numeric'       => [['payment_date' => '2026-10-04', 'amount' => 'ten euros']],
            'empty amount'      => [['payment_date' => '2026-10-04', 'amount' => '']],
            'missing date'      => [['amount' => '10']],
            'malformed date'    => [['payment_date' => '04/10/2026', 'amount' => '10']],
        ];
    }

    /** @param array<string,string> $post */
    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidPayments')]
    public function it_refuses_an_invalid_payment_and_leaves_the_books_untouched(array $post): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Strict Supplier'), 'BAD-' . random_int(100, 999), '100.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/payment/' . $invoiceId, $post);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices/view/' . $invoiceId);
        $this->assertDatabaseMissing('ip_supplier_invoice_payments', ['supplier_invoice_id' => $invoiceId]);
        $this->assertDatabaseHas('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId, 'amount_paid' => '0.000000', 'status' => 'received']);
    }

    #[Test]
    public function it_returns_404_for_a_payment_on_an_unknown_invoice(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/payment/999999', ['payment_date' => '2026-10-04', 'amount' => '10']);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertDatabaseCount('ip_supplier_invoice_payments', 0);
    }

    // -------------------------------------------------------------------------
    // invoice form
    // -------------------------------------------------------------------------

    #[Test]
    public function it_creates_a_supplier_invoice_with_its_line_items(): void
    {
        /* Arrange */
        $supplierId = $this->seedSupplier('Form Supplier');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/form', $this->invoiceFormPost($supplierId, [
            'supplier_invoice_number' => 'FORM-001',
            'total'                   => '121.00',
            'item_name'               => ['Consulting', 'Travel'],
            'quantity'                => ['2', '1'],
            'unit_price'              => ['40', '41'],
        ]));

        /* Assert */
        $saved = $this->databaseFetchOne('ip_supplier_invoices', ['supplier_invoice_number' => 'FORM-001']);
        self::assertNotNull($saved, 'The invoice must be persisted.');
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices/view/' . $saved['supplier_invoice_id']);
        self::assertSame((string) $supplierId, (string) $saved['supplier_id']);
        $this->assertDatabaseHas('ip_supplier_invoice_items', ['supplier_invoice_id' => $saved['supplier_invoice_id'], 'item_name' => 'Consulting']);
        $this->assertDatabaseHas('ip_supplier_invoice_items', ['supplier_invoice_id' => $saved['supplier_invoice_id'], 'item_name' => 'Travel']);
    }

    /** @return array<string, array{0: array<string,mixed>}> */
    public static function invalidInvoiceForms(): array
    {
        return [
            'no supplier'        => [['supplier_id' => 0]],
            'unknown supplier'   => [['supplier_id' => 999999]],
            'no invoice number'  => [['supplier_invoice_number' => '']],
            'no invoice date'    => [['supplier_invoice_date' => '']],
        ];
    }

    /** @param array<string,mixed> $override */
    #[Test]
    #[\PHPUnit\Framework\Attributes\DataProvider('invalidInvoiceForms')]
    public function it_rejects_an_incomplete_invoice_form_and_saves_nothing(array $override): void
    {
        /* Arrange */
        $supplierId = $this->seedSupplier('Validating Supplier');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/form', array_merge(
            $this->invoiceFormPost($supplierId, ['supplier_invoice_number' => 'INVALID-001']),
            $override,
        ));

        /* Assert: the form is re-rendered with errors; no invoice row exists */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseCount('ip_supplier_invoices', 0);
    }

    #[Test]
    public function it_rejects_a_duplicate_invoice_number_for_the_same_supplier_but_allows_it_for_another(): void
    {
        /* Arrange */
        $supplierA = $this->seedSupplier('Supplier A');
        $supplierB = $this->seedSupplier('Supplier B');
        $this->seedSupplierInvoice($supplierA, 'DUP-001', '10.00');
        $this->enableCsrfProtection();

        /* Act */
        $duplicate = $this->postWithValidCsrfToken('/supplier_invoices/form', $this->invoiceFormPost($supplierA, ['supplier_invoice_number' => 'DUP-001']));
        $this->postWithValidCsrfToken('/supplier_invoices/form', $this->invoiceFormPost($supplierB, ['supplier_invoice_number' => 'DUP-001']));

        /* Assert: the user is told why (validation message, not the generic save failure from a DB index) */
        $this->assertResponseBodyContains($duplicate, 'already exists for the selected supplier');
        $this->assertDatabaseCount('ip_supplier_invoices', 1, ['supplier_id' => $supplierA]);
        $this->assertDatabaseCount('ip_supplier_invoices', 1, ['supplier_id' => $supplierB]);
    }

    #[Test]
    public function it_edits_an_existing_invoice_in_place(): void
    {
        /* Arrange */
        $supplierId = $this->seedSupplier('Edit Supplier');
        $invoiceId  = $this->seedSupplierInvoice($supplierId, 'EDIT-001', '50.00');
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/form/' . $invoiceId, $this->invoiceFormPost($supplierId, [
            'supplier_invoice_number' => 'EDIT-001', 'notes' => 'amended', 'total' => '75.00',
        ]));

        /* Assert: same row updated, no second invoice created */
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices/view/' . $invoiceId);
        $this->assertDatabaseHas('ip_supplier_invoices', ['supplier_invoice_id' => $invoiceId, 'notes' => 'amended', 'total' => '75.000000']);
        $this->assertDatabaseCount('ip_supplier_invoices', 1);
    }

    #[Test]
    public function it_returns_404_when_editing_an_unknown_invoice(): void
    {
        /* Act */
        $response = $this->get('/supplier_invoices/form/999999');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    #[Test]
    public function it_returns_to_the_list_when_the_invoice_form_is_cancelled(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/form', ['btn_cancel' => '1']);

        /* Assert */
        $this->assertResponseRedirectsToRoute($response, 'supplier_invoices');
        $this->assertDatabaseCount('ip_supplier_invoices', 0);
    }

    // -------------------------------------------------------------------------
    // suppliers
    // -------------------------------------------------------------------------

    #[Test]
    public function it_lists_suppliers(): void
    {
        /* Arrange */
        $this->seedSupplier('Listed Supplier One');
        $this->seedSupplier('Listed Supplier Two');

        /* Act */
        $response = $this->get('/supplier_invoices/suppliers');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, 'Listed Supplier One');
        $this->assertResponseBodyContains($response, 'Listed Supplier Two');
    }

    #[Test]
    public function it_creates_and_then_updates_a_supplier(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();

        /* Act */
        $created = $this->postWithValidCsrfToken('/supplier_invoices/supplier_form', [
            'supplier_name' => 'Acme Components', 'supplier_vat_id' => 'NL123456789B01', 'supplier_email' => 'ap@acme.test', 'supplier_active' => '1',
        ]);
        $row = $this->databaseFetchOne('ip_suppliers', ['supplier_name' => 'Acme Components']);

        /* Assert: created */
        $this->assertResponseRedirectsToRoute($created, 'supplier_invoices/suppliers');
        self::assertNotNull($row);
        self::assertSame('NL123456789B01', $row['supplier_vat_id']);

        /* Act: update */
        $this->postWithValidCsrfToken('/supplier_invoices/supplier_form/' . $row['supplier_id'], [
            'supplier_name' => 'Acme Components BV', 'supplier_vat_id' => 'NL123456789B01', 'supplier_active' => '0',
        ]);

        /* Assert: updated in place and deactivated */
        $this->assertDatabaseHas('ip_suppliers', ['supplier_id' => $row['supplier_id'], 'supplier_name' => 'Acme Components BV', 'supplier_active' => 0]);
        $this->assertDatabaseCount('ip_suppliers', 1);
    }

    #[Test]
    public function it_rejects_a_supplier_without_a_name(): void
    {
        /* Arrange */
        $this->enableCsrfProtection();

        /* Act */
        $response = $this->postWithValidCsrfToken('/supplier_invoices/supplier_form', ['supplier_name' => '   ', 'supplier_email' => 'x@y.test']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertDatabaseCount('ip_suppliers', 0);
    }

    #[Test]
    public function it_returns_404_for_an_unknown_supplier(): void
    {
        /* Act */
        $response = $this->get('/supplier_invoices/supplier_form/999999');

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
    }

    // -------------------------------------------------------------------------
    // downloads
    // -------------------------------------------------------------------------

    #[Test]
    public function it_streams_a_stored_attachment_with_safe_headers_and_exact_bytes(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('Download Supplier'), 'DL-001', '10.00');
        $relative  = 'supplier-invoices/' . $invoiceId . '/test-download-' . bin2hex(random_bytes(4)) . '.pdf';
        $absolute  = rtrim(UPLOADS_ARCHIVE_FOLDER, '/') . '/' . $relative;
        @mkdir(dirname($absolute), 0750, true);
        file_put_contents($absolute, '%PDF-1.4 attachment-bytes');
        $attachmentId = $this->databaseInsert('ip_supplier_invoice_attachments', [
            'supplier_invoice_id' => $invoiceId, 'file_name' => 'scan.pdf', 'storage_path' => $relative,
            'mime_type' => 'application/pdf', 'file_size' => 25, 'sha256' => hash('sha256', 'x' . $relative), 'created_at' => date('Y-m-d H:i:s'),
        ]);

        try {
            /* Act */
            $response = $this->get('/supplier_invoices/download_attachment/' . $attachmentId);

            /* Assert */
            $this->assertResponseStatusCode($response, 200);
            self::assertSame('%PDF-1.4 attachment-bytes', $response->body());
        } finally {
            @unlink($absolute);
        }
    }

    #[Test]
    public function it_refuses_an_attachment_whose_stored_path_escapes_the_archive(): void
    {
        /* Arrange: a tampered row pointing at the application config */
        $invoiceId    = $this->seedSupplierInvoice($this->seedSupplier('Tampered Supplier'), 'DL-002', '10.00');
        $attachmentId = $this->databaseInsert('ip_supplier_invoice_attachments', [
            'supplier_invoice_id' => $invoiceId, 'file_name' => 'steal.txt', 'storage_path' => '../../ipconfig.php',
            'mime_type' => 'text/plain', 'file_size' => 1, 'sha256' => hash('sha256', 'tamper'), 'created_at' => date('Y-m-d H:i:s'),
        ]);

        /* Act */
        $response = $this->get('/supplier_invoices/download_attachment/' . $attachmentId);

        /* Assert */
        $this->assertResponseStatusCode($response, 404);
        $this->assertResponseBodyNotContains($response, 'ENCRYPTION_KEY');
    }

    #[Test]
    public function it_returns_404_for_an_unknown_attachment_and_for_an_invoice_without_a_document(): void
    {
        /* Arrange */
        $invoiceId = $this->seedSupplierInvoice($this->seedSupplier('No Document Supplier'), 'DL-003', '10.00');

        /* Act */
        $unknownAttachment = $this->get('/supplier_invoices/download_attachment/999999');
        $noDocument        = $this->get('/supplier_invoices/download_document/' . $invoiceId);
        $unknownInvoice    = $this->get('/supplier_invoices/download_document/999999');

        /* Assert */
        $this->assertResponseStatusCode($unknownAttachment, 404);
        $this->assertResponseStatusCode($noDocument, 404);
        $this->assertResponseStatusCode($unknownInvoice, 404);
    }

    /**
     * @param array<string,mixed> $override
     *
     * @return array<string,mixed>
     */
    private function invoiceFormPost(int $supplierId, array $override = []): array
    {
        return array_merge([
            'supplier_id' => $supplierId, 'supplier_invoice_number' => 'F-' . random_int(1000, 9999),
            'supplier_invoice_date' => '2026-10-04', 'supplier_due_date' => '2026-11-04', 'currency_code' => 'EUR',
            'subtotal' => '100.00', 'tax_total' => '21.00', 'total' => '121.00', 'notes' => '',
        ], $override);
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
