<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Feature\Quotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Quotes;
use Tests\AbstractTestCase;
use Tests\Concerns\InteractsWithDatabase;

/**
 * QuotesController (CRM/Guest) Feature Tests.
 *
 * Tests guest portal quote viewing and approval.
 */
#[CoversClass(Quotes::class)]
class QuotesControllerTest extends AbstractTestCase
{
    use InteractsWithDatabase;

    /**
     * Test that index method redirects to all quotes status view.
     */
    #[Group('smoke')]
    #[Test]
    public function it_redirects_to_all_status_view_from_index(): void
    {
        /* Arrange */
        $user = $this->seedModel('User');

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes');

        /* Assert */
        $response->assertRedirect('/quotes/status/all');
    }

    /**
     * Test that status method displays only draft quotes when draft status is selected.
     */
    #[Group('smoke')]
    #[Test]
    public function it_displays_only_draft_quotes_when_draft_status_selected(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $draftQuote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $sentQuote = $this->seedModel('Quote', [
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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $draftQuote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $sentQuote = $this->seedModel('Quote', [
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
        /* Arrange */
        $user = $this->seedModel('User');

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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $item1 = $this->seedModel('QuoteItem', ['quote_id' => $quote->quote_id]);
        $item2 = $this->seedModel('QuoteItem', ['quote_id' => $quote->quote_id]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/view/' . $quote->quote_id);

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
        /* Arrange */
        $user               = $this->seedModel('User');
        $nonExistentQuoteId = 99999;

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/view/' . $nonExistentQuoteId);

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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/view/' . $quote->quote_id);

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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        $this->actingAs($user);
        $response = $this->get('/quotes/view/' . $quote->quote_id);

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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote = $this->seedModel('Quote', [
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
        $response = $this->post('/quotes/delete/' . $quote->quote_id);

        /* Assert */
        $response->assertRedirect('/quotes');

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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $item    = $this->seedModel('QuoteItem', ['quote_id' => $quote->quote_id]);
        $taxRate = $this->seedModel('QuoteTaxRate', ['quote_id' => $quote->quote_id]);

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
        $this->actingAs($user)->post('/quotes/delete/' . $quote->quote_id);

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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $taxRate = $this->seedModel('QuoteTaxRate', [
            'quote_id'    => $quote->quote_id,
            'tax_rate_id' => 1,
        ]);

        $quoteTaxRateId = $taxRate->quote_tax_rate_id;

        /* Act */
        /**
         * Note: Empty payload is correct - IDs are passed via route parameters
         * Route: POST /quotes/delete_tax/{quote_id}/{quote_tax_rate_id}.
         */
        $payload = [];

        $this->actingAs($user);
        $response = $this->post(
            '/quotes/delete_quote_tax/' . $quote->quote_id . '/' . $quoteTaxRateId,
            $payload
        );

        /* Assert */
        $response->assertRedirect('/quotes/view/' . $quote->quote_id);

        /* Verify tax rate was deleted */
        $this->assertNull(QuoteTaxRate::find($quoteTaxRateId));
    }

    /**
     * Test that deleteQuoteTax method redirects back to quote view.
     */
    #[Test]
    public function it_redirects_to_quote_view_after_deleting_tax_rate(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $taxRate = $this->seedModel('QuoteTaxRate', ['quote_id' => $quote->quote_id]);

        /* Act */
        /**
         * Note: Empty payload is correct - IDs are passed via route parameters
         * Route: POST /quotes/delete_tax/{quote_id}/{quote_tax_rate_id}.
         */
        $payload = [];

        $this->actingAs($user);
        $response = $this->post(
            '/quotes/delete_quote_tax/' . $quote->quote_id . '/' . $taxRate->quote_tax_rate_id,
            $payload
        );

        /* Assert */
        $response->assertRedirect('/quotes/view/' . $quote->quote_id);
        $response->assertSessionHas('success');
    }

    /**
     * Test that recalculateAllQuotes method processes all quotes in the system.
     */
    #[Group('exotic')]
    #[Test]
    public function it_recalculates_all_quotes_successfully(): void
    {
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $quote1 = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $quote2 = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        /* Act */
        /**
         * {}.
         */
        $recalculatePayload = [];

        $this->actingAs($user);
        $response = $this->post('/quotes/recalculate_all_quotes', $recalculatePayload);

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
        /* Arrange */
        $user = $this->seedModel('User');
        Quote::query()->delete();

        /* Act */
        /**
         * {}.
         */
        $recalculatePayload = [];

        $this->actingAs($user);
        $response = $this->post('/quotes/recalculate_all_quotes', $recalculatePayload);

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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        /* Create 20 draft quotes (more than the 15 per page limit) */
        for ($i = 0; $i < 20; $i++) {
            $this->seedModel('Quote', [
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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $draftQuote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $sentQuote = $this->seedModel('Quote', [
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
        /* Arrange */
        $user   = $this->seedModel('User');
        $client = $this->seedModel('Client');

        $draftQuote = $this->seedModel('Quote', [
            'client_id' => $client->client_id,
            'user_id'   => $user->user_id,
        ]);

        $approvedQuote = $this->seedModel('Quote', [
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


    // Migrated from BckpQuotesControllerTest.php
    #[\PHPUnit\Framework\Attributes\Test]
    public function it_redirects_to_status_all(): void
    {
        /* Act */
        $response = $this->get('/quotes');

        /* Assert */
        $response->assertRedirect('/quotes/status/all');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_quotes_by_status(): void
    {
        /* Arrange */
        $draftQuote    = $this->seedModel('Quote', ['status' => 'draft']);
        $sentQuote     = $this->seedModel('Quote', ['status' => 'sent']);
        $approvedQuote = $this->seedModel('Quote', ['status' => 'approved']);

        /* Act */
        $response = $this->get('/quotes/status/draft');
        $response->assertSee($draftQuote->title);
        $response->assertDontSee($sentQuote->title);
        $response->assertDontSee($approvedQuote->title);
        $response->assertStatus(200);

        /* Act */
        $response = $this->get('/quotes/status/sent');
        $response->assertSee($sentQuote->title);
        $response->assertDontSee($draftQuote->title);
        $response->assertDontSee($approvedQuote->title);
        $response->assertStatus(200);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_displays_quotes_index(): void
    {
        $response = $this->get('/quotes');

        $response->assertSuccessful();
        $response->assertViewHas('quotes');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_new_quote_with_single_item(): void
    {
        $quoteData = [
            'client_id'          => $this->client->client_id,
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(30)->format('Y-m-d'),
            'quote_status_id'    => 1,
            'quote_group_id'     => $this->quoteGroup->invoice_group_id,
            'items'              => [
                0 => [
                    'item_name'        => 'Quoted Service',
                    'item_description' => 'Service description for quote',
                    'item_quantity'    => 1,
                    'item_price'       => 250.00,
                ],
            ],
        ];

        $response = $this->post('/quotes/form', $quoteData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'client_id'       => $this->client->client_id,
            'quote_status_id' => 1,
        ]);
        $this->assertDatabaseHas('ip_quote_items', [
            'item_name'  => 'Quoted Service',
            'item_price' => 250.00,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_quote_with_multiple_items(): void
    {
        $quoteData = [
            'client_id'          => $this->client->client_id,
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(30)->format('Y-m-d'),
            'items'              => [
                0 => [
                    'item_name'     => 'Consultation',
                    'item_quantity' => 2,
                    'item_price'    => 150.00,
                ],
                1 => [
                    'item_name'     => 'Implementation',
                    'item_quantity' => 1,
                    'item_price'    => 500.00,
                ],
                2 => [
                    'item_name'     => 'Training',
                    'item_quantity' => 3,
                    'item_price'    => 100.00,
                ],
            ],
        ];

        $response = $this->post('/quotes/form', $quoteData);

        $response->assertRedirect();
        $quote = \Tests\Feature\Invoices\Quote::where('client_id', $this->client->client_id)->latest()->first();
        $this->assertCount(3, $quote->items);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_creates_quote_with_tax_rates(): void
    {
        $taxRate = $this->seedModel('TaxRate', ['tax_rate_percent' => 21.00]);

        $quoteData = [
            'client_id'          => $this->client->client_id,
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(15)->format('Y-m-d'),
            'items'              => [
                0 => [
                    'item_name'        => 'Taxable Service',
                    'item_quantity'    => 1,
                    'item_price'       => 200.00,
                    'item_tax_rate_id' => $taxRate->tax_rate_id,
                ],
            ],
        ];

        $response = $this->post('/quotes/form', $quoteData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quote_items', [
            'item_name'        => 'Taxable Service',
            'item_tax_rate_id' => $taxRate->tax_rate_id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_views_quote_details(): void
    {
        $quote = $this->seedModel('Quote', ['client_id' => $this->client->client_id]);
        $this->seedModelMany('QuoteItem', 2, ['quote_id' => $quote->quote_id]);

        $response = $this->get('/quotes/view/' . $quote->quote_id);

        $response->assertSuccessful();
        $response->assertViewHas('quote', function ($viewQuote) use ($quote): bool {
            return $viewQuote->quote_id === $quote->quote_id;
        });
        $response->assertViewHas('items', function ($items): bool {
            return count($items) === 2;
        });
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_loads_quote_form(): void
    {
        $response = $this->get('/quotes/form');

        $response->assertSuccessful();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_generates_quote_pdf(): void
    {
        $quote = $this->seedModel('Quote');

        $response = $this->get('/quotes/generate_pdf/' . $quote->quote_id);

        $response->assertSuccessful();
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_loads_quote_edit_form(): void
    {
        $quote = $this->seedModel('Quote');

        $response = $this->get('/quotes/form/' . $quote->quote_id);

        $response->assertSuccessful();
        $response->assertViewHas('quote');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_quote_details(): void
    {
        $quote = $this->seedModel('Quote', [
            'client_id'       => $this->client->client_id,
            'quote_status_id' => 1,
        ]);

        $updateData = [
            'quote_date_expires' => now()->addDays(60)->format('Y-m-d'),
            'quote_status_id'    => 2,
            'quote_notes'        => 'Updated quote with extended expiry',
        ];

        $response = $this->post('/quotes/form/' . $quote->quote_id, $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 2,
            'quote_notes'     => 'Updated quote with extended expiry',
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_updates_quote_items_and_pricing(): void
    {
        $quote = $this->seedModel('Quote');
        $item  = $this->seedModel('QuoteItem', [
            'quote_id'  => $quote->quote_id,
            'item_name' => 'Original Name',
        ]);

        $updateData = [
            'items' => [
                $item->item_id => [
                    'item_name'        => 'Updated Service Name',
                    'item_description' => 'Updated service description',
                    'item_quantity'    => 2,
                    'item_price'       => 300.00,
                ],
            ],
        ];

        $response = $this->post('/quotes/form/' . $quote->quote_id, $updateData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quote_items', [
            'item_id'    => $item->item_id,
            'item_name'  => 'Updated Service Name',
            'item_price' => 300.00,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_marks_quote_as_sent(): void
    {
        $quote = $this->seedModel('Quote', ['quote_status_id' => 1]);

        $response = $this->post('/quotes/form/' . $quote->quote_id, [
            'quote_status_id' => 2,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 2,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_marks_quote_as_approved(): void
    {
        $quote = $this->seedModel('Quote', ['quote_status_id' => 2]);

        $response = $this->post('/quotes/form/' . $quote->quote_id, [
            'quote_status_id' => 3,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 3,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_marks_quote_as_rejected(): void
    {
        $quote = $this->seedModel('Quote', ['quote_status_id' => 2]);

        $response = $this->post('/quotes/form/' . $quote->quote_id, [
            'quote_status_id' => 4,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_quotes', [
            'quote_id'        => $quote->quote_id,
            'quote_status_id' => 4,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_converts_quote_to_invoice(): void
    {
        $quote = $this->seedModel('Quote', ['client_id' => $this->client->client_id]);
        $this->seedModelMany('QuoteItem', 2, ['quote_id' => $quote->quote_id]);

        $convertData = [
            'invoice_date_created' => now()->format('Y-m-d'),
            'invoice_date_due'     => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post('/quotes/convert_to_invoice/' . $quote->quote_id, $convertData);

        $response->assertRedirect();
        $this->assertDatabaseHas('ip_invoices', [
            'client_id' => $this->client->client_id,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_copies_existing_quote(): void
    {
        $originalQuote = $this->seedModel('Quote', ['client_id' => $this->client->client_id]);
        $this->seedModelMany('QuoteItem', 2, ['quote_id' => $originalQuote->quote_id]);

        $response = $this->post('/quotes/copy/' . $originalQuote->quote_id, [
            'quote_date_created' => now()->format('Y-m-d'),
            'quote_date_expires' => now()->addDays(45)->format('Y-m-d'),
        ]);

        $response->assertRedirect();
        $this->assertEquals(2, \Tests\Feature\Invoices\Quote::where('client_id', $this->client->client_id)->count());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_deletes_quote(): void
    {
        $quote = $this->seedModel('Quote');

        $response = $this->delete('/quotes/delete/' . $quote->quote_id);

        $response->assertRedirect();
        $this->assertDatabaseMissing('ip_quotes', ['quote_id' => $quote->quote_id]);
    }

}
