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
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'PayClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoices', [
            'client_id'                => $client_id,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-PM-' . uniqid(),
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $invoice_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_payments', [
            'invoice_id'           => $invoice_id,
            'payment_method_id'    => 1,
            'payment_amount'       => '150.00',
            'payment_date'         => date('Y-m-d'),
            'payment_note'         => '',
            'payment_date_created' => date('Y-m-d H:i:s'),
        ]);
        $payment_id = $this->CI->db->insert_id();

        /* Act */
        $payment = $this->CI->db->get_where('ip_payments', ['payment_id' => $payment_id])->row();

        /* Assert */
        $this->assertNotNull($payment);
        $this->assertEquals(150.00, (float) $payment->payment_amount);
        $this->assertEquals($invoice_id, (int) $payment->invoice_id);

        /* Cleanup */
        $this->CI->db->delete('ip_payments', ['payment_id' => $payment_id]);
        $this->CI->db->delete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_retrieves_payments_by_invoice_id(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'PayFilter_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoices', [
            'client_id'                => $client_id,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-PMFILT-' . uniqid(),
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $invoice_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_payments', [
            'invoice_id'           => $invoice_id,
            'payment_method_id'    => 1,
            'payment_amount'       => '100.00',
            'payment_date'         => date('Y-m-d'),
            'payment_note'         => '',
            'payment_date_created' => date('Y-m-d H:i:s'),
        ]);
        $payment_id = $this->CI->db->insert_id();

        /* Act */
        $this->model->by_client(null); // reset
        $payments = $this->CI->db->get_where('ip_payments', ['invoice_id' => $invoice_id])->result();

        /* Assert */
        $this->assertNotEmpty($payments);
        $this->assertEquals($payment_id, (int) $payments[0]->payment_id);

        /* Cleanup */
        $this->CI->db->delete('ip_payments', ['payment_id' => $payment_id]);
        $this->CI->db->delete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }
}
