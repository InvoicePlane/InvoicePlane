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
        $result = $this->model->is_draft();
        $this->assertInstanceOf(\Mdl_Invoices::class, $result);
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_sent_returns_self(): void
    {
        $result = $this->model->is_sent();
        $this->assertInstanceOf(\Mdl_Invoices::class, $result);
    }

    #[Group('smoke')]
    #[Test]
    public function it_is_paid_returns_self(): void
    {
        $result = $this->model->is_paid();
        $this->assertInstanceOf(\Mdl_Invoices::class, $result);
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_invoice_and_retrieves_it(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'InvTestClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $invoice_number = 'INV-TEST-' . uniqid();
        $this->CI->db->insert('ip_invoices', [
            'client_id'                => $client_id,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => $invoice_number,
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $invoice_id = $this->CI->db->insert_id();

        /* Act */
        $invoice = $this->CI->db->get_where('ip_invoices', ['invoice_id' => $invoice_id])->row();

        /* Assert */
        $this->assertNotNull($invoice);
        $this->assertEquals($invoice_number, $invoice->invoice_number);
        $this->assertEquals(1, (int) $invoice->invoice_status_id);

        /* Cleanup */
        $this->CI->db->delete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_attaches_payments_to_invoice_object(): void
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
            'invoice_status_id'        => 4,
            'invoice_number'           => 'INV-PAY-' . uniqid(),
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

        $invoice = $this->CI->db->get_where('ip_invoices', ['invoice_id' => $invoice_id])->row();

        /* Act */
        $result = $this->model->get_payments($invoice);

        /* Assert */
        $this->assertNotNull($result);

        /* Cleanup */
        $this->CI->db->delete('ip_payments', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }
}
