<?php

namespace Tests\Unit\Invoices;

use Mdl_Items;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Items::class)]
class InvoiceItemsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_items');
        $this->model = $this->CI->mdl_items;
    }

    #[Test]
    public function it_has_correct_table(): void
    {
        $this->assertEquals('ip_invoice_items', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('item_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertEquals('required', $rules['invoice_id']['rules']);
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

    #[Test]
    public function it_has_save_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'save'));
    }

    #[Test]
    public function it_has_delete_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'delete'));
    }
}
