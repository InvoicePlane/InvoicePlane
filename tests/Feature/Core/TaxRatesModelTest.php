<?php

namespace Tests\Feature\Core;

use Mdl_Tax_Rates;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Tax_Rates::class)]
class TaxRatesModelTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        get_instance()->load->model('tax_rates/mdl_tax_rates');
        $this->model = get_instance()->mdl_tax_rates;
    }

    #[Test]
    public function it_retrieves_all_tax_rates(): void
    {
        /* Arrange */
        $this->model->save(null, [
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => 20.00,
        ]);
        $this->model->save(null, [
            'tax_rate_name'    => 'VAT 10%',
            'tax_rate_percent' => 10.00,
        ]);

        /* Act */
        $this->model->default_select();
        $result = $this->model->get()->result();

        /* Assert */
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $rules = $this->model->validation_rules();

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
        for ($i = 1; $i <= 5; $i++) {
            $this->model->save(null, [
                'tax_rate_name'    => "Tax Rate {$i}",
                'tax_rate_percent' => $i * 5.00,
            ]);
        }

        /* Act */
        $this->model->default_select();
        $results = $this->model->get()->result();

        /* Assert */
        $this->assertCount(5, $results);
    }
}
