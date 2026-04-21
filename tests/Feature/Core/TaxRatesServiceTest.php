<?php

namespace Tests\Feature\Core;

use Tests\Concerns\InteractsWithDatabase;

use Modules\CustomFields\Models\CustomField;
use Modules\CustomFields\Services\CustomFieldsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TaxRatesServiceTest extends TestCase
{
    use InteractsWithDatabase;


    private TaxRatesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TaxRatesService::class);
    }

    #[Test]
    public function it_retrieves_all_tax_rates(): void
    {
        // Arrange
        TaxRate::create([
            'tax_rate_name'    => 'VAT 20%',
            'tax_rate_percent' => 20.00,
        ]);
        TaxRate::create([
            'tax_rate_name'    => 'VAT 10%',
            'tax_rate_percent' => 10.00,
        ]);

        // Act
        $result = $this->service->defaultSelect()->get();

        // Assert
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        // Act
        $rules = $this->service->validationRules();

        // Assert
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('tax_rate_name', $rules);
        $this->assertArrayHasKey('tax_rate_percent', $rules);
        $this->assertEquals('required', $rules['tax_rate_name']['rules']);
        $this->assertEquals('required', $rules['tax_rate_percent']['rules']);
    }

    #[Test]
    public function it_all_returns_all_tax_rates(): void
    {
        $this->seedModelMany('TaxRate', 5);

        $results = $this->service->getAll();
        $this->assertCount(5, $results);
    }
}
