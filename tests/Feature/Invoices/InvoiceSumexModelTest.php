<?php

namespace Tests\Feature\Invoices;

use Mdl_invoice_sumex;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_invoice_sumex::class)]
class InvoiceSumexModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_invoice_sumex');
        $this->model = $this->CI->mdl_invoice_sumex;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('sumex_invoice', $rules);
        $this->assertArrayHasKey('sumex_reason', $rules);
        $this->assertArrayHasKey('sumex_treatmentstart', $rules);
        $this->assertArrayHasKey('sumex_treatmentend', $rules);
        $this->assertArrayHasKey('sumex_casedate', $rules);
    }

    #[Test]
    public function it_validates_sumex_invoice_as_required_integer(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('sumex_invoice', $rules);
        $this->assertArrayHasKey('rules', $rules['sumex_invoice']);
        $this->assertStringContainsString('required', $rules['sumex_invoice']['rules']);
    }

    #[Test]
    public function it_validates_optional_fields_as_nullable(): void
    {
        $rules = $this->model->validation_rules();

        // Optional fields should exist but have no 'rules' key or empty rules
        $this->assertArrayHasKey('sumex_diagnosis', $rules);
        $hasRules = isset($rules['sumex_diagnosis']['rules']) && $rules['sumex_diagnosis']['rules'] !== '';
        $this->assertFalse($hasRules, 'sumex_diagnosis should be optional (no required rule)');
    }

    #[Test]
    public function it_validates_date_fields(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('sumex_treatmentstart', $rules);
        $this->assertArrayHasKey('rules', $rules['sumex_treatmentstart']);
        $this->assertStringContainsString('required', $rules['sumex_treatmentstart']['rules']);

        $this->assertArrayHasKey('sumex_casedate', $rules);
        $this->assertArrayHasKey('rules', $rules['sumex_casedate']);
        $this->assertStringContainsString('required', $rules['sumex_casedate']['rules']);
    }

    #[Test]
    public function it_validates_string_fields(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertArrayHasKey('sumex_casenumber', $rules);
        $this->assertEquals('ip_invoice_sumex', $this->model->table);
    }
}
