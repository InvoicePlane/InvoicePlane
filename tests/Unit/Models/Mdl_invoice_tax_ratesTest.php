<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Basic smoke tests for Mdl_invoice_tax_rates model
 * Tests verify model structure and key functionality
 */
#[CoversClass(Mdl_Invoice_Tax_Rates::class)]
class Mdl_invoice_tax_ratesTest extends CiTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->CI->load->model('invoices/mdl_invoice_tax_rates');
        $this->model = $this->CI->mdl_invoice_tax_rates;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_invoice_tax_rates', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertEquals('ip_invoice_tax_rates.invoice_tax_rate_id', $this->model->primary_key);
    }

    #[Test]
    public function it_has_validation_rules(): void
    {
        $this->assertTrue(method_exists($this->model, 'validation_rules'));
        $rules = $this->model->validation_rules();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
    }

    #[Test]
    public function it_extends_response_model(): void
    {
        $this->assertInstanceOf('Response_Model', $this->model);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_default_join_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_join'));
    }
}
