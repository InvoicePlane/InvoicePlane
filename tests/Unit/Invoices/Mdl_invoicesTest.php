<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoices;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

/**
 * Tests for Mdl_Invoices (application/modules/invoices/models/Mdl_invoices.php).
 */
#[CoversClass(Mdl_Invoices::class)]
class Mdl_invoicesTest extends CiTestCase
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
    public function it_returns_statuses_array(): void
    {
        $statuses = $this->model->statuses();

        $this->assertIsArray($statuses);
        $this->assertCount(4, $statuses);
        $this->assertArrayHasKey('1', $statuses);
        $this->assertArrayHasKey('2', $statuses);
        $this->assertArrayHasKey('3', $statuses);
        $this->assertArrayHasKey('4', $statuses);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_id', $rules);
        $this->assertArrayHasKey('invoice_date_created', $rules);
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

    #[Test]
    public function it_returns_url_key(): void
    {
        $urlKey = $this->model->get_url_key();

        $this->assertIsString($urlKey);
        $this->assertEquals(32, strlen($urlKey));
    }

    #[Test]
    public function it_calculates_date_due_from_invoice_date(): void
    {
        $created    = '2024-01-01';
        $dateDue    = $this->model->get_date_due($created);

        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $dateDue);
        // Date due must be after or equal to created date
        $this->assertGreaterThanOrEqual($created, $dateDue);
    }

    #[Test]
    public function it_has_validation_rules_save_invoice_method(): void
    {
        $rules = $this->model->validation_rules_save_invoice();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_number', $rules);
        $this->assertArrayHasKey('invoice_date_created', $rules);
        $this->assertArrayHasKey('invoice_date_due', $rules);
    }
}
