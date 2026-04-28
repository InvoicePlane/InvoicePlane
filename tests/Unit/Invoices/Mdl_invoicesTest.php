<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoices;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\CiTestCase;

/**
 * Test coverage for Mdl_Invoices Model (application/modules/invoices/models/Mdl_invoices.php).
 */
#[CoversClass(Mdl_Invoices::class)]
class Mdl_Invoices_Test extends CiTestCase
{
    public function set_up(): void
    {
        parent::set_up();
        $this->CI->load->model('invoices/mdl_invoices');
    }

    public function test_get_invoice_by_id(): void
    {
        $invoice = $this->CI->mdl_invoices->get_invoice_by_id(1);
        $this->assertEquals('Invoice 1', $invoice->title);
    }

    public function test_get_all_invoices(): void
    {
        $invoices = $this->CI->mdl_invoices->get_all_invoices();
        $this->assertCount(2, $invoices);
    }

    public function test_create_invoice(): void
    {
        $data = [
            'title'  => 'New Invoice',
            'amount' => 100,
            'status' => 'unpaid',
        ];
        $invoice_id = $this->CI->mdl_invoices->create_invoice($data);
        $this->assertGreaterThan(0, $invoice_id);
    }

    public function test_update_invoice(): void
    {
        $data = [
            'title'  => 'Updated Invoice',
            'amount' => 150,
            'status' => 'paid',
        ];
        $this->CI->mdl_invoices->update_invoice(1, $data);
        $invoice = $this->CI->mdl_invoices->get_invoice_by_id(1);
        $this->assertEquals('Updated Invoice', $invoice->title);
    }

    public function test_delete_invoice(): void
    {
        $this->markTestIncomplete('weak test');
        $this->CI->mdl_invoices->delete_invoice(2);
        $invoice = $this->CI->mdl_invoices->get_invoice_by_id(2);
        $this->assertNull($invoice);
    }
}
