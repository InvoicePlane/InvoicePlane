<?php

namespace Modules\Products\Tests\Unit;

use Modules\Products\Services\ProductService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(ProductService::class)]

class ProductServiceTest extends AbstractServiceTestCase
{
    private ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProductService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('product_name', $rules);
        $this->assertArrayHasKey('product_price', $rules);
        $this->assertArrayHasKey('family_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('unit_id', $rules);
    }
}
