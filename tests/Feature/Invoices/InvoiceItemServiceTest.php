<?php

namespace Tests\Feature\Invoices;

use Illuminate\Support\Facades\DB;
use Modules\Core\Models\Setting;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceAmount;
use Modules\Invoices\Models\Item;
use Modules\Invoices\Models\ItemAmount;
use Modules\Invoices\Services\InvoiceAmountService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(InvoiceAmountService::class)]

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

        $this->assertInstanceOf(Item::class, $item);
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

        $existingItem = Item::query()->create([
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

        $item = Item::query()->create([
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
        $this->assertNull(Item::query()->find($item->item_id));
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

        $item1 = Item::query()->create([
            'invoice_id'           => $invoice->invoice_id,
            'item_name'            => 'Item 1',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);

        $item2 = Item::query()->create([
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
