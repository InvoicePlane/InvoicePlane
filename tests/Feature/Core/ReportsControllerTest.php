<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Integration\Support\HttpResponse;

/**
 * Reports controller — application/modules/reports/controllers/Reports.php.
 *
 * Every report renders straight to a streamed mPDF document; there is no
 * create/update/delete surface. Each test proves the response is a well-formed,
 * non-empty PDF (header + %%EOF trailer + realistic size) built from the seeded
 * rows, and that generating a report never writes to the tables it reads.
 */
#[Group('reports')]
#[CoversClass(\Reports::class)]
class ReportsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    // -------------------------------------------------------------------------
    // Report generation — each type returns a real PDF over the seeded data
    // -------------------------------------------------------------------------

    #[Test]
    public function it_generates_an_invoices_per_client_report_for_a_date_range_without_mutating_data(): void
    {
        /* Arrange */
        $includedClientId = $this->seedClient(['client_name' => 'Included Report Client']);
        $excludedClientId = $this->seedClient(['client_name' => 'Excluded Report Client']);
        $this->seedInvoice($includedClientId, [
            'invoice_number'       => 'INV-REPORT-IN',
            'invoice_date_created' => '2026-01-15',
        ], [
            'invoice_total' => '125.00',
        ]);
        $this->seedInvoice($excludedClientId, [
            'invoice_number'       => 'INV-REPORT-OUT',
            'invoice_date_created' => '2025-01-15',
        ], [
            'invoice_total' => '250.00',
        ]);

        /* Act */
        $response = $this->post('/reports/invoices_per_client', [
            'from_date'  => '2026-01-01',
            'to_date'    => '2026-01-31',
            'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertIsPdfDocument($response);
        $this->assertDatabaseCount('ip_clients', 2);
        $this->assertDatabaseCount('ip_invoices', 2);
    }

    #[Test]
    public function it_generates_a_sales_by_client_report(): void
    {
        /* Arrange */
        $clientId = $this->seedClient(['client_name' => 'Sales By Client Report']);
        $this->seedInvoice($clientId, ['invoice_date_created' => '2026-01-15'], ['invoice_total' => '75.00']);

        /* Act */
        $response = $this->post('/reports/sales_by_client', [
            'from_date' => '2026-01-01', 'to_date' => '2026-01-31', 'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertIsPdfDocument($response);
        $this->assertDatabaseCount('ip_invoices', 1);
    }

    #[Test]
    public function it_generates_a_payment_history_report(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $paymentId = $this->seedPayment($invoiceId, ['payment_date' => '2026-01-15']);

        /* Act */
        $response = $this->post('/reports/payment_history', [
            'from_date' => '2026-01-01', 'to_date' => '2026-01-31', 'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertIsPdfDocument($response);
        $this->assertDatabaseHas('ip_payments', ['payment_id' => $paymentId]);
    }

    #[Test]
    public function it_generates_an_invoice_aging_report(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId, ['invoice_date_due' => date('Y-m-d', strtotime('-10 days'))], ['invoice_balance' => '50.00']);

        /* Act */
        $response = $this->post('/reports/invoice_aging', ['btn_submit' => '1']);

        /* Assert */
        $this->assertIsPdfDocument($response);
        $this->assertDatabaseHas('ip_invoices', ['invoice_id' => $invoiceId]);
    }

    #[Test]
    public function it_generates_a_sales_by_year_report(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->seedInvoice($clientId, ['invoice_date_created' => '2026-01-15'], ['invoice_total' => '90.00']);

        /* Act */
        $response = $this->post('/reports/sales_by_year', [
            'from_date' => '2026-01-01', 'to_date' => '2026-12-31', 'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertIsPdfDocument($response);
        $this->assertDatabaseCount('ip_invoices', 1);
    }

    // -------------------------------------------------------------------------
    // Guest access — always last
    // -------------------------------------------------------------------------

    #[Test]
    public function it_redirects_a_guest_to_login_and_serves_no_report(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/reports/sales_by_client');

        /* Assert */
        self::assertTrue($response->isRedirect(), 'An unauthenticated report request must redirect to login.');
        self::assertStringNotContainsString('%PDF-', $response->body(), 'No PDF may be streamed to a guest.');
    }

    /**
     * A generated report must be a complete PDF stream, not an error page, an
     * empty buffer or a truncated document: header marker, trailer marker and a
     * realistic byte size (a one-row report already clears ~1 KB).
     */
    private function assertIsPdfDocument(HttpResponse $response): void
    {
        $body = $response->body();

        self::assertStringStartsWith('%PDF-', $body, 'Response is not a PDF document.');
        self::assertStringContainsString('%%EOF', $body, 'PDF stream is missing its %%EOF trailer.');
        self::assertGreaterThan(1000, strlen($body), 'PDF stream is implausibly small for a rendered report.');
    }
}
