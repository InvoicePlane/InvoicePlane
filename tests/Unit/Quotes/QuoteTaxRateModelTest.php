<?php

namespace Tests\Unit\Quotes;

use Mdl_Quote_Tax_Rates;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quote_Tax_Rates::class)]
class QuoteTaxRateModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quote_tax_rates');
        $this->model = $this->CI->mdl_quote_tax_rates;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_quote_tax_rates', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertEquals('ip_quote_tax_rates.quote_tax_rate_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('include_item_tax', $rules);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
        // Calling default_select() sets up the query builder
        $this->model->default_select();
        // Verify it doesn't throw an error
        $this->addToAssertionCount(1);
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
    public function it_requires_quote_id_in_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertArrayHasKey('quote_id', $rules);
        $this->assertEquals('required', $rules['quote_id']['rules']);
    }

    #[Test]
    public function it_requires_tax_rate_id_in_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertEquals('required', $rules['tax_rate_id']['rules']);
    }

    #[Test]
    public function it_requires_include_item_tax_in_validation_rules(): void
    {
        $rules = $this->model->validation_rules();
        $this->assertArrayHasKey('include_item_tax', $rules);
        $this->assertEquals('required', $rules['include_item_tax']['rules']);
    }
}
