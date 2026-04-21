<?php

namespace Modules\Invoices\tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Modules\InvoiceGroups\Tests\Unit\app;

use Modules\Invoices\app\Models\InvoiceGroup;
use Modules\Invoices\app\Services\InvoiceGroupsService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InvoiceGroupsServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoiceGroupsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InvoiceGroupsService::class);
    }

    #[Test]
    public function it_retrieves_all_invoice_groups(): void
    {
        // Arrange
        InvoiceGroup::create([
            'invoice_group_name' => 'Default',
        ]);
        InvoiceGroup::create([
            'invoice_group_name' => 'Custom Group',
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
    }

    #[Test]
    public function it_orders_by_next_id_by_default(): void
    {
        // Arrange
        InvoiceGroup::create([
            'invoice_group_name'    => 'Group A',
            'invoice_group_next_id' => 100,
        ]);
        InvoiceGroup::create([
            'invoice_group_name'    => 'Group B',
            'invoice_group_next_id' => 50,
        ]);

        // Act
        $result = $this->service->defaultOrderBy()->get();

        // Assert
        $this->assertCount(2, $result);
    }
}
