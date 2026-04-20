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

class InvoiceItemsServiceTest extends TestCase
{
    use RefreshDatabase;

    private ItemsService $service;

    private $itemAmountsService;

    private $invoiceAmountsService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itemAmountsService    = $this->createMock(ItemAmountsService::class);
        $this->invoiceAmountsService = $this->createMock(InvoiceAmountsService::class);

        $this->service = new ItemsService(
            $this->itemAmountsService,
            $this->invoiceAmountsService
        );
    }

    public function test_get_by_invoice_id_returns_items(): void
    {
        $invoice_id = 1;
        Item::factory()->count(3)->create(['invoice_id' => $invoice_id]);
        Item::factory()->count(2)->create(['invoice_id' => 2]);

        $results = $this->service->getByInvoiceId($invoice_id);
        $this->assertCount(3, $results);
    }

    public function test_get_by_invoice_id_returns_collection(): void
    {
        $results = $this->service->getByInvoiceId(1);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $results);
    }

    public function test_validation_rules_requires_invoice_id(): void
    {
        $rules = $this->service->validationRules();
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertEquals('required', $rules['invoice_id']['rules']);
    }

    public function test_delete_removes_item_and_amounts(): void
    {
        $item = Item::factory()->create(['invoice_id' => 1]);
        ItemAmount::factory()->create(['item_id' => $item->item_id]);

        $this->invoiceAmountsService
            ->expects($this->once())
            ->method('getGlobalDiscount')
            ->willReturn(['item' => 0]);

        $this->invoiceAmountsService
            ->expects($this->once())
            ->method('calculate');

        $result = $this->service->delete($item->item_id);
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_invoice_items', ['item_id' => $item->item_id]);
    }

    public function test_delete_returns_false_for_nonexistent_item(): void
    {
        $result = $this->service->delete(99999);
        $this->assertFalse($result);
    }

    public function test_service_has_correct_table(): void
    {
        $this->assertEquals('ip_invoice_items', $this->service->table);
    }
}

class InvoicesServiceTest extends TestCase
{
    use RefreshDatabase;

    private InvoicesService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(InvoicesService::class);
    }

    #[Test]
    public function it_attaches_payments_to_invoice(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        Payment::create([
            'invoice_id'     => $invoice->invoice_id,
            'payment_amount' => 100.00,
            'payment_date'   => now(),
        ]);

        Payment::create([
            'invoice_id'     => $invoice->invoice_id,
            'payment_amount' => 50.00,
            'payment_date'   => now(),
        ]);

        // Act
        $result = $this->service->getPayments($invoice);

        // Assert
        $this->assertNotNull($result->payments);
        $this->assertCount(2, $result->payments);
    }

    #[Test]
    public function it_returns_null_payments_when_invoice_has_no_payments(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1,
        ]);

        // Act
        $result = $this->service->getPayments($invoice);

        // Assert
        $this->assertNull($result->payments);
    }

    #[Test]
    public function it_marks_invoice_as_viewed_when_status_is_sent(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 2, // Sent status
        ]);

        // Act
        $this->service->markViewed($invoice->invoice_id);

        // Assert
        $invoice->refresh();
        $this->assertEquals(3, $invoice->invoice_status_id); // Viewed status
    }

    #[Test]
    public function it_does_not_change_status_when_invoice_is_not_sent(): void
    {
        // Arrange
        $client = tmpClient::create([
            'client_name'   => 'Test Client',
            'client_active' => 1,
        ]);

        $invoice = Invoice::create([
            'client_id'         => $client->client_id,
            'invoice_status_id' => 1, // Draft status
        ]);

        // Act
        $this->service->markViewed($invoice->invoice_id);

        // Assert
        $invoice->refresh();
        $this->assertEquals(1, $invoice->invoice_status_id); // Should remain draft
    }

    #[Test]
    public function it_filters_invoices_by_client(): void
    {
        // Arrange
        $client1 = tmpClient::create([
            'client_name'   => 'Client 1',
            'client_active' => 1,
        ]);

        $client2 = tmpClient::create([
            'client_name'   => 'Client 2',
            'client_active' => 1,
        ]);

        Invoice::create([
            'client_id'         => $client1->client_id,
            'invoice_status_id' => 1,
        ]);

        Invoice::create([
            'client_id'         => $client1->client_id,
            'invoice_status_id' => 1,
        ]);

        Invoice::create([
            'client_id'         => $client2->client_id,
            'invoice_status_id' => 1,
        ]);

        // Act
        $result = $this->service->byClient($client1->client_id);

        // Assert
        $this->assertInstanceOf(InvoicesService::class, $result);
    }
}

