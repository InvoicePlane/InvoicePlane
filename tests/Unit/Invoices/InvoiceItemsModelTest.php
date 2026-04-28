<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Invoices;

use Mdl_Items;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

#[CoversClass(Mdl_Items::class)]
class InvoiceItemsModelTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    private $service;

    private $itemAmountsService;

    private $invoiceAmountsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemAmountsService    = $this->createMock(ItemAmountsService::class);
        $this->invoiceAmountsService = $this->createMock(InvoiceAmountsService::class);

        $this->model = new ItemsService(
            $this->itemAmountsService,
            $this->invoiceAmountsService
        );
    }

    public function test_get_by_invoice_id_returns_items(): void
    {
        $invoice_id = 1;
        $this->seedModelMany('Item', 3, ['invoice_id' => $invoice_id]);
        $this->seedModelMany('Item', 2, ['invoice_id' => 2]);

        $results = $this->model->getByInvoiceId($invoice_id);
        $this->assertCount(3, $results);
    }

    public function test_get_by_invoice_id_returns_collection(): void
    {
        $results = $this->model->getByInvoiceId(1);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
    }

    public function test_validation_rules_requires_invoice_id(): void
    {
        $rules = $this->model->validationRules();
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertEquals('required', $rules['invoice_id']['rules']);
    }

    public function test_delete_removes_item_and_amounts(): void
    {
        $item = $this->seedModel('Item', ['invoice_id' => 1]);
        $this->seedModel('ItemAmount', ['item_id' => $item->item_id]);

        $this->invoiceAmountsService
            ->expects($this->once())
            ->method('getGlobalDiscount')
            ->willReturn(['item' => 0]);

        $this->invoiceAmountsService
            ->expects($this->once())
            ->method('calculate');

        $result = $this->model->delete($item->item_id);
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_invoice_items', ['item_id' => $item->item_id]);
    }

    public function test_delete_returns_false_for_nonexistent_item(): void
    {
        $this->markTestIncomplete('weak test');
        $result = $this->model->delete(99999);
        $this->assertFalse($result);
    }

    public function test_service_has_correct_table(): void
    {
        $this->assertEquals('ip_invoice_items', $this->model->table);
    }
}
