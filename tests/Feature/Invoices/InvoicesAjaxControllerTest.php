<?php

namespace Tests\Feature\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Modules\Invoices\Controllers\InvoicesAjaxController;
use Modules\Invoices\Models\Invoice;
use Modules\Invoices\Models\Item;
use Modules\Invoices\Models\InvoiceTaxRate;
use Modules\Invoices\Models\InvoicesRecurring;
use Modules\Crm\Models\Client;
use Modules\Users\Models\User;

/**
 * Test suite for InvoicesAjaxController
 *
 * Tests all AJAX operations for invoice management including creation,
 * saving, copying, and conversion operations.
 */
#[CoversClass(InvoicesAjaxController::class)]
class InvoicesAjaxControllerTest extends TestCase
{
    private InvoicesAjaxController $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new InvoicesAjaxController();
    }

    /**
     * Test saving invoice with items and custom fields returns success
     */
    #[Test]
    public function it_saves_invoice_with_items_and_returns_success(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $items = [
            [
                'item_id' => null,
                'item_name' => 'Test Item 1',
                'item_quantity' => 2,
                'item_price' => 100.00,
                'item_discount_amount' => 0,
            ],
            [
                'item_id' => null,
                'item_name' => 'Test Item 2',
                'item_quantity' => 1,
                'item_price' => 50.00,
                'item_discount_amount' => 0,
            ],
        ];

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'items' => json_encode($items),
            'invoice_discount_percent' => 0,
            'invoice_discount_amount' => 0,
        ]);

        /** Act */
        $response = $this->controller->save();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $this->assertEquals(2, Item::where('invoice_id', $invoice->invoice_id)->count());
    }

    /**
     * Test saving invoice returns validation errors for invalid data
     */
    #[Test]
    public function it_returns_validation_errors_when_saving_invalid_invoice(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'items' => json_encode([]),
            'invoice_date_created' => 'invalid-date',
        ]);

        /** Act */
        $response = $this->controller->save();

        /** Assert */
        $this->assertEquals(0, $response['success']);
        $this->assertArrayHasKey('validation_errors', $response);
    }

    /**
     * Test preventing both discount types when saving invoice
     */
    #[Test]
    public function it_prevents_both_discount_types_when_saving_invoice(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $items = [
            [
                'item_name' => 'Test Item',
                'item_quantity' => 1,
                'item_price' => 100.00,
            ],
        ];

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'items' => json_encode($items),
            'invoice_discount_percent' => 10,
            'invoice_discount_amount' => 20, // Should be cleared
        ]);

        /** Act */
        $response = $this->controller->save();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $savedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertEquals(10, $savedInvoice->invoice_discount_percent);
        $this->assertEquals(0, $savedInvoice->invoice_discount_amount);
    }

    /**
     * Test returning error when item has quantity but no name
     */
    #[Test]
    public function it_returns_error_when_item_has_quantity_but_no_name(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $items = [
            [
                'item_name' => '',
                'item_quantity' => 5,
                'item_price' => 100.00,
            ],
        ];

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'items' => json_encode($items),
        ]);

        /** Act */
        $response = $this->controller->save();

        /** Assert */
        $this->assertEquals(0, $response['success']);
        $this->assertArrayHasKey('validation_errors', $response);
        $this->assertArrayHasKey('item_name', $response['validation_errors']);
    }

    /**
     * Test saving invoice tax rate in legacy calculation mode
     */
    #[Test]
    public function it_saves_invoice_tax_rate_in_legacy_calculation_mode(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $taxRate = TaxRate::factory()->create(['tax_rate_percent' => 20]);

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'tax_rate_id' => $taxRate->tax_rate_id,
            'include_item_tax' => 1,
        ]);

        /** Act */
        $response = $this->controller->saveInvoiceTaxRate();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $savedTax = InvoiceTaxRate::where('invoice_id', $invoice->invoice_id)
            ->where('tax_rate_id', $taxRate->tax_rate_id)
            ->first();
        $this->assertNotNull($savedTax);
        $this->assertEquals(1, $savedTax->include_item_tax);
    }

    /**
     * Test deleting invoice item and recalculating invoice
     */
    #[Test]
    public function it_deletes_invoice_item_and_returns_success(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $item = Item::factory()->create(['invoice_id' => $invoice->invoice_id]);

        request()->merge(['item_id' => $item->item_id]);

        /** Act */
        $response = $this->controller->deleteItem($invoice->invoice_id);

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $this->assertNull(Item::find($item->item_id));
    }

    /**
     * Test returning failure when deleting item for non-existent invoice
     */
    #[Test]
    public function it_returns_failure_when_deleting_item_for_non_existent_invoice(): void
    {
        /** Arrange */
        request()->merge(['item_id' => 99999]);

        /** Act */
        $response = $this->controller->deleteItem(99999);

        /** Assert */
        $this->assertEquals(0, $response['success']);
    }

    /**
     * Test returning invoice item data when getting item
     */
    #[Test]
    public function it_returns_invoice_item_data_when_getting_item(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $item = Item::factory()->create([
            'invoice_id' => $invoice->invoice_id,
            'item_name' => 'Test Item',
            'item_price' => 100.00,
        ]);

        request()->merge(['item_id' => $item->item_id]);

        /** Act */
        $response = $this->controller->getItem();

        /** Assert */
        $this->assertIsArray($response);
        $this->assertEquals('Test Item', $response['item_name']);
        $this->assertEquals(100.00, $response['item_price']);
    }

    /**
     * Test returning empty array when getting non-existent item
     */
    #[Test]
    public function it_returns_empty_array_when_getting_non_existent_item(): void
    {
        /** Arrange */
        request()->merge(['item_id' => 99999]);

        /** Act */
        $response = $this->controller->getItem();

        /** Assert */
        $this->assertIsArray($response);
        $this->assertEmpty($response);
    }

    /**
     * Test copying invoice with all items and tax rates
     */
    #[Test]
    public function it_copies_invoice_with_all_items_and_tax_rates(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $sourceInvoice = Invoice::factory()->draft()->create();
        Item::factory()->count(3)->create(['invoice_id' => $sourceInvoice->invoice_id]);

        request()->merge([
            'invoice_id' => $sourceInvoice->invoice_id,
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
            'invoice_date_created' => date('Y-m-d'),
            'invoice_change_client' => 0,
        ]);

        /** Act */
        $response = $this->controller->copyInvoice();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $this->assertArrayHasKey('invoice_id', $response);
        $newInvoice = Invoice::find($response['invoice_id']);
        $this->assertNotNull($newInvoice);
        $this->assertEquals($client->client_id, $newInvoice->client_id);
        $this->assertEquals(3, Item::where('invoice_id', $newInvoice->invoice_id)->count());
    }

    /**
     * Test changing invoice user and returning success
     */
    #[Test]
    public function it_changes_invoice_user_and_returns_success(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $newUser = User::factory()->create();

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'user_id' => $newUser->user_id,
        ]);

        /** Act */
        $response = $this->controller->changeUser();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $updatedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertEquals($newUser->user_id, $updatedInvoice->user_id);
    }

    /**
     * Test returning error when changing to non-existent user
     */
    #[Test]
    public function it_returns_error_when_changing_to_non_existent_user(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'user_id' => 99999,
        ]);

        /** Act */
        $response = $this->controller->changeUser();

        /** Assert */
        $this->assertEquals(0, $response['success']);
        $this->assertArrayHasKey('error', $response);
    }

    /**
     * Test changing invoice client and returning success
     */
    #[Test]
    public function it_changes_invoice_client_and_returns_success(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $newClient = Client::factory()->create();

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'client_id' => $newClient->client_id,
        ]);

        /** Act */
        $response = $this->controller->changeClient();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $updatedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertEquals($newClient->client_id, $updatedInvoice->client_id);
    }

    /**
     * Test creating new invoice and returning invoice ID
     */
    #[Test]
    public function it_creates_new_invoice_and_returns_invoice_id(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();

        request()->merge([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
            'invoice_date_created' => date('Y-m-d'),
        ]);

        /** Act */
        $response = $this->controller->create();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $this->assertArrayHasKey('invoice_id', $response);
        $invoice = Invoice::find($response['invoice_id']);
        $this->assertNotNull($invoice);
        $this->assertEquals($client->client_id, $invoice->client_id);
        $this->assertEquals(1, $invoice->invoice_status_id); // Draft
    }

    /**
     * Test creating recurring invoice and returning ID
     */
    #[Test]
    public function it_creates_recurring_invoice_and_returns_id(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();

        request()->merge([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
            'invoice_group_id' => 1,
            'recur_start_date' => date('Y-m-d'),
            'recur_end_date' => date('Y-m-d', strtotime('+1 year')),
            'recur_frequency' => '1M',
        ]);

        /** Act */
        $response = $this->controller->createRecurring();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $this->assertArrayHasKey('invoice_recurring_id', $response);
        $recurring = InvoicesRecurring::find($response['invoice_recurring_id']);
        $this->assertNotNull($recurring);
        $this->assertEquals('1M', $recurring->recur_frequency);
    }

    /**
     * Test calculating recurring start date based on frequency
     */
    #[Test]
    public function it_calculates_recurring_start_date_based_on_frequency(): void
    {
        /** Arrange */
        request()->merge(['recur_frequency' => '1M']);

        /** Act */
        $response = $this->controller->getRecurStartDate();

        /** Assert */
        $this->assertArrayHasKey('recur_start_date', $response);
        $expectedDate = date('Y-m-d', strtotime('+1 month'));
        $this->assertEquals($expectedDate, $response['recur_start_date']);
    }

    /**
     * Test creating credit invoice from existing invoice
     */
    #[Test]
    public function it_creates_credit_invoice_from_existing_invoice(): void
    {
        /** Arrange */
        $sourceInvoice = Invoice::factory()->paid()->create();
        Item::factory()->count(2)->create(['invoice_id' => $sourceInvoice->invoice_id]);

        request()->merge([
            'invoice_id' => $sourceInvoice->invoice_id,
            'invoice_date_created' => date('Y-m-d'),
        ]);

        /** Act */
        $response = $this->controller->createCredit();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $this->assertArrayHasKey('invoice_id', $response);
        $creditInvoice = Invoice::find($response['invoice_id']);
        $this->assertNotNull($creditInvoice);
        $this->assertEquals($sourceInvoice->client_id, $creditInvoice->client_id);
    }

    /**
     * Test loading copy invoice modal with correct view data
     */
    #[Test]
    public function it_loads_copy_invoice_modal_with_clients_and_users(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        Client::factory()->count(3)->create();
        User::factory()->count(2)->create();

        request()->merge(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->controller->modalCopyInvoice();

        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('invoice', $viewData);
        $this->assertArrayHasKey('clients', $viewData);
        $this->assertArrayHasKey('users', $viewData);
        $this->assertCount(3, $viewData['clients']);
    }

    /**
     * Test loading create invoice modal with clients list
     */
    #[Test]
    public function it_loads_create_invoice_modal_with_clients_list(): void
    {
        /** Arrange */
        Client::factory()->count(5)->create();
        User::factory()->count(2)->create();

        /** Act */
        $response = $this->controller->modalCreateInvoice();

        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('clients', $viewData);
        $this->assertArrayHasKey('users', $viewData);
        $this->assertCount(5, $viewData['clients']);
    }

    /**
     * Test loading change user modal with users list
     */
    #[Test]
    public function it_loads_change_user_modal_with_users_list(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        User::factory()->count(3)->create();

        request()->merge(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->controller->modalChangeUser();

        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('invoice', $viewData);
        $this->assertArrayHasKey('users', $viewData);
        $this->assertCount(3, $viewData['users']);
    }

    /**
     * Test loading change client modal with clients list
     */
    #[Test]
    public function it_loads_change_client_modal_with_clients_list(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        Client::factory()->count(4)->create();

        request()->merge(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->controller->modalChangeClient();

        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('invoice', $viewData);
        $this->assertArrayHasKey('clients', $viewData);
        $this->assertCount(4, $viewData['clients']);
    }

    /**
     * Test loading create recurring modal with form data
     */
    #[Test]
    public function it_loads_create_recurring_modal_with_form_data(): void
    {
        /** Arrange */
        Client::factory()->count(2)->create();
        User::factory()->count(2)->create();

        /** Act */
        $response = $this->controller->modalCreateRecurring();

        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('clients', $viewData);
        $this->assertArrayHasKey('users', $viewData);
    }

    /**
     * Test loading create credit modal with invoice data
     */
    #[Test]
    public function it_loads_create_credit_modal_with_invoice_data(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->paid()->create();

        request()->merge(['invoice_id' => $invoice->invoice_id]);

        /** Act */
        $response = $this->controller->modalCreateCredit();

        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        $this->assertArrayHasKey('invoice', $viewData);
        $this->assertEquals($invoice->invoice_id, $viewData['invoice']->invoice_id);
    }

    /**
     * Test preserving item details when saving invoice
     */
    #[Test]
    public function it_preserves_item_details_when_saving_invoice(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $items = [
            [
                'item_id' => null,
                'item_name' => 'Consulting Services',
                'item_description' => 'Full project consultation',
                'item_quantity' => 10,
                'item_price' => 150.00,
                'item_discount_amount' => 50.00,
            ],
        ];

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'items' => json_encode($items),
            'invoice_discount_percent' => 0,
            'invoice_discount_amount' => 0,
        ]);

        /** Act */
        $response = $this->controller->save();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $savedItem = Item::where('invoice_id', $invoice->invoice_id)->first();
        $this->assertEquals('Consulting Services', $savedItem->item_name);
        $this->assertEquals('Full project consultation', $savedItem->item_description);
        $this->assertEquals(10, $savedItem->item_quantity);
        $this->assertEquals(150.00, $savedItem->item_price);
        $this->assertEquals(50.00, $savedItem->item_discount_amount);
    }

    /**
     * Test handling global discount distribution across items
     */
    #[Test]
    public function it_distributes_global_discount_across_items_proportionally(): void
    {
        /** Arrange */
        $invoice = Invoice::factory()->draft()->create();
        $items = [
            [
                'item_name' => 'Item 1',
                'item_quantity' => 1,
                'item_price' => 100.00,
            ],
            [
                'item_name' => 'Item 2',
                'item_quantity' => 1,
                'item_price' => 50.00,
            ],
        ];

        request()->merge([
            'invoice_id' => $invoice->invoice_id,
            'items' => json_encode($items),
            'invoice_discount_percent' => 0,
            'invoice_discount_amount' => 30.00, // 20% global discount
        ]);

        /** Act */
        $response = $this->controller->save();

        /** Assert */
        $this->assertEquals(1, $response['success']);
        $invoice->refresh();
        $this->assertEquals(30.00, $invoice->invoice_discount_amount);
        /** Verify proportional distribution */
        $itemsTotal = Item::where('invoice_id', $invoice->invoice_id)
            ->sum('item_subtotal');
        $this->assertEquals(120.00, $itemsTotal); // 150 - 30 = 120
    }
}
