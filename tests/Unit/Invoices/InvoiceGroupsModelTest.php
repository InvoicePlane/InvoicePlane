<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoice_Groups;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoice_Groups::class)]
class InvoiceGroupsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoice_groups/mdl_invoice_groups');
        $this->model = $this->CI->mdl_invoice_groups;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_invoice_groups', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertEquals('ip_invoice_groups.invoice_group_id', $this->model->primary_key);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_group_name', $rules);
        $this->assertArrayHasKey('invoice_group_identifier_format', $rules);
        $this->assertArrayHasKey('invoice_group_next_id', $rules);
        $this->assertArrayHasKey('invoice_group_left_pad', $rules);
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
    public function it_requires_invoice_group_name_in_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertArrayHasKey('invoice_group_name', $rules);
        $this->assertEquals('required', $rules['invoice_group_name']['rules']);
    }
}
