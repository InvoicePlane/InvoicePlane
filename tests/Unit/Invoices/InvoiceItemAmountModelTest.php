<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoice_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoice_Amounts::class)]

class InvoiceItemAmountModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->model = new InvoiceItemAmountService();

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

        $item = Item::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => $taxRate->tax_rate_id,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 5,
        ]);

        $globalDiscount = [];
        $this->model->calculate($item->item_id, $globalDiscount);

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

        $item = Item::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Test Item',
            'item_quantity'        => 3,
            'item_price'           => 50,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $globalDiscount = [];
        $this->model->calculate($item->item_id, $globalDiscount);

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

        $item = Item::query()->create([
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

        $this->model->calculate($item->item_id, $globalDiscount);

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

        $item = Item::query()->create([
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

        $this->model->calculate($item->item_id, $globalDiscount);

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

        $item = Item::query()->create([
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

        $this->model->calculate($item->item_id, $globalDiscount);

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

        $item = Item::query()->create([
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
        $this->model->calculate($item->item_id, $globalDiscount);

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

        $item1 = Item::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Item 1',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $item2 = Item::query()->create([
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

        $this->model->calculate($item1->item_id, $globalDiscount);
        $this->model->calculate($item2->item_id, $globalDiscount);

        // Should accumulate: 10% of 100 + 10% of 200 = 10 + 20 = 30
        $this->assertEquals(30.0, $globalDiscount['item']);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_fractional_quantities_and_prices(): void
    {
        $this->markTestIncomplete();
        Setting::setValue('legacy_calculation', '1');

        $item = Item::query()->create([
            'invoice_id'           => 1,
            'item_tax_rate_id'     => null,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2.5,
            'item_price'           => 33.33,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $globalDiscount = [];
        $this->model->calculate($item->item_id, $globalDiscount);

        $itemAmount = ItemAmount::query()->where('item_id', $item->item_id)->first();

        $this->assertNotNull($itemAmount);
        $this->assertEquals(83.325, (float) $itemAmount->item_subtotal); // 2.5 * 33.33
    }
}
