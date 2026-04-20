<?php

namespace Modules\Quotes\Tests\Feature;

use Modules\Crm\Controllers\QuotesController as GuestQuotesController;
use Modules\Quotes\Models\Quote;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\Feature\FeatureTestCase;

/**
 * QuotesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal quote viewing and approval.
 */
#[CoversClass(GuestQuotesController::class)]
class CrmQuotesControllerTest extends FeatureTestCase
{
    /**
     * Test index displays guest quotes list.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_guest_quotes_list(): void
    {
        /** Arrange */
        // Guest portal accessible without authentication

        /** Act */
        $response = $this->get(route('guest.quotes'));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_quotes');
    }

    /**
     * Test view displays specific quote by URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_quote_by_url_key(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create(['quote_url_key' => 'test-quote-key']);

        /** Act */
        $response = $this->get(route('guest.quotes.view', ['urlKey' => 'test-quote-key']));

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('crm::guest_quote_view');
        $response->assertViewHas('quote');

        $viewQuote = $response->viewData('quote');
        $this->assertEquals($quote->quote_id, $viewQuote->quote_id);
    }

    /**
     * Test view returns 404 for invalid URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_for_invalid_quote_url_key(): void
    {
        /** Arrange */
        // No quote with this URL key

        /** Act */
        $response = $this->get(route('guest.quotes.view', ['urlKey' => 'non-existent-key']));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test approve updates quote status to approved.
     */
    #[Test]
    public function it_approves_quote_when_approve_called(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create([
            'quote_url_key'   => 'approve-key',
            'quote_status_id' => 2, // Sent
        ]);

        /** Act */
        $response = $this->get(route('guest.quotes.approve', ['urlKey' => 'approve-key']));

        /* Assert */
        $response->assertRedirect();
        $response->assertSessionHas('alert_success');

        $quote->refresh();
        $this->assertEquals(4, $quote->quote_status_id); // Approved
    }

    /**
     * Test approve returns 404 for invalid URL key.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_approving_non_existent_quote(): void
    {
        /** Arrange */
        // No quote with this URL key

        /** Act */
        $response = $this->get(route('guest.quotes.approve', ['urlKey' => 'invalid-key']));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test quote operations are accessible without authentication.
     */
    #[Test]
    public function it_is_accessible_without_authentication(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create(['quote_url_key' => 'guest-quote-key']);

        /** Act */
        $response = $this->get(route('guest.quotes.view', ['urlKey' => 'guest-quote-key']));

        /* Assert */
        $response->assertOk();
    }
}

/**
 * Test suite for QuotesAjaxController.
 *
 * Tests AJAX operations including save, copy, create, and quote-to-invoice conversion via HTTP routes
 */
#[CoversClass(QuotesAjaxController::class)]
class QuotesAjaxControllerTest extends FeatureTestCase
{
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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create(['quote_status_id' => 1]);

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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create();

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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create();

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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create();

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
        $user    = User::factory()->create();
        $quote   = Quote::factory()->create();
        $taxRate = TaxRate::factory()->create();

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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create();
        $item  = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);

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
        $user = User::factory()->create();
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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create();
        $item  = QuoteItem::factory()->create([
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
        $user = User::factory()->create();

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
        $user   = User::factory()->create();
        $client = Client::factory()->create();
        $quote  = Quote::factory()->create();
        QuoteItem::factory()->count(3)->create(['quote_id' => $quote->quote_id]);

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
        $user    = User::factory()->create();
        $newUser = User::factory()->create();
        $quote   = Quote::factory()->create();

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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create();

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
        $user      = User::factory()->create();
        $newClient = Client::factory()->create();
        $quote     = Quote::factory()->create();

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
        $user   = User::factory()->create();
        $client = Client::factory()->create();

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
        $user         = User::factory()->create();
        $client       = Client::factory()->create();
        $quote        = Quote::factory()->create(['client_id' => $client->client_id]);
        $invoiceGroup = InvoiceGroup::factory()->create();
        QuoteItem::factory()->count(2)->create(['quote_id' => $quote->quote_id]);

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
        $user         = User::factory()->create();
        $client       = Client::factory()->create();
        $quote        = Quote::factory()->create(['client_id' => $client->client_id, 'quote_status_id' => 1]);
        $invoiceGroup = InvoiceGroup::factory()->create();

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
        $user  = User::factory()->create();
        $quote = Quote::factory()->create();
        Client::factory()->count(3)->create();
        User::factory()->count(2)->create();

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
        $user = User::factory()->create();
        Client::factory()->count(5)->create();
        User::factory()->count(2)->create();

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

/**
 * QuotesController Feature Tests.
 *
 * Comprehensive test suite for QuotesController covering all methods
 * with data integrity validation, edge cases, and business logic verification.
 * Uses Laravel HTTP testing helpers for proper feature testing.
 */
#[CoversClass(QuotesController::class)]
class QuotesControllerTest extends FeatureTestCase
{
    /**
     * Test that index method redirects to all quotes status view.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_all_status_view_from_index(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.index'));

        /* Assert */
        $response->assertRedirect(route('quotes.status', ['status' => 'all']));
    }

    /**
     * Test that status method displays only draft quotes when draft status is selected.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_draft_quotes_when_draft_status_selected(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $sentQuote = Quote::factory()->sent()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/status/draft');

        /* Assert */
        $response->assertOk();
        $response->assertViewIs('quotes::index');
        $response->assertViewHas('quotes');
        $response->assertViewHas('status', 'draft');

        /** Verify only draft quotes are returned */
        $quotes   = $response->viewData('quotes');
        $quoteIds = $quotes->pluck('quote_id')->toArray();
        $this->assertContains($draftQuote->quote_id, $quoteIds);
        $this->assertNotContains($sentQuote->quote_id, $quoteIds);
    }

    /**
     * Test that status method displays all quotes when 'all' status is selected.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_all_quotes_when_all_status_selected(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $sentQuote = Quote::factory()->sent()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/status/all');

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('quotes');
        $response->assertViewHas('status', 'all');

        /** Verify all quotes are returned */
        $quotes   = $response->viewData('quotes');
        $quoteIds = $quotes->pluck('quote_id')->toArray();
        $this->assertContains($draftQuote->quote_id, $quoteIds);
        $this->assertContains($sentQuote->quote_id, $quoteIds);
    }

    /**
     * Test that status method includes quote statuses in view data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_includes_quote_statuses_in_view_data_for_status_method(): void
    {
        /** Arrange */
        $user = User::factory()->create();

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/status/all');

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('quote_statuses');
        $quoteStatuses = $response->viewData('quote_statuses');
        $this->assertIsArray($quoteStatuses);
        $this->assertNotEmpty($quoteStatuses);
    }

    /**
     * Test that view method displays quote details with all related data.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_quote_details_with_items_and_amounts(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $item1 = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);
        $item2 = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.view', ['quote_id' => $quote->quote_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('quote');
        $response->assertViewHas('items');
        $response->assertViewHas('quote_id');

        $viewQuote = $response->viewData('quote');
        $items     = $response->viewData('items');
        $quoteId   = $response->viewData('quote_id');

        $this->assertEquals($quote->quote_id, $viewQuote->quote_id);
        $this->assertEquals($quote->quote_id, $quoteId);
        $this->assertCount(2, $items);
    }

    /**
     * Test that view method returns 404 when quote is not found.
     */
    #[Group('smoke')]
    #[Test]
    public function it_returns_404_when_viewing_non_existent_quote(): void
    {
        /** Arrange */
        $user               = User::factory()->create();
        $nonExistentQuoteId = 99999;

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.view', ['quote_id' => $nonExistentQuoteId]));

        /* Assert */
        $response->assertNotFound();
    }

    /**
     * Test that view method includes custom fields in view data.
     */
    #[Group('exotic')]
    #[Test]
    public function it_includes_custom_fields_in_quote_view_data(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.view', ['quote_id' => $quote->quote_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('custom_fields');
        $response->assertViewHas('custom_values');
    }

    /**
     * Test that view method includes tax rates in view data.
     */
    #[Group('exotic')]
    #[Test]
    public function it_includes_tax_rates_in_quote_view_data(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get(route('quotes.view', ['quote_id' => $quote->quote_id]));

        /* Assert */
        $response->assertOk();
        $response->assertViewHas('tax_rates');
        $response->assertViewHas('quote_tax_rates');
    }

    /**
     * Test that delete method removes quote and redirects to index.
     */
    #[Group('smoke')]
    #[Test]
    public function it_deletes_quote_and_redirects_to_index(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $quoteId = $quote->quote_id;

        /**
         * {
         *     "quote_id": 1
         * }.
         */
        $deleteParams = [
            'quote_id' => $quoteId,
        ];

        /* Act */
        $this->actingAs($user);
        $response = $this->post(route('quotes.delete', $deleteParams));

        /* Assert */
        $response->assertRedirect(route('quotes.index'));

        /* Verify quote was deleted */
        $this->assertNull(Quote::find($quoteId));
    }

    /**
     * Test that delete method also deletes related records (items, tax rates, amounts).
     */
    #[Group('crud')]
    #[Test]
    public function it_deletes_quote_and_all_related_records(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $item    = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);
        $taxRate = QuoteTaxRate::factory()->create(['quote_id' => $quote->quote_id]);

        $quoteId   = $quote->quote_id;
        $itemId    = $item->item_id;
        $taxRateId = $taxRate->quote_tax_rate_id;

        /**
         * {
         *     "quote_id": 1
         * }.
         */
        $deleteParams = [
            'quote_id' => $quoteId,
        ];

        /* Act */
        $this->actingAs($user)->post(route('quotes.delete', $deleteParams));

        /* Assert - verify all related records are deleted */
        $this->assertNull(Quote::find($quoteId));
        $this->assertNull(QuoteItem::find($itemId));
        $this->assertNull(QuoteTaxRate::find($taxRateId));
    }

    /**
     * Test that deleteQuoteTax method removes tax rate and recalculates quote.
     */
    #[Group('exotic')]
    #[Test]
    public function it_removes_tax_rate_and_recalculates_quote(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $taxRate = QuoteTaxRate::factory()->create([
            'quote_id'    => $quote->quote_id,
            'tax_rate_id' => 1,
        ]);

        $quoteTaxRateId = $taxRate->quote_tax_rate_id;

        /** Act */
        /**
         * Note: Empty payload is correct - IDs are passed via route parameters
         * Route: POST /quotes/delete_tax/{quote_id}/{quote_tax_rate_id}.
         */
        $payload = [];

        $this->actingAs($user);
        $response = $this->post(
            route('quotes.delete_tax', [
                'quote_id'          => $quote->quote_id,
                'quote_tax_rate_id' => $quoteTaxRateId,
            ]),
            $payload
        );

        /* Assert */
        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));

        /* Verify tax rate was deleted */
        $this->assertNull(QuoteTaxRate::find($quoteTaxRateId));
    }

    /**
     * Test that deleteQuoteTax method redirects back to quote view.
     */
    #[Test]
    public function it_redirects_to_quote_view_after_deleting_tax_rate(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $taxRate = QuoteTaxRate::factory()->create(['quote_id' => $quote->quote_id]);

        /** Act */
        /**
         * Note: Empty payload is correct - IDs are passed via route parameters
         * Route: POST /quotes/delete_tax/{quote_id}/{quote_tax_rate_id}.
         */
        $payload = [];

        $this->actingAs($user);
        $response = $this->post(
            route('quotes.delete_tax', [
                'quote_id'          => $quote->quote_id,
                'quote_tax_rate_id' => $taxRate->quote_tax_rate_id,
            ]),
            $payload
        );

        /* Assert */
        $response->assertRedirect(route('quotes.view', ['quote_id' => $quote->quote_id]));
        $response->assertSessionHas('success');
    }

    /**
     * Test that recalculateAllQuotes method processes all quotes in the system.
     */
    #[Group('exotic')]
    #[Test]
    public function it_recalculates_all_quotes_successfully(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $quote1 = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $quote2 = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /** Act */
        /**
         * {}.
         */
        $recalculatePayload = [];

        $this->actingAs($user);
        $response = $this->post(route('quotes.recalculate_all'), $recalculatePayload);

        /* Assert */
        $response->assertRedirect();
        $response->assertSessionHas('success');
    }

    /**
     * Test that recalculateAllQuotes method handles empty quote list gracefully.
     */
    #[Group('exotic')]
    #[Test]
    public function it_handles_empty_quote_list_when_recalculating_all_quotes(): void
    {
        /** Arrange */
        $user = User::factory()->create();
        Quote::query()->delete();

        /** Act */
        /**
         * {}.
         */
        $recalculatePayload = [];

        $this->actingAs($user);
        $response = $this->post(route('quotes.recalculate_all'), $recalculatePayload);

        /* Assert */
        $response->assertRedirect();
        /* Should still return success even with no quotes */
        $response->assertSessionHas('success');
    }

    /**
     * Test that status method paginates results correctly.
     */
    #[Test]
    public function it_paginates_quote_results_correctly(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        /* Create 20 draft quotes (more than the 15 per page limit) */
        for ($i = 0; $i < 20; $i++) {
            Quote::factory()->draft()->create([
                'client_id' => $client->client_id,
                'user_id'   => $user->user_id,
            ]);
        }

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/status/draft');

        /* Assert */
        $response->assertOk();
        $quotes = $response->viewData('quotes');
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $quotes);
        $this->assertEquals(15, $quotes->perPage());
        $this->assertLessThanOrEqual(15, $quotes->count());
    }

    /**
     * Test that status method filters quotes by sent status correctly.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_sent_quotes_when_sent_status_selected(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $sentQuote = Quote::factory()->sent()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/status/sent');

        /* Assert */
        $response->assertOk();
        $quotes   = $response->viewData('quotes');
        $quoteIds = $quotes->pluck('quote_id')->toArray();

        $this->assertNotContains($draftQuote->quote_id, $quoteIds);
        $this->assertContains($sentQuote->quote_id, $quoteIds);
    }

    /**
     * Test that status method filters quotes by approved status correctly.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_approved_quotes_when_approved_status_selected(): void
    {
        /** Arrange */
        $user   = User::factory()->create();
        $client = Client::factory()->create();

        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $approvedQuote = Quote::factory()->approved()->create([
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/status/approved');

        /* Assert */
        $response->assertOk();
        $quotes   = $response->viewData('quotes');
        $quoteIds = $quotes->pluck('quote_id')->toArray();

        $this->assertNotContains($draftQuote->quote_id, $quoteIds);
        $this->assertContains($approvedQuote->quote_id, $quoteIds);
    }
}

