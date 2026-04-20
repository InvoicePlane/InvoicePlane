<?php

namespace Modules\Invoices\Tests\Unit;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Setting;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceAmount;
use Modules\Invoices\Models\InvoiceItem;
use Modules\Invoices\Models\ItemAmount;
use Modules\Invoices\Services\InvoiceAmountService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(InvoiceAmountService::class)]
class InvoiceAmountServiceTest extends AbstractServiceTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        DB::table('ip_invoice_amounts')->delete();
        DB::table('ip_invoice_item_amounts')->delete();
        DB::table('ip_invoice_items')->delete();
        DB::table('ip_payments')->delete();
        DB::table('ip_invoices')->delete();

        Setting::setValue('tax_rate_decimal_places', '2');
        Setting::setValue('legacy_calculation', '0');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_totals_with_payments(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-1000',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-1000',
        ]);

        $firstItem = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_tax_rate_id'     => null,
            'item_product_id'      => null,
            'item_name'            => 'Consulting',
            'item_description'     => 'Consulting hours',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
            'item_product_unit'    => null,
            'item_product_unit_id' => null,
        ]);

        $secondItem = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_tax_rate_id'     => null,
            'item_product_id'      => null,
            'item_name'            => 'Support',
            'item_description'     => 'Support plan',
            'item_quantity'        => 1,
            'item_price'           => 150,
            'item_order'           => 2,
            'item_discount_amount' => 0,
            'item_product_unit'    => null,
            'item_product_unit_id' => null,
        ]);

        ItemAmount::query()->create([
            'item_id'        => $firstItem->item_id,
            'item_subtotal'  => 200,
            'item_tax_total' => 20,
            'item_discount'  => 0,
            'item_total'     => 220,
        ]);

        ItemAmount::query()->create([
            'item_id'        => $secondItem->item_id,
            'item_subtotal'  => 150,
            'item_tax_total' => 15,
            'item_discount'  => 0,
            'item_total'     => 165,
        ]);

        DB::table('ip_payments')->insert([
            'invoice_id'     => $invoice->invoice_id,
            'payment_amount' => 100,
            'payment_method' => 1,
            'payment_date'   => '2024-01-10',
        ]);

        $service = app(InvoiceAmountService::class);
        $service->calculate($invoice->invoice_id);

        $amount = InvoiceAmount::query()->where('invoice_id', $invoice->invoice_id)->firstOrFail();

        $this->assertEquals(350.0, (float) $amount->invoice_item_subtotal);
        $this->assertEquals(35.0, (float) $amount->invoice_item_tax_total);
        $this->assertEquals(385.0, (float) $amount->invoice_total);
        $this->assertEquals(100.0, (float) $amount->invoice_paid);
        $this->assertEquals(285.0, (float) $amount->invoice_balance);
        $this->assertEquals(0.0, (float) $amount->invoice_tax_total);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_totals_without_payments(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-1001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-1001',
        ]);

        $item = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_tax_rate_id'     => null,
            'item_product_id'      => null,
            'item_name'            => 'Service',
            'item_description'     => 'Service description',
            'item_quantity'        => 1,
            'item_price'           => 500,
            'item_order'           => 1,
            'item_discount_amount' => 0,
            'item_product_unit'    => null,
            'item_product_unit_id' => null,
        ]);

        ItemAmount::query()->create([
            'item_id'        => $item->item_id,
            'item_subtotal'  => 500,
            'item_tax_total' => 50,
            'item_discount'  => 0,
            'item_total'     => 550,
        ]);

        $service = app(InvoiceAmountService::class);
        $service->calculate($invoice->invoice_id);

        $amount = InvoiceAmount::query()->where('invoice_id', $invoice->invoice_id)->firstOrFail();

        $this->assertEquals(500.0, (float) $amount->invoice_item_subtotal);
        $this->assertEquals(50.0, (float) $amount->invoice_item_tax_total);
        $this->assertEquals(550.0, (float) $amount->invoice_total);
        $this->assertEquals(0.0, (float) $amount->invoice_paid);
        $this->assertEquals(550.0, (float) $amount->invoice_balance);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_invoice_with_global_discount(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '0');

        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-1002',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-1002',
        ]);

        $item = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_tax_rate_id'     => null,
            'item_product_id'      => null,
            'item_name'            => 'Product',
            'item_description'     => 'Product description',
            'item_quantity'        => 1,
            'item_price'           => 1000,
            'item_order'           => 1,
            'item_discount_amount' => 0,
            'item_product_unit'    => null,
            'item_product_unit_id' => null,
        ]);

        ItemAmount::query()->create([
            'item_id'        => $item->item_id,
            'item_subtotal'  => 1000,
            'item_tax_total' => 100,
            'item_discount'  => 0,
            'item_total'     => 1100,
        ]);

        $globalDiscount = ['item' => 100.0];
        $service        = app(InvoiceAmountService::class);
        $service->calculate($invoice->invoice_id, $globalDiscount);

        $amount = InvoiceAmount::query()->where('invoice_id', $invoice->invoice_id)->firstOrFail();

        $this->assertEquals(900.0, (float) $amount->invoice_item_subtotal);
        $this->assertEquals(1000.0, (float) $amount->invoice_total);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_discount_with_amount_and_percent(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-1003',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 50,
            'invoice_discount_percent' => 10,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-1003',
        ]);

        $service = app(InvoiceAmountService::class);
        $result  = $service->calculateDiscount($invoice->invoice_id, 1000, 2);

        // 1000 - 50 = 950, then 950 - (950 * 10 / 100) = 855
        $this->assertEquals(855.0, $result);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_for_global_discount_when_no_items(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-1004',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-1004',
        ]);

        $service = app(InvoiceAmountService::class);
        $result  = $service->getGlobalDiscount($invoice->invoice_id);

        $this->assertEquals(0.0, $result);
    }

    #[Test]
    public function it_gets_total_invoiced_for_month(): void
    {
        $this->markTestIncomplete();
        $service = app(InvoiceAmountService::class);
        $result  = $service->getTotalInvoiced('month');

        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0.0, $result);
    }

    #[Test]
    public function it_gets_total_paid_for_year(): void
    {
        $this->markTestIncomplete();
        $service = app(InvoiceAmountService::class);
        $result  = $service->getTotalPaid('year');

        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0.0, $result);
    }

    #[Test]
    public function it_gets_total_balance_for_last_month(): void
    {
        $this->markTestIncomplete();
        $service = app(InvoiceAmountService::class);
        $result  = $service->getTotalBalance('last_month');

        $this->assertIsFloat($result);
        $this->assertGreaterThanOrEqual(0.0, $result);
    }

    #[Test]
    public function it_gets_status_totals_for_this_month(): void
    {
        $this->markTestIncomplete();
        $service = app(InvoiceAmountService::class);
        $result  = $service->getStatusTotals('this-month');

        $this->assertIsArray($result);
        $this->assertArrayHasKey(1, $result); // Draft
        $this->assertArrayHasKey(2, $result); // Sent
        $this->assertArrayHasKey(3, $result); // Viewed
        $this->assertArrayHasKey(4, $result); // Paid

        foreach ($result as $status) {
            $this->assertArrayHasKey('invoice_status_id', $status);
            $this->assertArrayHasKey('sum_total', $status);
            $this->assertArrayHasKey('sum_paid', $status);
            $this->assertArrayHasKey('sum_balance', $status);
            $this->assertArrayHasKey('num_total', $status);
        }
    }

    #[Test]
    public function it_gets_status_totals_for_different_periods(): void
    {
        $this->markTestIncomplete();
        $service = app(InvoiceAmountService::class);

        $periods = ['last-month', 'this-quarter', 'last-quarter', 'this-year', 'last-year'];

        foreach ($periods as $period) {
            $result = $service->getStatusTotals($period);
            $this->assertIsArray($result);
            $this->assertCount(4, $result);
        }
    }
}

#[CoversClass(InvoiceGroupService::class)]
class InvoiceGroupServiceTest extends AbstractServiceTestCase
{
    private InvoiceGroupService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoiceGroupService();

        DB::table('ip_invoice_groups')->delete();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_group_name', $rules);
        $this->assertArrayHasKey('invoice_group_identifier_format', $rules);
        $this->assertArrayHasKey('invoice_group_next_id', $rules);
        $this->assertArrayHasKey('invoice_group_left_pad', $rules);
    }

    #[Test]
    public function it_generates_invoice_number_with_year_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV-{{{year}}}-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $expectedYear = date('Y');
        $this->assertEquals("INV-{$expectedYear}-0001", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_month_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{month}}}/{{{id}}}',
            'invoice_group_next_id'           => 5,
            'invoice_group_left_pad'          => 3,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $expectedMonth = date('m');
        $this->assertEquals("{$expectedMonth}/005", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_day_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{day}}}-{{{id}}}',
            'invoice_group_next_id'           => 10,
            'invoice_group_left_pad'          => 2,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $expectedDay = date('d');
        $this->assertEquals("{$expectedDay}-10", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_short_year_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{yy}}}{{{id}}}',
            'invoice_group_next_id'           => 100,
            'invoice_group_left_pad'          => 5,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $expectedYY = date('y');
        $this->assertEquals("{$expectedYY}00100", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_multiple_templates(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{year}}}/{{{month}}}/{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 6,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $expectedYear  = date('Y');
        $expectedMonth = date('m');
        $this->assertEquals("{$expectedYear}/{$expectedMonth}/000001", $number);
    }

    #[Test]
    public function it_generates_invoice_number_without_templates(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'STATIC-PREFIX',
            'invoice_group_next_id'           => 999,
            'invoice_group_left_pad'          => 0,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $this->assertEquals('STATIC-PREFIX', $number);
    }

    #[Test]
    public function it_increments_next_id_when_set_next_is_true(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 50,
            'invoice_group_left_pad'          => 3,
        ]);

        $this->service->generateInvoiceNumber($group, true);

        $group->refresh();
        $this->assertEquals(51, $group->invoice_group_next_id);
    }

    #[Test]
    public function it_does_not_increment_next_id_when_set_next_is_false(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 50,
            'invoice_group_left_pad'          => 3,
        ]);

        $this->service->generateInvoiceNumber($group, false);

        $group->refresh();
        $this->assertEquals(50, $group->invoice_group_next_id);
    }

    #[Test]
    public function it_pads_invoice_id_with_zeros(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{id}}}',
            'invoice_group_next_id'           => 7,
            'invoice_group_left_pad'          => 10,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $this->assertEquals('0000000007', $number);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_zero_left_pad(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV{{{id}}}',
            'invoice_group_next_id'           => 123,
            'invoice_group_left_pad'          => 0,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $this->assertEquals('INV123', $number);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_unknown_template_variables(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{unknown}}}-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 2,
        ]);

        $number = $this->service->generateInvoiceNumber($group, false);

        $this->assertEquals('-01', $number); // Unknown variable replaced with empty string
    }
}

#[CoversClass(InvoiceItemAmountService::class)]
class InvoiceItemAmountServiceTest extends AbstractServiceTestCase
{
    private InvoiceItemAmountService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoiceItemAmountService();

        DB::table('ip_invoice_item_amounts')->delete();
        DB::table('ip_invoice_items')->delete();
        DB::table('ip_tax_rates')->delete();

        Setting::setValue('tax_rate_decimal_places', '2');
        Setting::setValue('legacy_calculation', '0');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_in_legacy_mode(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '1');

        $taxRate = TaxRate::query()->create([
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => 10,
        ]);

        $item = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => $taxRate->tax_rate_id,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 5,
        ]);

        $globalDiscount = [];
        $this->service->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        $this->assertNotNull($itemAmount);
        $this->assertEquals(200.0, (float) $itemAmount->item_subtotal); // 2 * 100
        $this->assertEquals(20.0, (float) $itemAmount->item_tax_total);  // 200 * 10%
        $this->assertEquals(10.0, (float) $itemAmount->item_discount);   // 5 * 2
        $this->assertEquals(210.0, (float) $itemAmount->item_total);     // 200 + 20 - 10
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_with_no_tax(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '1');

        $item = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Test Item',
            'item_quantity'        => 3,
            'item_price'           => 50,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $globalDiscount = [];
        $this->service->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        $this->assertNotNull($itemAmount);
        $this->assertEquals(150.0, (float) $itemAmount->item_subtotal);
        $this->assertEquals(0.0, (float) $itemAmount->item_tax_total);
        $this->assertEquals(0.0, (float) $itemAmount->item_discount);
        $this->assertEquals(150.0, (float) $itemAmount->item_total);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_with_global_amount_discount(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '0');

        $item = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $globalDiscount = [
            'amount'         => 50,
            'items_subtotal' => 200,
        ];

        $this->service->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        $this->assertNotNull($itemAmount);
        $this->assertEquals(200.0, (float) $itemAmount->item_subtotal);
        $this->assertEquals(50.0, $globalDiscount['item']); // Global discount tracked
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_with_global_percent_discount(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '0');

        $item = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Test Item',
            'item_quantity'        => 1,
            'item_price'           => 1000,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $globalDiscount = [
            'percent' => 10,
        ];

        $this->service->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        $this->assertNotNull($itemAmount);
        $this->assertEquals(1000.0, (float) $itemAmount->item_subtotal);
        $this->assertEquals(100.0, $globalDiscount['item']); // 10% of 1000
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_with_item_and_global_discount(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '0');

        $taxRate = TaxRate::query()->create([
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => 20,
        ]);

        $item = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => $taxRate->tax_rate_id,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 10,
        ]);

        $globalDiscount = [
            'percent' => 5,
        ];

        $this->service->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        $this->assertNotNull($itemAmount);
        $this->assertEquals(200.0, (float) $itemAmount->item_subtotal);
        $this->assertEquals(20.0, (float) $itemAmount->item_discount); // 10 * 2
        $this->assertEquals(10.0, $globalDiscount['item']); // 5% of 200
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_item_amount(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '1');

        $item = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Test Item',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        // Create initial amount
        ItemAmount::query()->create([
            'item_id'        => $item->item_id,
            'item_subtotal'  => 50,
            'item_tax_total' => 5,
            'item_discount'  => 0,
            'item_total'     => 55,
        ]);

        $globalDiscount = [];
        $this->service->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        // Should update to correct values
        $this->assertEquals(100.0, (float) $itemAmount->item_subtotal);
        $this->assertEquals(0.0, (float) $itemAmount->item_tax_total);
        $this->assertEquals(100.0, (float) $itemAmount->item_total);
    }

    #[Test]
    public function it_accumulates_global_discount_across_multiple_items(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '0');

        $item1 = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Item 1',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $item2 = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Item 2',
            'item_quantity'        => 1,
            'item_price'           => 200,
            'item_order'           => 2,
            'item_discount_amount' => 0,
        ]);

        $globalDiscount = [
            'percent' => 10,
        ];

        $this->service->calculate($item1->item_id, $globalDiscount);
        $this->service->calculate($item2->item_id, $globalDiscount);

        // Should accumulate: 10% of 100 + 10% of 200 = 10 + 20 = 30
        $this->assertEquals(30.0, $globalDiscount['item']);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_fractional_quantities_and_prices(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '1');

        $item = InvoiceItem::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2.5,
            'item_price'           => 33.33,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $globalDiscount = [];
        $this->service->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        $this->assertNotNull($itemAmount);
        $this->assertEquals(83.325, (float) $itemAmount->item_subtotal); // 2.5 * 33.33
    }
}

#[CoversClass(InvoiceItemService::class)]
class InvoiceItemServiceTest extends AbstractServiceTestCase
{
    private InvoiceItemService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoiceItemService();

        DB::table('ip_invoice_amounts')->delete();
        DB::table('ip_invoice_item_amounts')->delete();
        DB::table('ip_invoice_items')->delete();
        DB::table('ip_invoices')->delete();

        Setting::setValue('tax_rate_decimal_places', '2');
        Setting::setValue('legacy_calculation', '0');
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('item_name', $rules);
        $this->assertArrayHasKey('item_description', $rules);
        $this->assertArrayHasKey('item_quantity', $rules);
        $this->assertArrayHasKey('item_price', $rules);
        $this->assertArrayHasKey('item_tax_rate_id', $rules);
        $this->assertArrayHasKey('item_product_id', $rules);
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_new_item(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-001',
        ]);

        $data = [
            'item_name'            => 'Test Item',
            'item_description'     => 'Test Description',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ];

        $item = $this->service->saveItem(null, $data, $invoice->invoice_id);

        $this->assertInstanceOf(InvoiceItem::class, $item);
        $this->assertEquals('Test Item', $item->item_name);
        $this->assertEquals(2, $item->item_quantity);
        $this->assertEquals(100, $item->item_price);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_item(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-002',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-002',
        ]);

        $existingItem = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_name'            => 'Original Name',
            'item_description'     => 'Original Description',
            'item_quantity'        => 1,
            'item_price'           => 50,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $data = [
            'item_name'            => 'Updated Name',
            'item_description'     => 'Updated Description',
            'item_quantity'        => 3,
            'item_price'           => 150,
            'item_order'           => 1,
            'item_discount_amount' => 10,
        ];

        $item = $this->service->saveItem($existingItem->item_id, $data, $invoice->invoice_id);

        $this->assertEquals($existingItem->item_id, $item->item_id);
        $this->assertEquals('Updated Name', $item->item_name);
        $this->assertEquals(3, $item->item_quantity);
        $this->assertEquals(150, $item->item_price);
        $this->assertEquals(10, $item->item_discount_amount);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_item_and_recalculates_invoice(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-003',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-003',
        ]);

        $item = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_name'            => 'Item to Delete',
            'item_description'     => 'Description',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        ItemAmount::query()->create([
            'item_id'        => $item->item_id,
            'item_subtotal'  => 100,
            'item_tax_total' => 10,
            'item_discount'  => 0,
            'item_total'     => 110,
        ]);

        $result = $this->service->deleteItem($item->item_id);

        $this->assertTrue($result);
        $this->assertNull(InvoiceItem::query()->find($item->item_id));
        $this->assertNull(ItemAmount::query()->where('item_id', $item->item_id)->first());
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_false_when_deleting_non_existent_item(): void
    {
        $this->markTestIncomplete();
        $result = $this->service->deleteItem(99999);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_gets_items_subtotal(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-004',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-004',
        ]);

        $item1 = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_name'            => 'Item 1',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $item2 = InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_name'            => 'Item 2',
            'item_quantity'        => 1,
            'item_price'           => 150,
            'item_order'           => 2,
            'item_discount_amount' => 0,
        ]);

        ItemAmount::query()->create([
            'item_id'        => $item1->item_id,
            'item_subtotal'  => 200,
            'item_tax_total' => 20,
            'item_discount'  => 0,
            'item_total'     => 220,
        ]);

        ItemAmount::query()->create([
            'item_id'        => $item2->item_id,
            'item_subtotal'  => 150,
            'item_tax_total' => 15,
            'item_discount'  => 0,
            'item_total'     => 165,
        ]);

        $subtotal = $this->service->getItemsSubtotal($invoice->invoice_id);

        $this->assertEquals(350.0, $subtotal);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_subtotal_for_invoice_without_items(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-005',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-005',
        ]);

        $subtotal = $this->service->getItemsSubtotal($invoice->invoice_id);

        $this->assertEquals(0.0, $subtotal);
    }
}

#[CoversClass(InvoiceService::class)]
class InvoiceServiceTest extends AbstractServiceTestCase
{
    private InvoiceService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoiceService();

        DB::table('ip_invoice_amounts')->delete();
        DB::table('ip_invoice_tax_rates')->delete();
        DB::table('ip_invoice_items')->delete();
        DB::table('ip_invoices')->delete();
        DB::table('ip_invoice_groups')->delete();
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_invoice_statuses(): void
    {
        $this->markTestIncomplete();
        $statuses = $this->service->getStatuses();

        $this->assertIsArray($statuses);
        $this->assertCount(4, $statuses);
        $this->assertArrayHasKey(1, $statuses); // Draft
        $this->assertArrayHasKey(2, $statuses); // Sent
        $this->assertArrayHasKey(3, $statuses); // Viewed
        $this->assertArrayHasKey(4, $statuses); // Paid

        foreach ($statuses as $status) {
            $this->assertArrayHasKey('label', $status);
            $this->assertArrayHasKey('class', $status);
            $this->assertArrayHasKey('href', $status);
        }
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_id', $rules);
        $this->assertArrayHasKey('invoice_date_created', $rules);
        $this->assertArrayHasKey('invoice_group_id', $rules);
        $this->assertArrayHasKey('invoice_password', $rules);
        $this->assertArrayHasKey('user_id', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_save_validation_rules_without_invoice_id(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getSaveValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_number', $rules);
        $this->assertEquals('unique:ip_invoices,invoice_number', $rules['invoice_number']);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_save_validation_rules_with_invoice_id(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getSaveValidationRules(123);

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_number', $rules);
        $this->assertEquals('unique:ip_invoices,invoice_number,123,invoice_id', $rules['invoice_number']);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_due_date_from_creation_date(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('invoices_due_after', '30');

        $createdDate = '2024-01-01';
        $expectedDue = '2024-01-31';

        $dueDate = $this->service->calculateDateDue($createdDate);

        $this->assertEquals($expectedDue, $dueDate);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_due_date_with_different_intervals(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('invoices_due_after', '7');

        $createdDate = '2024-01-15';
        $expectedDue = '2024-01-22';

        $dueDate = $this->service->calculateDateDue($createdDate);

        $this->assertEquals($expectedDue, $dueDate);
    }

    #[Test]
    public function it_generates_url_key(): void
    {
        $this->markTestIncomplete();
        $urlKey = $this->service->generateUrlKey();

        $this->assertIsString($urlKey);
        $this->assertEquals(32, mb_strlen($urlKey)); // 16 bytes = 32 hex chars
        $this->assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $urlKey);
    }

    #[Test]
    public function it_generates_unique_url_keys(): void
    {
        $this->markTestIncomplete();
        $key1 = $this->service->generateUrlKey();
        $key2 = $this->service->generateUrlKey();

        $this->assertNotEquals($key1, $key2);
    }

    #[Test]
    public function it_gets_invoice_group_id(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 5,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-2001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-2001',
        ]);

        $groupId = $this->service->getInvoiceGroupId($invoice->invoice_id);

        $this->assertEquals(5, $groupId);
    }

    #[Test]
    public function it_gets_parent_invoice_number(): void
    {
        $this->markTestIncomplete();
        $parentInvoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 4,
            'invoice_number'           => 'INV-PARENT-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-parent-001',
        ]);

        $number = $this->service->getParentInvoiceNumber($parentInvoice->invoice_id);

        $this->assertEquals('INV-PARENT-001', $number);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_invoice_and_related_records(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-DEL-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-del-001',
        ]);

        InvoiceItem::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_name'            => 'Test Item',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        InvoiceAmount::query()->create([
            'invoice_id'             => $invoice->invoice_id,
            'invoice_item_subtotal'  => 100,
            'invoice_item_tax_total' => 10,
            'invoice_total'          => 110,
            'invoice_paid'           => 0,
            'invoice_balance'        => 110,
        ]);

        $result = $this->service->deleteInvoice($invoice->invoice_id);

        $this->assertTrue($result);
        $this->assertNull(Invoice::query()->find($invoice->invoice_id));
        $this->assertEquals(0, InvoiceItem::query()->where('invoice_id', $invoice->invoice_id)->count());
        $this->assertEquals(0, InvoiceAmount::query()->where('invoice_id', $invoice->invoice_id)->count());
    }

    #[Test]
    public function it_marks_invoice_as_viewed_when_sent(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 2, // Sent
            'invoice_number'           => 'INV-VIEW-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-view-001',
        ]);

        $result = $this->service->markViewed($invoice->invoice_id);

        $this->assertTrue($result);
        $this->assertEquals(3, Invoice::query()->find($invoice->invoice_id)->invoice_status_id);
    }

    #[Test]
    public function it_does_not_mark_draft_invoice_as_viewed(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1, // Draft
            'invoice_number'           => 'INV-VIEW-002',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-view-002',
        ]);

        $result = $this->service->markViewed($invoice->invoice_id);

        $this->assertFalse($result);
        $this->assertEquals(1, Invoice::query()->find($invoice->invoice_id)->invoice_status_id);
    }

    #[Test]
    public function it_marks_draft_invoice_as_sent(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1, // Draft
            'invoice_number'           => 'INV-SENT-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-sent-001',
        ]);

        $result = $this->service->markSent($invoice->invoice_id);

        $this->assertTrue($result);
        $this->assertEquals(2, Invoice::query()->find($invoice->invoice_id)->invoice_status_id);
    }

    #[Test]
    public function it_does_not_mark_paid_invoice_as_sent(): void
    {
        $this->markTestIncomplete();
        $invoice = Invoice::query()->create([
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 4, // Paid
            'invoice_number'           => 'INV-SENT-002',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_modified'    => '2024-01-01',
            'invoice_date_due'         => '2024-01-15',
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-sent-002',
        ]);

        $result = $this->service->markSent($invoice->invoice_id);

        $this->assertFalse($result);
        $this->assertEquals(4, Invoice::query()->find($invoice->invoice_id)->invoice_status_id);
    }

    #[Test]
    public function it_detects_overdue_invoice(): void
    {
        $this->markTestIncomplete();
        $invoice = new Invoice([
            'invoice_status_id' => 2, // Sent
            'invoice_date_due'  => date('Y-m-d', strtotime('-10 days')),
        ]);

        $result = $this->service->isOverdue($invoice);

        $this->assertTrue($result);
    }

    #[Test]
    public function it_detects_non_overdue_invoice(): void
    {
        $this->markTestIncomplete();
        $invoice = new Invoice([
            'invoice_status_id' => 2, // Sent
            'invoice_date_due'  => date('Y-m-d', strtotime('+10 days')),
        ]);

        $result = $this->service->isOverdue($invoice);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_does_not_mark_draft_invoice_as_overdue(): void
    {
        $this->markTestIncomplete();
        $invoice = new Invoice([
            'invoice_status_id' => 1, // Draft
            'invoice_date_due'  => date('Y-m-d', strtotime('-10 days')),
        ]);

        $result = $this->service->isOverdue($invoice);

        $this->assertFalse($result);
    }

    #[Test]
    public function it_does_not_mark_paid_invoice_as_overdue(): void
    {
        $this->markTestIncomplete();
        $invoice = new Invoice([
            'invoice_status_id' => 4, // Paid
            'invoice_date_due'  => date('Y-m-d', strtotime('-10 days')),
        ]);

        $result = $this->service->isOverdue($invoice);

        $this->assertFalse($result);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_days_overdue(): void
    {
        $this->markTestIncomplete();
        $invoice = new Invoice([
            'invoice_status_id' => 2, // Sent
            'invoice_date_due'  => date('Y-m-d', strtotime('-15 days')),
        ]);

        $result = $this->service->getDaysOverdue($invoice);

        $this->assertEquals(15, $result);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_days_overdue_for_non_overdue_invoice(): void
    {
        $this->markTestIncomplete();
        $invoice = new Invoice([
            'invoice_status_id' => 2, // Sent
            'invoice_date_due'  => date('Y-m-d', strtotime('+5 days')),
        ]);

        $result = $this->service->getDaysOverdue($invoice);

        $this->assertEquals(0, $result);
    }

    #[Group('relationships')]
    #[Test]
    public function it_filters_invoices_by_status(): void
    {
        /** Arrange */
        $client = \Modules\Crm\Models\Client::factory()->create();
        Invoice::factory()->create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1, // Draft
        ]);
        Invoice::factory()->create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 4, // Paid
        ]);

        /** Act */
        $draftResult = $this->service->getAllWithRelations(['client'], 'draft');
        $paidResult  = $this->service->getAllWithRelations(['client'], 'paid');

        /* Assert */
        $this->assertGreaterThanOrEqual(1, $draftResult->total());
        $this->assertGreaterThanOrEqual(1, $paidResult->total());
    }

    #[Group('queries')]
    #[Test]
    public function it_gets_invoices_by_client_id(): void
    {
        /** Arrange */
        $client1  = \Modules\Crm\Models\Client::factory()->create();
        $client2  = \Modules\Crm\Models\Client::factory()->create();
        $invoice1 = Invoice::factory()->create(['client_id' => $client1->client_id]);
        $invoice2 = Invoice::factory()->create(['client_id' => $client1->client_id]);
        $invoice3 = Invoice::factory()->create(['client_id' => $client2->client_id]);

        /** Act */
        $result = $this->service->getByClientId($client1->client_id);

        /* Assert */
        $this->assertCount(2, $result);
        $this->assertTrue($result->contains('invoice_id', $invoice1->invoice_id));
        $this->assertTrue($result->contains('invoice_id', $invoice2->invoice_id));
        $this->assertFalse($result->contains('invoice_id', $invoice3->invoice_id));
    }
}

#[CoversClass(InvoicesRecurringService::class)]
class InvoicesRecurringServiceTest extends AbstractServiceTestCase
{
    private InvoicesRecurringService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoicesRecurringService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('recur_start_date', $rules);
        $this->assertArrayHasKey('recur_end_date', $rules);
        $this->assertArrayHasKey('recur_frequency', $rules);
        $this->assertArrayHasKey('recur_next_date', $rules);
    }

    #[Test]
    public function it_validates_invoice_id_as_required_integer(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('required', $rules['invoice_id']);
        $this->assertStringContainsString('integer', $rules['invoice_id']);
    }

    #[Test]
    public function it_validates_recur_start_date_as_required_date(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('required', $rules['recur_start_date']);
        $this->assertStringContainsString('date', $rules['recur_start_date']);
    }

    #[Test]
    public function it_validates_recur_end_date_as_nullable_date(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('nullable', $rules['recur_end_date']);
        $this->assertStringContainsString('date', $rules['recur_end_date']);
    }

    #[Test]
    public function it_validates_recur_frequency_as_required_string(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('required', $rules['recur_frequency']);
        $this->assertStringContainsString('string', $rules['recur_frequency']);
    }

    #[Test]
    public function it_validates_recur_next_date_as_nullable_date(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('nullable', $rules['recur_next_date']);
        $this->assertStringContainsString('date', $rules['recur_next_date']);
    }

    #[Test]
    public function it_provides_all_required_validation_keys(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $expectedKeys = [
            'invoice_id',
            'recur_start_date',
            'recur_end_date',
            'recur_frequency',
            'recur_next_date',
        ];

        foreach ($expectedKeys as $key) {
            $this->assertArrayHasKey($key, $rules);
        }
    }

    #[Group('relationships')]
    #[Test]
    public function it_gets_all_recurring_invoices_with_relations_paginated(): void
    {
        /** Arrange */
        $client  = \Modules\Crm\Models\Client::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id' => $client->client_id,
        ]);

        \Modules\Invoices\Models\InvoicesRecurring::factory()->count(3)->create([
            'invoice_id' => $invoice->invoice_id,
            'client_id'  => $client->client_id,
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations();

        /* Assert */
        $this->assertGreaterThanOrEqual(3, $result->total());
        $this->assertTrue($result->first()->relationLoaded('invoice'));
        $this->assertTrue($result->first()->relationLoaded('client'));
    }

    #[Group('relationships')]
    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        /** Arrange */
        $client  = \Modules\Crm\Models\Client::factory()->create();
        $invoice = \Modules\Invoices\Models\Invoice::factory()->create([
            'client_id' => $client->client_id,
        ]);

        \Modules\Invoices\Models\InvoicesRecurring::factory()->count(10)->create([
            'invoice_id' => $invoice->invoice_id,
            'client_id'  => $client->client_id,
        ]);

        /** Act */
        $result = $this->service->getAllWithRelations(['invoice'], 5);

        /* Assert */
        $this->assertEquals(5, $result->perPage());
    }
}

#[CoversClass(InvoiceSumexService::class)]
class InvoiceSumexServiceTest extends AbstractServiceTestCase
{
    private InvoiceSumexService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new InvoiceSumexService();
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('sumex_invoice', $rules);
        $this->assertArrayHasKey('sumex_reason', $rules);
        $this->assertArrayHasKey('sumex_diagnosis', $rules);
        $this->assertArrayHasKey('sumex_observations', $rules);
        $this->assertArrayHasKey('sumex_treatmentstart', $rules);
        $this->assertArrayHasKey('sumex_treatmentend', $rules);
        $this->assertArrayHasKey('sumex_casedate', $rules);
        $this->assertArrayHasKey('sumex_casenumber', $rules);
    }

    #[Test]
    public function it_validates_sumex_invoice_as_required_integer(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('required', $rules['sumex_invoice']);
        $this->assertStringContainsString('integer', $rules['sumex_invoice']);
    }

    #[Test]
    public function it_validates_optional_fields_as_nullable(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('nullable', $rules['sumex_reason']);
        $this->assertStringContainsString('nullable', $rules['sumex_diagnosis']);
        $this->assertStringContainsString('nullable', $rules['sumex_observations']);
        $this->assertStringContainsString('nullable', $rules['sumex_casenumber']);
    }

    #[Test]
    public function it_validates_date_fields(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('date', $rules['sumex_treatmentstart']);
        $this->assertStringContainsString('date', $rules['sumex_treatmentend']);
        $this->assertStringContainsString('date', $rules['sumex_casedate']);
    }

    #[Test]
    public function it_validates_string_fields(): void
    {
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertStringContainsString('string', $rules['sumex_diagnosis']);
        $this->assertStringContainsString('string', $rules['sumex_observations']);
        $this->assertStringContainsString('string', $rules['sumex_casenumber']);
    }
}

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
        $this->markTestIncomplete();
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('include_item_tax', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_not_in_legacy_mode(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '0');

        $data = [
            'invoice_id'       => 1,
            'tax_rate_id'      => 1,
            'include_item_tax' => 0,
        ];

        $result = $this->service->saveTaxRate($data);

        $this->assertNull($result);
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_tax_rate_in_legacy_mode(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '1');

        $data = [
            'invoice_id'               => 1,
            'tax_rate_id'              => 1,
            'include_item_tax'         => 0,
            'invoice_tax_rate_percent' => 10.0,
            'invoice_tax_rate_amount'  => 0.0,
        ];

        $result = $this->service->saveTaxRate($data);

        $this->assertInstanceOf(InvoiceTaxRate::class, $result);
        $this->assertEquals(1, $result->invoice_id);
        $this->assertEquals(1, $result->tax_rate_id);
        $this->assertEquals(0, $result->include_item_tax);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_tax_rate_in_legacy_mode(): void
    {
        $this->markTestIncomplete();
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

        $result = $this->service->saveTaxRate($data);

        $this->assertEquals($existingTaxRate->invoice_tax_rate_id, $result->invoice_tax_rate_id);
        $this->assertEquals(2, $result->tax_rate_id);
        $this->assertEquals(1, $result->include_item_tax);
        $this->assertEquals(20.0, (float) $result->invoice_tax_rate_percent);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_include_item_tax_flag(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '1');

        $data1 = [
            'invoice_id'               => 1,
            'tax_rate_id'              => 1,
            'include_item_tax'         => 1,
            'invoice_tax_rate_percent' => 10.0,
            'invoice_tax_rate_amount'  => 0.0,
        ];

        $result1 = $this->service->saveTaxRate($data1);
        $this->assertEquals(1, $result1->include_item_tax);

        $data2 = [
            'invoice_id'               => 2,
            'tax_rate_id'              => 2,
            'include_item_tax'         => 0,
            'invoice_tax_rate_percent' => 15.0,
            'invoice_tax_rate_amount'  => 0.0,
        ];

        $result2 = $this->service->saveTaxRate($data2);
        $this->assertEquals(0, $result2->include_item_tax);
    }
}

