<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Core;

use Mdl_Tax_Rates;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Tax_Rates::class)]
#[CoversClass(Tests\Feature\Core\TaxRatesService::class)]
class TaxRatesModelTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = app(TaxRatesService::class);
    }

    #[Test]
    public function it_retrieves_all_tax_rates(): void
    {
        /* Arrange */
        TaxRate::create([
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => 20.00,
        ]);
        TaxRate::create([
            'tax_rate_name'    => 'VAT 10%',
            'tax_rate_percent' => 10.00,
        ]);

        /* Act */
        $result = $this->model->defaultSelect()->get();

        /* Assert */
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $rules = $this->model->validationRules();

        /* Assert */
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('tax_rate_name', $rules);
        $this->assertArrayHasKey('tax_rate_percent', $rules);
        $this->assertEquals('required', $rules['tax_rate_name']['rules']);
        $this->assertEquals('required', $rules['tax_rate_percent']['rules']);
    }

    #[Test]
    public function it_all_returns_all_tax_rates(): void
    {
        /* Arrange */
        $this->seedModelMany('TaxRate', 5);

        /* Act */
        $results = $this->model->getAll();

        /* Assert */
        $this->assertCount(5, $results);
    }
}
