<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoices;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoices::class)]
class InvoicesModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_invoices');
        $this->model = $this->CI->mdl_invoices;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_invoices', $this->model->table);
    }

    #[Test]
    public function it_has_by_client_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'by_client'));
    }

    #[Test]
    public function it_has_get_payments_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_payments'));
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_draft_returns_self(): void
    {
        $this->assertInstanceOf(\Mdl_Invoices::class, $this->model->is_draft());
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_sent_returns_self(): void
    {
        $this->assertInstanceOf(\Mdl_Invoices::class, $this->model->is_sent());
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_paid_returns_self(): void
    {
        $this->assertInstanceOf(\Mdl_Invoices::class, $this->model->is_paid());
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_invoice_and_retrieves_it(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id      = $this->seedClient();
        $invoice_number = 'INV-TEST-' . uniqid();
        $invoice_id     = $this->seedInvoice($client_id, ['invoice_number' => $invoice_number]);

        /* Act */
        $row = $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoice_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals($invoice_number, $row['invoice_number']);
        $this->assertEquals(1, (int) $row['invoice_status_id']);

        /* Cleanup */
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_attaches_payments_to_invoice_object(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedClient();
        $invoice_id = $this->seedInvoice($client_id, ['invoice_status_id' => 4]);
        $this->seedPayment($invoice_id, ['payment_amount' => '100.00']);

        $invoice = (object) $this->databaseFetchOne('ip_invoices', ['invoice_id' => $invoice_id]);

        /* Act */
        $result = $this->model->get_payments($invoice);

        /* Assert */
        $this->assertNotNull($result);

        /* Cleanup */
        $this->databaseDelete('ip_payments', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
