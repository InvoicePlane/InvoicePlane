<?php

namespace Tests\Feature\Quotes;

use Modules\Crm\Controllers\QuotesController as GuestQuotesController;
use Modules\Quotes\Models\Quote;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Concerns\InteractsWithDatabase;
use Tests\Feature\FeatureTestCase;

/**
 * QuotesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal quote viewing and approval.
 */
#[CoversClass(GuestQuotesController::class)]

class QuotesAjaxControllerTest extends FeatureTestCase
{
    use InteractsWithDatabase;

    /**
     * Test saving a quote with items returns success.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "quote_status_id": 2,
     *   "quote_date_created": "2024-01-01",
     *   "quote_date_expires": "2024-01-31",
     *   "items": "[{\"item_name\":\"Test Item\",\"item_quantity\":2,\"item_price\":100.00,\"item_order\":1}]"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_saves_quote_with_items_and_returns_success(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote', ['quote_status_id' => 1]);

        /**
         * {
         *     "quote_id": 1,
         *     "quote_status_id": 2,
         *     "quote_date_created": "2024-01-01",
         *     "quote_date_expires": "2024-01-31",
         *     "items": "[{\"item_name\":\"Test Item\",\"item_quantity\":2,\"item_price\":100,\"item_order\":1}]"
         * }.
         */
        $payload = [
            'quote_id'           => $quote->quote_id,
            'quote_status_id'    => 2,
            'quote_date_created' => '2024-01-01',
            'quote_date_expires' => '2024-01-31',
            'items'              => json_encode([
                [
                    'item_name'     => 'Test Item',
                    'item_quantity' => 2,
                    'item_price'    => 100.00,
                    'item_order'    => 1,
                ],
            ]),
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);

        /** Verify quote was updated */
        $updatedQuote = Quote::find($quote->quote_id);
        $this->assertEquals(2, $updatedQuote->quote_status_id);
    }

    /**
     * Test saving quote with validation errors returns error response.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_returns_validation_errors_when_saving_invalid_quote(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote');

        /**
         * {
         *     "quote_id": 1
         * }.
         */
        $payload = [
            'quote_id' => $quote->quote_id,
            /* Missing required fields */
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(0, $data['success']);
        $this->assertArrayHasKey('validation_errors', $data);
    }

    /**
     * Test saving quote with discount percent prevents discount amount.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "quote_discount_percent": 10,
     *   "quote_discount_amount": 20,
     *   "items": "[{\"item_name\":\"Test\",\"item_quantity\":1,\"item_price\":100}]"
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_prevents_both_discount_types_when_saving_quote(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote');

        /**
         * {
         *     "quote_id": 1,
         *     "quote_discount_percent": 10,
         *     "quote_discount_amount": 20,
         *     "quote_date_created": "2024-01-01",
         *     "quote_date_expires": "2024-01-31",
         *     "items": "[{\"item_name\":\"Test\",\"item_quantity\":1,\"item_price\":100}]"
         * }.
         */
        $payload = [
            'quote_id'               => $quote->quote_id,
            'quote_discount_percent' => 10,
            'quote_discount_amount'  => 20,
            'quote_date_created'     => '2024-01-01',
            'quote_date_expires'     => '2024-01-31',
            'items'                  => json_encode([
                ['item_name' => 'Test', 'item_quantity' => 1, 'item_price' => 100],
            ]),
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);

        $quote->refresh();
        $this->assertEquals(10, $quote->quote_discount_percent);
        $this->assertEquals(0, $quote->quote_discount_amount);
    }

    /**
     * Test saving quote item calculates subtotal correctly.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "items": "[{\"item_name\":\"Item\",\"item_quantity\":3,\"item_price\":50.00}]"
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_subtotal_correctly_when_saving_quote(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote');

        /**
         * {
         *     "quote_id": 1,
         *     "quote_date_created": "2024-01-01",
         *     "quote_date_expires": "2024-01-31",
         *     "items": "[{\"item_name\":\"Item\",\"item_quantity\":3,\"item_price\":50}]"
         * }.
         */
        $payload = [
            'quote_id'           => $quote->quote_id,
            'quote_date_created' => '2024-01-01',
            'quote_date_expires' => '2024-01-31',
            'items'              => json_encode([
                ['item_name' => 'Item', 'item_quantity' => 3, 'item_price' => 50.00],
            ]),
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.save'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
    }

    /**
     * Test saving quote tax rate returns success.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "tax_rate_id": 1,
     *   "include_item_tax": 0
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_saves_quote_tax_rate_successfully(): void
    {
        /** Arrange */
        $user    = $this->seedModel('User');
        $quote   = $this->seedModel('Quote');
        $taxRate = $this->seedModel('TaxRate');

        /**
         * {
         *     "quote_id": 1,
         *     "tax_rate_id": 1,
         *     "include_item_tax": 0
         * }.
         */
        $payload = [
            'quote_id'         => $quote->quote_id,
            'tax_rate_id'      => $taxRate->tax_rate_id,
            'include_item_tax' => 0,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.save_tax_rate'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);

        $this->assertNotNull(QuoteTaxRate::query()->where('quote_id', $quote->quote_id)
            ->where('tax_rate_id', $taxRate->tax_rate_id)
            ->first());
    }

    /**
     * Test deleting quote item returns success.
     *
     * JSON Payload:
     * {
     *   "item_id": 1
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_quote_item_successfully(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote');
        $item  = $this->seedModel('QuoteItem', ['quote_id' => $quote->quote_id]);

        /**
         * {
         *     "item_id": 1
         * }.
         */
        $payload = ['item_id' => $item->item_id];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('quotes.ajax.delete_item', ['quoteId' => $quote->quote_id]),
            $payload
        );

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertNull(QuoteItem::find($item->item_id));
    }

    /**
     * Test deleting item from non-existent quote returns failure.
     *
     * JSON Payload:
     * {
     *   "item_id": 99999
     * }
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_failure_when_deleting_item_from_non_existent_quote(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');
        /**
         * {
         *     "item_id": 99999
         * }.
         */
        $payload = ['item_id' => 99999];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(
            route('quotes.ajax.delete_item', ['quoteId' => 99999]),
            $payload
        );

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(0, $data['success']);
    }

    /**
     * Test getting quote item returns item data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_quote_item_data_when_getting_item(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote');
        $item  = $this->seedModel('QuoteItem', [
            'quote_id'  => $quote->quote_id,
            'item_name' => 'Test Item',
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.ajax.get_item', ['item_id' => $item->item_id]));

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals('Test Item', $data['item_name']);
    }

    /**
     * Test getting non-existent item returns empty array.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_empty_array_when_getting_non_existent_item(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.ajax.get_item', ['item_id' => 99999]));

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEmpty($data);
    }

    /**
     * Test copying quote creates new quote with same data.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "client_id": 2,
     *   "user_id": 1,
     *   "quote_date_created": "2024-01-01",
     *   "quote_change_client": 0
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_copies_quote_with_all_items(): void
    {
        /** Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');
        $quote  = $this->seedModel('Quote');
        $this->seedModelMany('QuoteItem', 3, ['quote_id' => $quote->quote_id]);

        /**
         * {
         *     "quote_id": 1,
         *     "client_id": 1,
         *     "user_id": 1,
         *     "quote_date_created": "2024-01-01",
         *     "quote_change_client": 0
         * }.
         */
        $payload = [
            'quote_id'            => $quote->quote_id,
            'client_id'           => $client->client_id,
            'user_id'             => $user->user_id,
            'quote_date_created'  => '2024-01-01',
            'quote_change_client' => 0,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.copy'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('quote_id', $data);

        $newQuote = Quote::find($data['quote_id']);
        $this->assertNotNull($newQuote);
        $this->assertEquals(3, QuoteItem::query()->where('quote_id', $newQuote->quote_id)->count());
    }

    /**
     * Test changing quote user updates user_id.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "user_id": 2
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_changes_quote_user_successfully(): void
    {
        /** Arrange */
        $user    = $this->seedModel('User');
        $newUser = $this->seedModel('User');
        $quote   = $this->seedModel('Quote');

        /**
         * {
         *     "quote_id": 1,
         *     "user_id": 1
         * }.
         */
        $payload = [
            'quote_id' => $quote->quote_id,
            'user_id'  => $newUser->user_id,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.change_user'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);

        $quote->refresh();
        $this->assertEquals($newUser->user_id, $quote->user_id);
    }

    /**
     * Test changing to non-existent user returns error.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "user_id": 99999
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_returns_error_when_changing_to_non_existent_user(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote');

        /**
         * {
         *     "quote_id": 1,
         *     "user_id": 99999
         * }.
         */
        $payload = [
            'quote_id' => $quote->quote_id,
            'user_id'  => 99999,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.change_user'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(0, $data['success']);
    }

    /**
     * Test changing quote client updates client_id.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "client_id": 2
     * }
     */
    #[Group('exotic')]
    #[Test]
    public function it_changes_quote_client_successfully(): void
    {
        /** Arrange */
        $user      = $this->seedModel('User');
        $newClient = $this->seedModel('Client');
        $quote     = $this->seedModel('Quote');

        /**
         * {
         *     "quote_id": 1,
         *     "client_id": 1
         * }.
         */
        $payload = [
            'quote_id'  => $quote->quote_id,
            'client_id' => $newClient->client_id,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.change_client'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);

        $quote->refresh();
        $this->assertEquals($newClient->client_id, $quote->client_id);
    }

    /**
     * Test creating new quote returns quote ID.
     *
     * JSON Payload:
     * {
     *   "client_id": 1,
     *   "user_id": 1,
     *   "quote_date_created": "2024-01-01"
     * }
     */
    #[Group('crud')]
    #[Test]
    public function it_creates_new_quote_and_returns_quote_id(): void
    {
        /** Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        /**
         * {
         *     "client_id": 1,
         *     "user_id": 1,
         *     "quote_date_created": "2024-01-01"
         * }.
         */
        $payload = [
            'client_id'          => $client->client_id,
            'user_id'            => $user->user_id,
            'quote_date_created' => '2024-01-01',
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.create'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('quote_id', $data);

        $quote = Quote::find($data['quote_id']);
        $this->assertNotNull($quote);
        $this->assertEquals($client->client_id, $quote->client_id);
    }

    /**
     * Test converting quote to invoice creates invoice.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "client_id": 1,
     *   "user_id": 1,
     *   "invoice_date_created": "2024-01-01",
     *   "invoice_group_id": 1,
     *   "invoice_change_client": 0
     * }
     */
    #[Test]
    public function it_converts_quote_to_invoice_successfully(): void
    {
        /** Arrange */
        $user         = $this->seedModel('User');
        $client       = $this->seedModel('Client');
        $quote        = $this->seedModel('Quote', ['client_id' => $client->client_id]);
        $invoiceGroup = $this->seedModel('InvoiceGroup');
        $this->seedModelMany('QuoteItem', 2, ['quote_id' => $quote->quote_id]);

        /**
         * {
         *     "quote_id": 1,
         *     "client_id": 1,
         *     "user_id": 1,
         *     "invoice_date_created": "2024-01-01",
         *     "invoice_group_id": 1,
         *     "invoice_change_client": 0
         * }.
         */
        /**
         * {
         *     "quote_id": 1,
         *     "client_id": 1,
         *     "user_id": 1,
         *     "invoice_date_created": "2024-01-01",
         *     "invoice_group_id": 1,
         *     "invoice_change_client": 0
         * }.
         */
        $payload = [
            'quote_id'              => $quote->quote_id,
            'client_id'             => $client->client_id,
            'user_id'               => $user->user_id,
            'invoice_date_created'  => '2024-01-01',
            'invoice_group_id'      => $invoiceGroup->invoice_group_id,
            'invoice_change_client' => 0,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.quote_to_invoice'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('invoice_id', $data);

        $invoice = Invoice::find($data['invoice_id']);
        $this->assertNotNull($invoice);
        $this->assertEquals($client->client_id, $invoice->client_id);
    }

    /**
     * Test converting approved quote to invoice marks quote as approved.
     *
     * JSON Payload:
     * {
     *   "quote_id": 1,
     *   "client_id": 1,
     *   "user_id": 1,
     *   "invoice_date_created": "2024-01-01",
     *   "invoice_group_id": 1,
     *   "invoice_change_client": 0
     * }
     */
    #[Test]
    public function it_marks_quote_as_approved_when_converting_to_invoice(): void
    {
        /** Arrange */
        $user         = $this->seedModel('User');
        $client       = $this->seedModel('Client');
        $quote        = $this->seedModel('Quote', ['client_id' => $client->client_id, 'quote_status_id' => 1]);
        $invoiceGroup = $this->seedModel('InvoiceGroup');

        $payload = [
            'quote_id'              => $quote->quote_id,
            'client_id'             => $client->client_id,
            'user_id'               => $user->user_id,
            'invoice_date_created'  => '2024-01-01',
            'invoice_group_id'      => $invoiceGroup->invoice_group_id,
            'invoice_change_client' => 0,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.ajax.quote_to_invoice'), $payload);

        /* Assert */
        $response->assertOk();
        $data = $response->json();
        $this->assertEquals(1, $data['success']);

        $quote->refresh();
        $this->assertEquals(4, $quote->quote_status_id); // 4 = Approved
    }

    /**
     * Test modal copy quote loads with clients and users.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_copy_quote_modal_with_clients_and_users(): void
    {
        /** Arrange */
        $user  = $this->seedModel('User');
        $quote = $this->seedModel('Quote');
        $this->seedModelMany('Client', 3);
        $this->seedModelMany('User', 2);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.modal.copy', ['quote_id' => $quote->quote_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('quotes::modal_copy_quote');
        $response->assertViewHas('quote');
        $response->assertViewHas('clients');
        $response->assertViewHas('users');
    }

    /**
     * Test modal create quote loads with clients and users.
     */
    #[Group('smoke')]
    #[Test]
    public function it_loads_create_quote_modal_with_clients_list(): void
    {
        /** Arrange */
        $user = $this->seedModel('User');
        $this->seedModelMany('Client', 5);
        $this->seedModelMany('User', 2);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.modal.create'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('quotes::modal_create_quote');
        $response->assertViewHas('clients');
        $response->assertViewHas('users');
    }
}
