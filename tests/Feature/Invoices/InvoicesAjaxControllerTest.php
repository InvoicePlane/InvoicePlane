<?php

namespace Tests\Feature\Invoices;

use Modules\Crm\Controllers\InvoicesController as GuestInvoicesController;
use Modules\Invoices\Models\Invoice;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\Core\FeatureTestCase;

/**
 * InvoicesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal invoice viewing.
 */
#[CoversClass(GuestInvoicesController::class)]

class InvoicesAjaxControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test creating new invoice and returning invoice ID.
     *
     * JSON Payload:
     * {
     *   "client_id": 1,
     *   "user_id": 1,
     *   "invoice_date_created": "2024-01-01"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_invoice_and_returns_invoice_id(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        /**
         * {
         *     "client_id": 1,
         *     "user_id": 1,
         *     "invoice_date_created": "2024-01-01"
         * }.
         */
        $payload = [
            'client_id'            => $client->client_id,
            'user_id'              => $user->user_id,
            'invoice_date_created' => '2024-01-01',
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.create'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('invoice_id', $data);
        $invoice = Invoice::find($data['invoice_id']);
        $this->assertNotNull($invoice);
        $this->assertEquals($client->client_id, $invoice->client_id);
        $this->assertEquals(1, $invoice->invoice_status_id); // Draft
    }

    /**
     * Test saving invoice with items and custom fields returns success.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "items": "[{\"item_id\":null,\"item_name\":\"Test Item 1\",\"item_quantity\":2,\"item_price\":100.00,\"item_discount_amount\":0},{\"item_id\":null,\"item_name\":\"Test Item 2\",\"item_quantity\":1,\"item_price\":50.00,\"item_discount_amount\":0}]",
     *   "invoice_discount_percent": 0,
     *   "invoice_discount_amount": 0,
     *   "invoice_number": "INV-001",
     *   "invoice_date_created": "2024-01-01",
     *   "invoice_date_due": "2024-01-31",
     *   "invoice_status_id": 1
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_saves_invoice_with_items_and_returns_success(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $items   = [
            [
                'item_id'              => null,
                'item_name'            => 'Test Item 1',
                'item_quantity'        => 2,
                'item_price'           => 100.00,
                'item_discount_amount' => 0,
            ],
            [
                'item_id'              => null,
                'item_name'            => 'Test Item 2',
                'item_quantity'        => 1,
                'item_price'           => 50.00,
                'item_discount_amount' => 0,
            ],
        ];

        /**
         * {
         *     "invoice_id": 1,
         *     "items": "[{\"item_id\":null,\"item_name\":\"Test Item 1\",\"item_quantity\":2,\"item_price\":100,\"item_discount_amount\":0},{\"item_id\":null,\"item_name\":\"Test Item 2\",\"item_quantity\":1,\"item_price\":50,\"item_discount_amount\":0}]",
         *     "invoice_discount_percent": 0,
         *     "invoice_discount_amount": 0,
         *     "invoice_number": "INV-001",
         *     "invoice_date_created": "2024-01-01",
         *     "invoice_date_due": "2024-01-31",
         *     "invoice_status_id": 1
         * }.
         */
        $payload = [
            'invoice_id'               => $invoice->invoice_id,
            'items'                    => json_encode($items),
            'invoice_discount_percent' => 0,
            'invoice_discount_amount'  => 0,
            'invoice_number'           => 'INV-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_due'         => '2024-01-31',
            'invoice_status_id'        => 1,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertEquals(2, Item::where('invoice_id', $invoice->invoice_id)->count());

        // Verify invoice data was saved
        $invoice->refresh();
        $this->assertEquals('INV-001', $invoice->invoice_number);
    }

    /**
     * Test updating existing invoice with new data.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "items": "[{\"item_id\":1,\"item_name\":\"Updated Item\",\"item_quantity\":3,\"item_price\":150.00,\"item_discount_amount\":0}]",
     *   "invoice_number": "INV-002",
     *   "invoice_date_created": "2024-01-01",
     *   "invoice_date_due": "2024-02-01",
     *   "invoice_status_id": 2,
     *   "invoice_discount_percent": 0,
     *   "invoice_discount_amount": 0
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_updates_invoice_with_modified_items_successfully(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice', ['invoice_number' => 'INV-OLD']);
        $item    = $this->seedModel('Item', [
            'invoice_id'    => $invoice->invoice_id,
            'item_name'     => 'Old Item',
            'item_quantity' => 1,
            'item_price'    => 100.00,
        ]);

        $items = [
            [
                'item_id'              => $item->item_id,
                'item_name'            => 'Updated Item',
                'item_quantity'        => 3,
                'item_price'           => 150.00,
                'item_discount_amount' => 0,
            ],
        ];

        /**
         * {
         *     "invoice_id": 1,
         *     "items": "[{\"item_id\":1,\"item_name\":\"Updated Item\",\"item_quantity\":3,\"item_price\":150,\"item_discount_amount\":0}]",
         *     "invoice_number": "INV-002",
         *     "invoice_date_created": "2024-01-01",
         *     "invoice_date_due": "2024-01-31",
         *     "invoice_status_id": 2,
         *     "invoice_discount_percent": 0,
         *     "invoice_discount_amount": 0
         * }.
         */
        $payload = [
            'invoice_id'               => $invoice->invoice_id,
            'items'                    => json_encode($items),
            'invoice_number'           => 'INV-002',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_due'         => '2024-01-31',
            'invoice_status_id'        => 2,
            'invoice_discount_percent' => 0,
            'invoice_discount_amount'  => 0,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);

        $invoice->refresh();
        $this->assertEquals('INV-002', $invoice->invoice_number);
        $this->assertEquals(2, $invoice->invoice_status_id);

        $item->refresh();
        $this->assertEquals('Updated Item', $item->item_name);
        $this->assertEquals(3, $item->item_quantity);
        $this->assertEquals(150.00, $item->item_price);
    }

    /**
     * Test saving invoice returns validation errors for invalid data.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "items": "[]",
     *   "invoice_date_created": "invalid-date"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_returns_validation_errors_when_saving_invalid_invoice(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');

        /**
         * {
         *     "invoice_id": 1,
         *     "items": "[]",
         *     "invoice_date_created": "invalid-date"
         * }.
         */
        $payload = [
            'invoice_id'           => $invoice->invoice_id,
            'items'                => json_encode([]),
            'invoice_date_created' => 'invalid-date',
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(0, $data['success']);
        $this->assertArrayHasKey('validation_errors', $data);
    }

    /**
     * Test preventing both discount types when saving invoice.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "items": "[{\"item_name\":\"Test Item\",\"item_quantity\":1,\"item_price\":100.00}]",
     *   "invoice_discount_percent": 10,
     *   "invoice_discount_amount": 20
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_prevents_both_discount_types_when_saving_invoice(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $items   = [
            [
                'item_name'     => 'Test Item',
                'item_quantity' => 1,
                'item_price'    => 100.00,
            ],
        ];

        /**
         * {
         *     "invoice_id": 1,
         *     "items": "[{\"item_id\":null,\"item_name\":\"Test Item\",\"item_quantity\":1,\"item_price\":100,\"item_discount_amount\":0}]",
         *     "invoice_discount_percent": 10,
         *     "invoice_discount_amount": 20,
         *     "invoice_number": "INV-001",
         *     "invoice_date_created": "2024-01-01",
         *     "invoice_date_due": "2024-01-31",
         *     "invoice_status_id": 1
         * }.
         */
        $payload = [
            'invoice_id'               => $invoice->invoice_id,
            'items'                    => json_encode($items),
            'invoice_discount_percent' => 10,
            'invoice_discount_amount'  => 20, // Should be cleared
            'invoice_number'           => 'INV-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_due'         => '2024-01-31',
            'invoice_status_id'        => 1,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $savedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertEquals(10, $savedInvoice->invoice_discount_percent);
        $this->assertEquals(0, $savedInvoice->invoice_discount_amount);
    }

    /**
     * Test returning error when item has quantity but no name.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "items": "[{\"item_name\":\"\",\"item_quantity\":5,\"item_price\":100.00}]"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_returns_error_when_item_has_quantity_but_no_name(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $items   = [
            [
                'item_name'     => '',
                'item_quantity' => 5,
                'item_price'    => 100.00,
            ],
        ];

        /**
         * {
         *     "invoice_id": 1,
         *     "items": "[{\"item_id\":null,\"item_name\":\"\",\"item_quantity\":5,\"item_price\":100,\"item_discount_amount\":0}]"
         * }.
         */
        $payload = [
            'invoice_id' => $invoice->invoice_id,
            'items'      => json_encode($items),
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(0, $data['success']);
        $this->assertArrayHasKey('validation_errors', $data);
        $this->assertArrayHasKey('item_name', $data['validation_errors']);
    }

    /**
     * Test saving invoice tax rate in legacy calculation mode.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "tax_rate_id": 1,
     *   "include_item_tax": 1
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_saves_invoice_tax_rate_in_legacy_calculation_mode(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $taxRate = $this->seedModel('TaxRate', ['tax_rate_percent' => 20]);

        /**
         * {
         *     "invoice_id": 1,
         *     "tax_rate_id": 1,
         *     "include_item_tax": 1
         * }.
         */
        $payload = [
            'invoice_id'       => $invoice->invoice_id,
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'include_item_tax' => 1,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save_tax_rate'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $savedTax = InvoiceTaxRate::where('invoice_id', $invoice->invoice_id)
            ->where('tax_rate_id', $taxRate->tax_rate_id)
            ->first();
        $this->assertNotNull($savedTax);
        $this->assertEquals(1, $savedTax->include_item_tax);
    }

    /**
     * Test deleting invoice item and recalculating invoice.
     *
     * JSON Payload:
     * {
     *   "item_id": 1
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_invoice_item_and_returns_success(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $item    = $this->seedModel('Item', ['invoice_id' => $invoice->invoice_id]);

        /**
         * {
         *     "item_id": 1
         * }.
         */
        $payload = ['item_id' => $item->item_id];

        /* Act */
        $response = $this->actingAs($user)->post(
            route('invoices.ajax.delete_item', ['invoiceId' => $invoice->invoice_id]),
            $payload
        );

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertNull(Item::find($item->item_id));
    }

    /**
     * Test returning failure when deleting item for non-existent invoice.
     *
     * JSON Payload:
     * {
     *   "item_id": 99999
     * }
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_failure_when_deleting_item_for_non_existent_invoice(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        /**
         * {
         *     "item_id": 99999
         * }.
         */
        $payload = ['item_id' => 99999];

        /* Act */
        $response = $this->actingAs($user)->post(
            route('invoices.ajax.delete_item', ['invoiceId' => 99999]),
            $payload
        );

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(0, $data['success']);
    }

    /**
     * Test returning invoice item data when getting item.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_invoice_item_data_when_getting_item(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $item    = $this->seedModel('Item', [
            'invoice_id' => $invoice->invoice_id,
            'item_name'  => 'Test Item',
            'item_price' => 100.00,
        ]);

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.ajax.get_item', ['item_id' => $item->item_id]));

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals('Test Item', $data['item_name']);
        $this->assertEquals(100.00, $data['item_price']);
    }

    /**
     * Test returning empty array when getting non-existent item.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_getting_non_existent_item(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.ajax.get_item', ['item_id' => 99999]));

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEmpty($data);
    }

    /**
     * Test copying invoice with all items and tax rates.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "client_id": 2,
     *   "user_id": 1,
     *   "invoice_date_created": "2024-01-01",
     *   "invoice_change_client": 0
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_copies_invoice_with_all_items_and_tax_rates(): void
    {
        /* Arrange */
        $user          = $this->seedModel('User');
        $client        = $this->seedModel('Client');
        $sourceInvoice = $this->seedModel('Invoice');
        $this->seedModelMany('Item', 3, ['invoice_id' => $sourceInvoice->invoice_id]);

        /**
         * {
         *     "invoice_id": 1,
         *     "client_id": 1,
         *     "user_id": 1,
         *     "invoice_date_created": "2024-01-01",
         *     "invoice_change_client": 0
         * }.
         */
        $payload = [
            'invoice_id'            => $sourceInvoice->invoice_id,
            'client_id'             => $client->client_id,
            'user_id'               => $user->user_id,
            'invoice_date_created'  => '2024-01-01',
            'invoice_change_client' => 0,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.copy'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('invoice_id', $data);
        $newInvoice = Invoice::find($data['invoice_id']);
        $this->assertNotNull($newInvoice);
        $this->assertEquals($client->client_id, $newInvoice->client_id);
        $this->assertEquals(3, Item::where('invoice_id', $newInvoice->invoice_id)->count());
    }

    /**
     * Test changing invoice user and returning success.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "user_id": 2
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_changes_invoice_user_and_returns_success(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $newUser = $this->seedModel('User');

        /**
         * {
         *     "invoice_id": 1,
         *     "user_id": 1
         * }.
         */
        $payload = [
            'invoice_id' => $invoice->invoice_id,
            'user_id'    => $newUser->user_id,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.change_user'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $updatedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertEquals($newUser->user_id, $updatedInvoice->user_id);
    }

    /**
     * Test returning error when changing to non-existent user.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "user_id": 99999
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_returns_error_when_changing_to_non_existent_user(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');

        /**
         * {
         *     "invoice_id": 1,
         *     "user_id": 99999
         * }.
         */
        $payload = [
            'invoice_id' => $invoice->invoice_id,
            'user_id'    => 99999,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.change_user'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(0, $data['success']);
        $this->assertArrayHasKey('error', $data);
    }

    /**
     * Test changing invoice client and returning success.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "client_id": 2
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_changes_invoice_client_and_returns_success(): void
    {
        /* Arrange */
        $user      = $this->seedModel('User');
        $invoice   = $this->seedModel('Invoice');
        $newClient = $this->seedModel('Client');

        /**
         * {
         *     "invoice_id": 1,
         *     "client_id": 1
         * }.
         */
        $payload = [
            'invoice_id' => $invoice->invoice_id,
            'client_id'  => $newClient->client_id,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.change_client'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $updatedInvoice = Invoice::find($invoice->invoice_id);
        $this->assertEquals($newClient->client_id, $updatedInvoice->client_id);
    }

    /**
     * Test creating recurring invoice and returning ID.
     *
     * JSON Payload:
     * {
     *   "client_id": 1,
     *   "user_id": 1,
     *   "invoice_group_id": 1,
     *   "recur_start_date": "2024-01-01",
     *   "recur_end_date": "2025-01-01",
     *   "recur_frequency": "1M"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_recurring_invoice_and_returns_id(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        /**
         * {
         *     "client_id": 1,
         *     "user_id": 1,
         *     "invoice_group_id": 1,
         *     "recur_start_date": "2024-01-01",
         *     "recur_end_date": "2025-01-01",
         *     "recur_frequency": "1M"
         * }.
         */
        $payload = [
            'client_id'        => $client->client_id,
            'user_id'          => $user->user_id,
            'invoice_group_id' => 1,
            'recur_start_date' => '2024-01-01',
            'recur_end_date'   => '2025-01-01',
            'recur_frequency'  => '1M',
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.create_recurring'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('invoice_recurring_id', $data);
        $recurring = InvoicesRecurring::find($data['invoice_recurring_id']);
        $this->assertNotNull($recurring);
        $this->assertEquals('1M', $recurring->recur_frequency);
    }

    /**
     * Test calculating recurring start date based on frequency.
     */
    #[Group('exotic')]
    #[Test]
    public function it_calculates_recurring_start_date_based_on_frequency(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.ajax.recur_start_date', ['recur_frequency' => '1M']));

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertArrayHasKey('recur_start_date', $data);
        $expectedDate = date('Y-m-d', strtotime('+1 month'));
        $this->assertEquals($expectedDate, $data['recur_start_date']);
    }

    /**
     * Test creating credit invoice from existing invoice.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "invoice_date_created": "2024-01-01"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_credit_invoice_from_existing_invoice(): void
    {
        /* Arrange */
        $user          = $this->seedModel('User');
        $sourceInvoice = $this->seedModel('Invoice');
        $this->seedModelMany('Item', 2, ['invoice_id' => $sourceInvoice->invoice_id]);

        /**
         * {
         *     "invoice_id": 1,
         *     "invoice_date_created": "2024-01-01"
         * }.
         */
        $payload = [
            'invoice_id'           => $sourceInvoice->invoice_id,
            'invoice_date_created' => '2024-01-01',
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.create_credit'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('invoice_id', $data);
        $creditInvoice = Invoice::find($data['invoice_id']);
        $this->assertNotNull($creditInvoice);
        $this->assertEquals($sourceInvoice->client_id, $creditInvoice->client_id);
    }

    /**
     * Test loading copy invoice modal with correct view data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_copy_invoice_modal_with_clients_and_users(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $this->seedModelMany('Client', 3);
        $this->seedModelMany('User', 2);

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.modal.copy', ['invoice_id' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::modal_copy_invoice');
        $response->assertViewHas('invoice');
        $response->assertViewHas('clients');
        $response->assertViewHas('users');
        $clients = $response->viewData('clients');
        $this->assertCount(3, $clients);
    }

    /**
     * Test loading create invoice modal with clients list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_create_invoice_modal_with_clients_list(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Client', 5);
        $this->seedModelMany('User', 2);

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.modal.create'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::modal_create_invoice');
        $response->assertViewHas('clients');
        $response->assertViewHas('users');
        $clients = $response->viewData('clients');
        $this->assertCount(5, $clients);
    }

    /**
     * Test loading change user modal with users list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_change_user_modal_with_users_list(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $this->seedModelMany('User', 3);

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.modal.change_user', ['invoice_id' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::modal_change_user');
        $response->assertViewHas('invoice');
        $response->assertViewHas('users');
        $users = $response->viewData('users');
        $this->assertCount(3, $users);
    }

    /**
     * Test loading change client modal with clients list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_change_client_modal_with_clients_list(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $this->seedModelMany('Client', 4);

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.modal.change_client', ['invoice_id' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::modal_change_client');
        $response->assertViewHas('invoice');
        $response->assertViewHas('clients');
        $clients = $response->viewData('clients');
        $this->assertCount(4, $clients);
    }

    /**
     * Test loading create recurring modal with form data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_create_recurring_modal_with_form_data(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Client', 2);
        $this->seedModelMany('User', 2);

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.modal.create_recurring'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::modal_create_recurring');
        $response->assertViewHas('clients');
        $response->assertViewHas('users');
    }

    /**
     * Test loading create credit modal with invoice data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_create_credit_modal_with_invoice_data(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');

        /* Act */
        $response = $this->actingAs($user)->get(route('invoices.modal.create_credit', ['invoice_id' => $invoice->invoice_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('invoices::modal_create_credit');
        $response->assertViewHas('invoice');
        $viewInvoice = $response->viewData('invoice');
        $this->assertEquals($invoice->invoice_id, $viewInvoice->invoice_id);
    }

    /**
     * Test preserving item details when saving invoice.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "items": "[{\"item_id\":null,\"item_name\":\"Consulting Services\",\"item_description\":\"Full project consultation\",\"item_quantity\":10,\"item_price\":150.00,\"item_discount_amount\":50.00}]",
     *   "invoice_discount_percent": 0,
     *   "invoice_discount_amount": 0,
     *   "invoice_number": "INV-001",
     *   "invoice_date_created": "2024-01-01",
     *   "invoice_date_due": "2024-01-31",
     *   "invoice_status_id": 1
     * }
     */
    #[Test]
    public function it_preserves_item_details_when_saving_invoice(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $items   = [
            [
                'item_id'              => null,
                'item_name'            => 'Consulting Services',
                'item_description'     => 'Full project consultation',
                'item_quantity'        => 10,
                'item_price'           => 150.00,
                'item_discount_amount' => 50.00,
            ],
        ];

        $payload = [
            'invoice_id'               => $invoice->invoice_id,
            'items'                    => json_encode($items),
            'invoice_discount_percent' => 0,
            'invoice_discount_amount'  => 0,
            'invoice_number'           => 'INV-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_due'         => '2024-01-31',
            'invoice_status_id'        => 1,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $savedItem = Item::where('invoice_id', $invoice->invoice_id)->first();
        $this->assertEquals('Consulting Services', $savedItem->item_name);
        $this->assertEquals('Full project consultation', $savedItem->item_description);
        $this->assertEquals(10, $savedItem->item_quantity);
        $this->assertEquals(150.00, $savedItem->item_price);
        $this->assertEquals(50.00, $savedItem->item_discount_amount);
    }

    /**
     * Test handling global discount distribution across items.
     *
     * JSON Payload:
     * {
     *   "invoice_id": 1,
     *   "items": "[{\"item_name\":\"Item 1\",\"item_quantity\":1,\"item_price\":100.00},{\"item_name\":\"Item 2\",\"item_quantity\":1,\"item_price\":50.00}]",
     *   "invoice_discount_percent": 0,
     *   "invoice_discount_amount": 30.00,
     *   "invoice_number": "INV-001",
     *   "invoice_date_created": "2024-01-01",
     *   "invoice_date_due": "2024-01-31",
     *   "invoice_status_id": 1
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_distributes_global_discount_across_items_proportionally(): void
    {
        /* Arrange */
        $user    = $this->seedModel('User');
        $invoice = $this->seedModel('Invoice');
        $items   = [
            [
                'item_name'     => 'Item 1',
                'item_quantity' => 1,
                'item_price'    => 100.00,
            ],
            [
                'item_name'     => 'Item 2',
                'item_quantity' => 1,
                'item_price'    => 50.00,
            ],
        ];

        /**
         * {
         *     "invoice_id": 1,
         *     "items": "[{\"item_id\":null,\"item_name\":\"Item 1\",\"item_quantity\":1,\"item_price\":100,\"item_discount_amount\":0},{\"item_id\":null,\"item_name\":\"Item 2\",\"item_quantity\":1,\"item_price\":50,\"item_discount_amount\":0}]",
         *     "invoice_discount_percent": 0,
         *     "invoice_discount_amount": 30.00,
         *     "invoice_number": "INV-001",
         *     "invoice_date_created": "2024-01-01",
         *     "invoice_date_due": "2024-01-31",
         *     "invoice_status_id": 1
         * }.
         */
        $payload = [
            'invoice_id'               => $invoice->invoice_id,
            'items'                    => json_encode($items),
            'invoice_discount_percent' => 0,
            'invoice_discount_amount'  => 30.00, // 20% global discount on 150 total
            'invoice_number'           => 'INV-001',
            'invoice_date_created'     => '2024-01-01',
            'invoice_date_due'         => '2024-01-31',
            'invoice_status_id'        => 1,
        ];

        /* Act */
        $response = $this->actingAs($user)->post(route('invoices.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $invoice->refresh();
        $this->assertEquals(30.00, $invoice->invoice_discount_amount);
        /* Verify items were created */
        $this->assertEquals(2, Item::where('invoice_id', $invoice->invoice_id)->count());
    }
}
