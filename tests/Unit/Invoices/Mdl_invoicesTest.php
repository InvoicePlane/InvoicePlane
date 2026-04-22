<?php
/**
 * Test coverage for Mdl_Invoices Model (application/modules/invoices/models/Mdl_invoices.php)
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Mdl_Invoices_Test extends CIUnit_TestCase
{
    public function set_up()
    {
        parent::set_up();
        $this->CI->load->model('invoices/mdl_invoices');
    }

    public function test_get_invoice_by_id()
    {
        $invoice = $this->CI->mdl_invoices->get_invoice_by_id(1);
        $this->assertEquals('Invoice 1', $invoice->title);
    }

    public function test_get_all_invoices()
    {
        $invoices = $this->CI->mdl_invoices->get_all_invoices();
        $this->assertCount(2, $invoices);
    }

    public function test_create_invoice()
    {
        $data = array(
            'title' => 'New Invoice',
            'amount' => 100,
            'status' => 'unpaid'
        );
        $invoice_id = $this->CI->mdl_invoices->create_invoice($data);
        $this->assertGreaterThan(0, $invoice_id);
    }

    public function test_update_invoice()
    {
        $data = array(
            'title' => 'Updated Invoice',
            'amount' => 150,
            'status' => 'paid'
        );
        $this->CI->mdl_invoices->update_invoice(1, $data);
        $invoice = $this->CI->mdl_invoices->get_invoice_by_id(1);
        $this->assertEquals('Updated Invoice', $invoice->title);
    }

    public function test_delete_invoice()
    {
        $this->CI->mdl_invoices->delete_invoice(2);
        $invoice = $this->CI->mdl_invoices->get_invoice_by_id(2);
        $this->assertNull($invoice);
    }
}
