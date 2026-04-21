<?php

namespace Tests\Feature\Invoices;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Setting;
use Modules\Invoices\Models\InvoiceTaxRate;
use Modules\Invoices\Services\InvoiceTaxRateService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(InvoiceTaxRateService::class)]

class InvoiceTaxRateServiceTest extends AbstractServiceTestCase
{
    private InvoiceTaxRateService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoiceTaxRateService();

        DB::table('ip_invoice_tax_rates')->delete();
        DB::table('ip_invoice_amounts')->delete();
        DB::table('ip_invoice_items')->delete();
        DB::table('ip_invoices')->delete();

        Setting::setValue('legacy_calculation', '0');
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Arrange */
        /* (no additional setup needed beyond setUp()) */

        /* Act */
        $rules = $this->service->getValidationRules();

        /* Assert */
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('include_item_tax', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_not_in_legacy_mode(): void
    {
        /* Arrange */
        Setting::setValue('legacy_calculation', '0');

        $data = [
            'invoice_id'       => 1,
            'tax_rate_id'      => 1,
            'include_item_tax' => 0,
        ];

        /* Act */
        $result = $this->service->saveTaxRate($data);

        /* Assert */
        $this->assertNull($result);
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_tax_rate_in_legacy_mode(): void
    {
        /* Arrange */
        Setting::setValue('legacy_calculation', '1');

        $data = [
            'invoice_id'               => 1,
            'tax_rate_id'              => 1,
            'include_item_tax'         => 0,
            'invoice_tax_rate_percent' => 10.0,
            'invoice_tax_rate_amount'  => 0.0,
        ];

        /* Act */
        $result = $this->service->saveTaxRate($data);

        /* Assert */
        $this->assertInstanceOf(InvoiceTaxRate::class, $result);
        $this->assertEquals(1, $result->invoice_id);
        $this->assertEquals(1, $result->tax_rate_id);
        $this->assertEquals(0, $result->include_item_tax);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_tax_rate_in_legacy_mode(): void
    {
        /* Arrange */
        Setting::setValue('legacy_calculation', '1');

        $existingTaxRate = InvoiceTaxRate::query()->create([
            'invoice_id'               => 1,
            'tax_rate_id'              => 1,
            'include_item_tax'         => 0,
            'invoice_tax_rate_percent' => 10.0,
            'invoice_tax_rate_amount'  => 0.0,
        ]);

        $data = [
            'invoice_tax_rate_id'      => $existingTaxRate->invoice_tax_rate_id,
            'invoice_id'               => 1,
            'tax_rate_id'              => 2,
            'include_item_tax'         => 1,
            'invoice_tax_rate_percent' => 20.0,
            'invoice_tax_rate_amount'  => 50.0,
        ];

        /* Act */
        $result = $this->service->saveTaxRate($data);

        /* Assert */
        $this->assertEquals($existingTaxRate->invoice_tax_rate_id, $result->invoice_tax_rate_id);
        $this->assertEquals(2, $result->tax_rate_id);
        $this->assertEquals(1, $result->include_item_tax);
        $this->assertEquals(20.0, (float) $result->invoice_tax_rate_percent);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_include_item_tax_flag(): void
    {
        /* Arrange */
        Setting::setValue('legacy_calculation', '1');

        $data1 = [
            'invoice_id'               => 1,
            'tax_rate_id'              => 1,
            'include_item_tax'         => 1,
            'invoice_tax_rate_percent' => 10.0,
            'invoice_tax_rate_amount'  => 0.0,
        ];

        /* Act */
        $result1 = $this->service->saveTaxRate($data1);

        /* Assert */
        $this->assertEquals(1, $result1->include_item_tax);

        /* Arrange (second case) */
        $data2 = [
            'invoice_id'               => 2,
            'tax_rate_id'              => 2,
            'include_item_tax'         => 0,
            'invoice_tax_rate_percent' => 15.0,
            'invoice_tax_rate_amount'  => 0.0,
        ];

        /* Act */
        $result2 = $this->service->saveTaxRate($data2);

        /* Assert */
        $this->assertEquals(0, $result2->include_item_tax);
    }
}
