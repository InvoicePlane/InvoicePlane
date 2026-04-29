<?php

namespace Tests\Unit\Payments;

use Mdl_Payments;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Payments::class)]
class PaymentsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('payments/mdl_payments');
        $this->model = $this->CI->mdl_payments;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_payments', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('payment_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('payment_method_id', $rules);
        $this->assertArrayHasKey('payment_amount', $rules);
        $this->assertArrayHasKey('payment_date', $rules);
    }

    #[Test]
    public function it_has_by_client_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'by_client'));
    }

    #[Test]
    public function it_has_validate_payment_amount_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'validate_payment_amount'));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_and_retrieves_payment(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;
        $payment_id = $this->seedModel('Payment', ['invoice_id' => $invoice_id, 'payment_amount' => '150.00'])->payment_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_payments', ['payment_id' => $payment_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals(150.00, (float) $row['payment_amount']);
        $this->assertEquals($invoice_id, (int) $row['invoice_id']);

        /* Cleanup */
        $this->databaseDelete('ip_payments', ['payment_id' => $payment_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_retrieves_payments_by_invoice_id(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;
        $payment_id = $this->seedModel('Payment', ['invoice_id' => $invoice_id, 'payment_amount' => '100.00'])->payment_id;

        /* Assert */
        $this->assertDatabaseHas('ip_payments', ['payment_id' => $payment_id, 'invoice_id' => $invoice_id]);
        $this->assertDatabaseCount('ip_payments', 1, ['invoice_id' => $invoice_id]);

        /* Cleanup */
        $this->databaseDelete('ip_payments', ['payment_id' => $payment_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
