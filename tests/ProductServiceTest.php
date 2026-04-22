<?php

use Modules\Products\Models\Family;
use Modules\Products\Services\FamilyService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

/**
 * FamilyService Deletion Validation Tests.
 *
 * Tests business rules for family deletion:
 * - Families with products cannot be deleted
 */
#[CoversClass(FamilyService::class)]

class ProductServiceTest extends AbstractTestCase
{
    private \Tests\Feature\Invoices\ProductService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new \Tests\Feature\Invoices\ProductService();
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
