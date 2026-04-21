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

        Item::query()->create([
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
        $this->assertEquals(0, Item::query()->where('invoice_id', $invoice->invoice_id)->count());
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
}
