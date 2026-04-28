<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoices;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoices::class)]
class InvoiceModelTest extends CiTestCase
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
    public function it_has_correct_primary_key(): void
    {
        $this->assertEquals('ip_invoices.invoice_id', $this->model->primary_key);
    }

    #[Test]
    public function it_returns_statuses(): void
    {
        $statuses = $this->model->statuses();
        $this->assertIsArray($statuses);
        $this->assertCount(4, $statuses);
        $this->assertArrayHasKey('1', $statuses); // draft
        $this->assertArrayHasKey('2', $statuses); // sent
        $this->assertArrayHasKey('3', $statuses); // viewed
        $this->assertArrayHasKey('4', $statuses); // paid
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_id', $rules);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_default_order_by_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_order_by'));
    }

    #[Test]
    public function it_has_default_join_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_join'));
    }
}
