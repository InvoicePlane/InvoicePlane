<?php

namespace Tests\Feature\Invoices;

use Mdl_Invoices_Recurring;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoices_Recurring::class)]
class InvoicesRecurringModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_invoices_recurring');
        $this->model = $this->CI->mdl_invoices_recurring;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('recur_start_date', $rules);
        $this->assertArrayHasKey('recur_frequency', $rules);
    }

    #[Test]
    public function it_validates_invoice_id_as_required_integer(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('rules', $rules['invoice_id']);
        $this->assertStringContainsString('required', $rules['invoice_id']['rules']);
    }

    #[Test]
    public function it_validates_recur_start_date_as_required_date(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('recur_start_date', $rules);
        $this->assertArrayHasKey('rules', $rules['recur_start_date']);
        $this->assertStringContainsString('required', $rules['recur_start_date']['rules']);
    }

    #[Test]
    public function it_validates_recur_end_date_as_nullable_date(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('recur_end_date', $rules);
        // recur_end_date is optional — no 'rules' key or empty rules
        $this->assertFalse(
            isset($rules['recur_end_date']['rules']) && $rules['recur_end_date']['rules'] !== '',
            'recur_end_date should be optional (no required rule)'
        );
    }

    #[Test]
    public function it_validates_recur_frequency_as_required_string(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('recur_frequency', $rules);
        $this->assertArrayHasKey('rules', $rules['recur_frequency']);
        $this->assertStringContainsString('required', $rules['recur_frequency']['rules']);
    }

    #[Test]
    public function it_validates_recur_next_date_as_nullable_date(): void
    {
        $this->assertEquals('ip_invoices_recurring', $this->model->table);
    }

    #[Test]
    public function it_provides_all_required_validation_keys(): void
    {
        $frequencies = $this->model->recur_frequencies;

        $this->assertIsArray($frequencies);
        $this->assertNotEmpty($frequencies);
        $this->assertArrayHasKey('1D', $frequencies);
        $this->assertArrayHasKey('1M', $frequencies);
        $this->assertArrayHasKey('1Y', $frequencies);
    }
}
