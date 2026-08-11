<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class ReportsControllerTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsAdmin();
    }

    #[Test]
    #[Group('smoke')]
    public function it_returns_a_successful_response_or_redirect(): void
    {
        /* Arrange */
        $this->seedClient(['client_name' => 'Report Test Client']);

        /* Act */
        $response = $this->get('/reports/sales_by_client');

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseBodyContains($response, '<form');
    }

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
        $this->assertResponseStatusCode($response, 200);
        $this->assertResponseHasNoPhpErrors($response);
        self::assertStringStartsWith('%PDF-', $response->body());
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
        $this->assertResponseStatusCode($response, 200);
        self::assertStringStartsWith('%PDF-', $response->body());
    }

    #[Test]
    public function it_generates_a_payment_history_report(): void
    {
        /* Arrange */
        $clientId  = $this->seedClient();
        $invoiceId = $this->seedInvoice($clientId);
        $this->seedPayment($invoiceId, ['payment_date' => '2026-01-15']);

        /* Act */
        $response = $this->post('/reports/payment_history', [
            'from_date' => '2026-01-01', 'to_date' => '2026-01-31', 'btn_submit' => '1',
        ]);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringStartsWith('%PDF-', $response->body());
    }

    #[Test]
    public function it_generates_an_invoice_aging_report(): void
    {
        /* Arrange */
        $clientId = $this->seedClient();
        $this->seedInvoice($clientId, ['invoice_date_due' => date('Y-m-d', strtotime('-10 days'))], ['invoice_balance' => '50.00']);

        /* Act */
        $response = $this->post('/reports/invoice_aging', ['btn_submit' => '1']);

        /* Assert */
        $this->assertResponseStatusCode($response, 200);
        self::assertStringStartsWith('%PDF-', $response->body());
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
        $this->assertResponseStatusCode($response, 200);
        self::assertStringStartsWith('%PDF-', $response->body());
    }

    #[Test]
    public function it_redirects_a_guest_to_login_for_reports(): void
    {
        /* Arrange */
        $this->actingAsGuest();

        /* Act */
        $response = $this->get('/reports/sales_by_client');

        /* Assert */
        self::assertTrue($response->isRedirect());
    }
}
