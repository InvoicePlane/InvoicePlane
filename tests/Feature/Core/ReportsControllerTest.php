<?php

namespace Modules\Core\Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Tests\Feature\Auth\route;

use Tests\TestCase;

class ReportsControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create(['user_type' => 1, 'user_active' => 1]);
        $this->actingAs($this->user);
    }

    #[Test]
    public function it_returns_sales_by_client_report()
    {
        // Arrange: create clients and sales data
        $client  = \Modules\Clients\Models\tmpClient::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory(3)->create([
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(5),
            'total'        => 500,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.salesByClient'), [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        // Assert: report contains client and sales data
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('500');
    }

    #[Test]
    public function it_generates_sales_by_client_report(): void
    {
        // Arrange
        $client = tmpClient::factory()->create();
        Invoice::factory()->count(3)->create([
            'client_id'            => $client->client_id,
            'invoice_status_id'    => 4, // Paid
            'invoice_date_created' => now()->subDays(10),
        ]);

        // Act
        $response = $this->post(route('reports.salesByClient'), [
            'btn_submit' => true,
            'from_date'  => now()->subDays(30)->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
        ]);

        // Assert
        $response->assertSuccessful();
        $response->assertViewHas('results');
        $response->assertViewHas('from_date');
        $response->assertViewHas('to_date');
    }

    #[Test]
    public function it_displays_payment_history_report_form(): void
    {
        $response = $this->get(route('reports.paymentHistory'));

        $response->assertSuccessful();
        $response->assertViewIs('reports.payment_history_index');
    }

    #[Test]
    public function it_generates_payment_history_report(): void
    {
        $invoice = Invoice::factory()->create();
        Payment::factory()->count(3)->create([
            'invoice_id'   => $invoice->invoice_id,
            'payment_date' => now()->subDays(5),
        ]);

        $response = $this->post(route('reports.paymentHistory'), [
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
        // Arrange: create clients and invoices
        Invoice::factory()->create([
            'invoice_date_due'  => now()->subDays(10),
            'invoice_status_id' => 2, // Sent
        ]);
        Invoice::factory()->create([
            'invoice_date_due'  => now()->subDays(40),
            'invoice_status_id' => 2,
        ]);
        Invoice::factory()->create([
            'invoice_date_due'  => now()->subDays(70),
            'invoice_status_id' => 2,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.invoiceAging'), [
            'btn_submit' => true,
        ]);

        // Assert: report contains client and invoice data
        $response->assertSuccessful();
        $response->assertViewHas('results');
    }

    #[Test]
    public function it_returns_invoices_per_client_report()
    {
        // Arrange: create clients and invoices
        $client  = \Modules\Clients\Models\tmpClient::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id'    => $client->id,
            'invoice_date' => now()->subDays(3),
            'total'        => 300,
        ]);

        // Act: submit the report form
        $response = $this->post(route('reports.invoicesPerClient'), [
            'from_date'  => now()->subMonth()->format('Y-m-d'),
            'to_date'    => now()->format('Y-m-d'),
            'btn_submit' => true,
        ]);

        // Assert: report contains client and invoice data
        $response->assertStatus(200);
        $response->assertSee($client->name);
        $response->assertSee('300');
    }

    #[Test]
    public function it_generates_sales_by_year_report_with_filters(): void
    {
        Invoice::factory()->count(10)->create([
            'invoice_date_created' => now()->subMonths(6),
            'invoice_status_id'    => 4,
        ]);

        $response = $this->post(route('reports.salesByYear'), [
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
        $response = $this->post(route('reports.salesByYear'), [
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
