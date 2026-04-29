<?php

namespace Tests\Feature\Core;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Reports;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Reports::class)]
#[CoversClass(Tests\Feature\Core\ReportsController::class)]
class ReportsControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = $this->seedModel('User', ['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_returns_sales_by_client_report(): void
    {
        /* Arrange */
        $client  = $this->seedModel('tmpClient');
        $invoice = $this->seedModelMany('Invoice', 3, [
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(5),
            'total'        => 500,
        ]);

        /* Act */
        /**
         * Payload:
         * {
         *     "from_date": 1,
         *     "to_date": 1,
         *     "btn_submit": true
         * }
         */
        $response = $this->post('/reports/salesByClient', [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        /* Assert */
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('500');
    }

    #[Test]
    public function it_generates_sales_by_client_report(): void
    {
        /* Arrange */
        $client = $this->seedModel('tmpClient');
        $this->seedModelMany('Invoice', 3, [
            'client_id'            => $client->client_id,
            'invoice_status_id'    => 4, // Paid
            'invoice_date_created' => now()->subDays(10),
        ]);

        /* Act */
        /**
         * Payload:
         * {
         *     "btn_submit": true,
         *     "from_date": 1,
         *     "to_date": 1
         * }
         */
        $response = $this->post('/reports/salesByClient', [
            'btn_submit' => true,
            'from_date'  => now()->subDays(30)->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
        ]);

        /* Assert */
        $response->assertSuccessful();
        $response->assertViewHas('results');
        $response->assertViewHas('from_date');
        $response->assertViewHas('to_date');
    }

    #[Test]
    public function it_displays_payment_history_report_form(): void
    {
        $response = $this->get('/reports/paymentHistory');

        $response->assertSuccessful();
        $response->assertViewIs('reports.payment_history_index');
    }

    #[Test]
    public function it_generates_payment_history_report(): void
    {
        $invoice = $this->seedModel('Invoice');
        $this->seedModelMany('Payment', 3, [
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(5),
        ]);

        /**
         * Payload:
         * {
         *     "btn_submit": true,
         *     "from_date": 1,
         *     "to_date": 1
         * }
         */
        $response = $this->post('/reports/paymentHistory', [
            'btn_submit' => true,
            'from_date'  => now()->subDays(30)->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('results');
    }

    #[Test]
    public function it_generates_invoice_aging_report(): void
    {
        /* Arrange */
        $this->seedModel('Invoice', [
            'invoice_date_due'  => now()->subDays(10),
            'invoice_status_id' => 2, // Sent
        ]);
        $this->seedModel('Invoice', [
            'invoice_date_due'  => now()->subDays(40),
            'invoice_status_id' => 2,
        ]);
        $this->seedModel('Invoice', [
            'invoice_date_due'  => now()->subDays(70),
            'invoice_status_id' => 2,
        ]);

        /* Act */
        /**
         * Payload:
         * {
         *     "btn_submit": true
         * }
         */
        $response = $this->post('/reports/invoiceAging', [
            'btn_submit' => true,
        ]);

        /* Assert */
        $response->assertSuccessful();
        $response->assertViewHas('results');
    }

    #[Test]
    public function it_returns_invoices_per_client_report(): void
    {
        /* Arrange */
        $client  = $this->seedModel('tmpClient');
        $invoice = $this->seedModel('Invoice', [
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(3),
            'total'        => 300,
        ]);

        /* Act */
        /**
         * Payload:
         * {
         *     "from_date": 1,
         *     "to_date": 1,
         *     "btn_submit": true
         * }
         */
        $response = $this->post('/reports/invoicesPerClient', [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        /* Assert */
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('300');
    }

    #[Test]
    public function it_generates_sales_by_year_report_with_filters(): void
    {
        $this->seedModelMany('Invoice', 10, [
            'invoice_date_created' => now()->subMonths(6),
            'invoice_status_id'    => 4,
        ]);

        /**
         * Payload:
         * {
         *     "btn_submit": true,
         *     "from_date": 1,
         *     "to_date": 1,
         *     "minQuantity": 0,
         *     "maxQuantity": 1000,
         *     "checkboxTax": true
         * }
         */
        $response = $this->post('/reports/salesByYear', [
            'btn_submit'  => true,
            'from_date'   => now()->subYear()->format('Y-m-d'),
            'to_date'     => now()->format('Y-m-d'),
            'minQuantity' => 0,
            'maxQuantity' => 1000,
            'checkboxTax' => true,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('results');
        $response->assertViewHas('from_date');
        $response->assertViewHas('to_date');
    }

    #[Test]
    public function it_filters_sales_report_by_quantity_range(): void
    {
        /**
         * Payload:
         * {
         *     "btn_submit": true,
         *     "from_date": 1,
         *     "to_date": 1,
         *     "minQuantity": 10,
         *     "maxQuantity": 100
         * }
         */
        $response = $this->post('/reports/salesByYear', [
            'btn_submit'  => true,
            'from_date'   => now()->subYear()->format('Y-m-d'),
            'to_date'     => now()->format('Y-m-d'),
            'minQuantity' => 10,
            'maxQuantity' => 100,
        ]);

        $response->assertSuccessful();
        $response->assertViewHas('results');
    }
}
