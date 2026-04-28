<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Models;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Basic smoke tests for Mdl_templates model
 * Tests verify model structure and key functionality
 */
#[CoversClass(Mdl_Templates::class)]
class Mdl_templatesTest extends CiTestCase
{
    protected $model;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->CI->load->model('invoices/mdl_templates');
        $this->model = $this->CI->mdl_templates;
    }

    #[Test]
    public function it_has_get_invoice_templates_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_invoice_templates'));
    }

    #[Test]
    public function it_has_get_quote_templates_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_quote_templates'));
    }

    #[Test]
    public function it_extends_ci_model(): void
    {
        $this->assertInstanceOf('CI_Model', $this->model);
    }
}
