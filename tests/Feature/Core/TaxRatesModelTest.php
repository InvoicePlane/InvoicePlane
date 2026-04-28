<?php

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
        $this->markTestIncomplete('This test uses Laravel Model::create pattern which needs to be refactored to use CodeIgniter insert patterns');
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
        $this->markTestIncomplete('This test needs to be refactored to use CodeIgniter query builder instead of getAll()');
    }
}
