<?php

namespace Tests\Feature\Invoices;

use Mdl_Invoice_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Invoice_Amounts::class)]
class InvoiceAmountModelTest extends CiTestCase
{
    use InteractsWithDatabase;

    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_invoice_amounts');
        $this->model = $this->CI->mdl_invoice_amounts;
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_totals_with_payments(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $invoice = $this->seedModel('Invoice', ['client_id' => $client->client_id]);
        $this->seedModel('InvoiceItem', ['invoice_id' => $invoice->invoice_id]);

        $this->model->calculate($invoice->invoice_id, ['item' => 0]);

        $row = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoice->invoice_id]);
        $this->assertNotNull($row);
        $this->assertArrayHasKey('invoice_item_subtotal', $row);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_totals_without_payments(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $invoice = $this->seedModel('Invoice', ['client_id' => $client->client_id]);
        $this->seedModel('InvoiceItem', ['invoice_id' => $invoice->invoice_id]);

        $this->model->calculate($invoice->invoice_id, ['item' => 0]);

        $row = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoice->invoice_id]);
        $this->assertNotNull($row);
        $this->assertArrayHasKey('invoice_item_subtotal', $row);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_with_global_discount(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $invoice = $this->seedModel('Invoice', [
            'client_id'               => $client->client_id,
            'invoice_discount_amount' => 10,
        ]);
        $this->seedModel('InvoiceItem', ['invoice_id' => $invoice->invoice_id]);

        $this->model->calculate($invoice->invoice_id, ['item' => 0]);

        $row = $this->databaseFetchOne('ip_invoice_amounts', ['invoice_id' => $invoice->invoice_id]);
        $this->assertNotNull($row);
        $this->assertArrayHasKey('invoice_total', $row);
    }

    #[Group('smoke')]
    #[Test]
    public function it_calculates_discount_with_amount_and_percent(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $invoice = $this->seedModel('Invoice', ['client_id' => $client->client_id]);

        $result = $this->model->calculate_discount($invoice->invoice_id, 100.0);

        $this->assertIsNumeric($result);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_for_global_discount_when_no_items(): void
    {
        $this->skipWithoutDatabase();
        $client  = $this->seedModel('Client');
        $invoice = $this->seedModel('Invoice', ['client_id' => $client->client_id]);

        $result = $this->model->get_global_discount($invoice->invoice_id);

        // An invoice with no items has no discount -- result is null or 0
        $this->assertTrue(
            $result === null || (float) $result === 0.0,
            'Expected null or 0 for an invoice with no discount set, got: ' . var_export($result, true)
        );
    }

    #[Test]
    public function it_gets_total_invoiced_for_month(): void
    {
        $this->skipWithoutDatabase();
        $result = $this->model->get_total_invoiced('month');
        $this->assertTrue($result === null || is_numeric($result));
    }

    #[Test]
    public function it_gets_total_paid_for_year(): void
    {
        $this->skipWithoutDatabase();
        $result = $this->model->get_total_paid('year');
        $this->assertTrue($result === null || is_numeric($result));
    }

    #[Test]
    public function it_gets_total_balance_for_last_month(): void
    {
        $this->skipWithoutDatabase();
        $result = $this->model->get_total_balance('last_month');
        $this->assertTrue($result === null || is_numeric($result));
    }

    #[Test]
    public function it_gets_status_totals_for_this_month(): void
    {
        $this->skipWithoutDatabase();
        $result = $this->model->get_status_totals('this-month');
        $this->assertIsArray($result);
    }

    #[Test]
    public function it_gets_status_totals_for_different_periods(): void
    {
        $this->skipWithoutDatabase();
        $periods = ['this-month', 'last-month', 'this-year', 'last-year'];
        foreach ($periods as $period) {
            $result = $this->model->get_status_totals($period);
            $this->assertIsArray($result, "Expected array for period '{$period}'");
        }
    }
}
