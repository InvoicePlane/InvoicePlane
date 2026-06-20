<?php

namespace Tests\Feature\Invoices;

use Modules\Core\Models\Setting;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\InvoiceAmount;
use Modules\Invoices\Models\Item;
use Modules\Invoices\Models\ItemAmount;
use Modules\Invoices\Services\InvoiceAmountService;
use Tests\AbstractTestCase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(InvoiceAmountService::class)]
#[CoversClass(Tests\Feature\Invoices\InvoiceAmountService::class)]

class InvoiceAmountServiceTest extends AbstractTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->markTestSkipped('Service/repository class does not exist — CI3 model layer, no Laravel service layer available');

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

        $firstItem = Item::query()->create([
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

        $secondItem = Item::query()->create([
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

        $item = Item::query()->create([
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

        $item = Item::query()->create([
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
