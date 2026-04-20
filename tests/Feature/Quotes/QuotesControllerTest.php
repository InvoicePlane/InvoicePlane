<?php

namespace Tests\Feature\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Modules\Quotes\Controllers\QuotesController;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Models\QuoteAmount;
use Modules\Quotes\Models\QuoteItem;
use Modules\Quotes\Models\QuoteTaxRate;
use Modules\Crm\Models\Client;
use Modules\Users\Models\User;

/**
 * QuotesController Feature Tests
 * 
 * Comprehensive test suite for QuotesController covering all methods
 * with data integrity validation, edge cases, and business logic verification
 * 
 * @package Tests\Feature\Controllers
 */
#[CoversClass(QuotesController::class)]
class QuotesControllerTest extends TestCase
{
    /**
     * Test that index method redirects to all quotes status view
     * 
     * @return void
     */
    #[Test]
    public function it_redirects_to_all_status_view_from_index(): void
    {
        /** Arrange */
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->index();
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('quotes.status', ['status' => 'all']), $response->getTargetUrl());
    }

    /**
     * Test that status method displays only draft quotes when draft status is selected
     * 
     * @return void
     */
    #[Test]
    public function it_displays_only_draft_quotes_when_draft_status_selected(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $sentQuote = Quote::factory()->sent()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->status('draft');
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('quotes', $viewData);
        $this->assertArrayHasKey('status', $viewData);
        $this->assertEquals('draft', $viewData['status']);
        
        /** Verify only draft quotes are returned */
        $quoteIds = $viewData['quotes']->pluck('quote_id')->toArray();
        $this->assertContains($draftQuote->quote_id, $quoteIds);
        $this->assertNotContains($sentQuote->quote_id, $quoteIds);
    }

    /**
     * Test that status method displays all quotes when 'all' status is selected
     * 
     * @return void
     */
    #[Test]
    public function it_displays_all_quotes_when_all_status_selected(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $sentQuote = Quote::factory()->sent()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->status('all');
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('quotes', $viewData);
        $this->assertEquals('all', $viewData['status']);
        
        /** Verify all quotes are returned */
        $quoteIds = $viewData['quotes']->pluck('quote_id')->toArray();
        $this->assertContains($draftQuote->quote_id, $quoteIds);
        $this->assertContains($sentQuote->quote_id, $quoteIds);
    }

    /**
     * Test that status method includes quote statuses in view data
     * 
     * @return void
     */
    #[Test]
    public function it_includes_quote_statuses_in_view_data_for_status_method(): void
    {
        /** Arrange */
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->status('all');
        
        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('quote_statuses', $viewData);
        $this->assertIsArray($viewData['quote_statuses']);
        $this->assertNotEmpty($viewData['quote_statuses']);
    }

    /**
     * Test that view method displays quote details with all related data
     * 
     * @return void
     */
    #[Test]
    public function it_displays_quote_details_with_items_and_amounts(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $item1 = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);
        $item2 = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->view($quote->quote_id);
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('quote', $viewData);
        $this->assertArrayHasKey('items', $viewData);
        $this->assertArrayHasKey('quote_id', $viewData);
        
        $this->assertEquals($quote->quote_id, $viewData['quote']->quote_id);
        $this->assertEquals($quote->quote_id, $viewData['quote_id']);
        $this->assertCount(2, $viewData['items']);
    }

    /**
     * Test that view method returns 404 when quote is not found
     * 
     * @return void
     */
    #[Test]
    public function it_returns_404_when_viewing_non_existent_quote(): void
    {
        /** Arrange */
        $nonExistentQuoteId = 99999;
        $controller = new QuotesController();
        
        /** Act & Assert */
        $this->expectException(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class);
        $controller->view($nonExistentQuoteId);
    }

    /**
     * Test that view method includes custom fields in view data
     * 
     * @return void
     */
    #[Test]
    public function it_includes_custom_fields_in_quote_view_data(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->view($quote->quote_id);
        
        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('custom_fields', $viewData);
        $this->assertArrayHasKey('custom_values', $viewData);
    }

    /**
     * Test that view method includes tax rates in view data
     * 
     * @return void
     */
    #[Test]
    public function it_includes_tax_rates_in_quote_view_data(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->view($quote->quote_id);
        
        /** Assert */
        $viewData = $response->getData();
        $this->assertArrayHasKey('tax_rates', $viewData);
        $this->assertArrayHasKey('quote_tax_rates', $viewData);
    }

    /**
     * Test that delete method removes quote and redirects to index
     * 
     * @return void
     */
    #[Test]
    public function it_deletes_quote_and_redirects_to_index(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $quoteId = $quote->quote_id;
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->delete($quoteId);
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('quotes.index'), $response->getTargetUrl());
        
        /** Verify quote was deleted */
        $this->assertNull(Quote::find($quoteId));
    }

    /**
     * Test that delete method also deletes related records (items, tax rates, amounts)
     * 
     * @return void
     */
    #[Test]
    public function it_deletes_quote_and_all_related_records(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $item = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);
        $taxRate = QuoteTaxRate::factory()->create(['quote_id' => $quote->quote_id]);
        
        $quoteId = $quote->quote_id;
        $itemId = $item->item_id;
        $taxRateId = $taxRate->quote_tax_rate_id;
        
        $controller = new QuotesController();
        
        /** Act */
        $controller->delete($quoteId);
        
        /** Assert - verify all related records are deleted */
        $this->assertNull(Quote::find($quoteId));
        $this->assertNull(QuoteItem::find($itemId));
        $this->assertNull(QuoteTaxRate::find($taxRateId));
    }

    /**
     * Test that deleteQuoteTax method removes tax rate and recalculates quote
     * 
     * @return void
     */
    #[Test]
    public function it_removes_tax_rate_and_recalculates_quote(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $taxRate = QuoteTaxRate::factory()->create([
            'quote_id' => $quote->quote_id,
            'tax_rate_id' => 1,
        ]);
        
        $quoteTaxRateId = $taxRate->quote_tax_rate_id;
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->deleteQuoteTax($quote->quote_id, $quoteTaxRateId);
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertEquals(route('quotes.view', ['quote_id' => $quote->quote_id]), $response->getTargetUrl());
        
        /** Verify tax rate was deleted */
        $this->assertNull(QuoteTaxRate::find($quoteTaxRateId));
    }

    /**
     * Test that deleteQuoteTax method redirects back to quote view
     * 
     * @return void
     */
    #[Test]
    public function it_redirects_to_quote_view_after_deleting_tax_rate(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $taxRate = QuoteTaxRate::factory()->create(['quote_id' => $quote->quote_id]);
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->deleteQuoteTax($quote->quote_id, $taxRate->quote_tax_rate_id);
        
        /** Assert */
        $this->assertEquals(route('quotes.view', ['quote_id' => $quote->quote_id]), $response->getTargetUrl());
        $this->assertTrue($response->getSession()->has('success'));
    }

    /**
     * Test that recalculateAllQuotes method processes all quotes in the system
     * 
     * @return void
     */
    #[Test]
    public function it_recalculates_all_quotes_successfully(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $quote1 = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $quote2 = Quote::factory()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->recalculateAllQuotes();
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        $this->assertTrue($response->getSession()->has('success'));
    }

    /**
     * Test that recalculateAllQuotes method handles empty quote list gracefully
     * 
     * @return void
     */
    #[Test]
    public function it_handles_empty_quote_list_when_recalculating_all_quotes(): void
    {
        /** Arrange */
        /** Delete all quotes */
        Quote::query()->delete();
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->recalculateAllQuotes();
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\Http\RedirectResponse::class, $response);
        /** Should still return success even with no quotes */
        $this->assertTrue($response->getSession()->has('success'));
    }

    /**
     * Test that status method paginates results correctly
     * 
     * @return void
     */
    #[Test]
    public function it_paginates_quote_results_correctly(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        /** Create 20 draft quotes (more than the 15 per page limit) */
        for ($i = 0; $i < 20; $i++) {
            Quote::factory()->draft()->create([
                'client_id' => $client->client_id,
                'user_id' => $user->user_id,
            ]);
        }
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->status('draft', 0);
        
        /** Assert */
        $viewData = $response->getData();
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $viewData['quotes']);
        $this->assertEquals(15, $viewData['quotes']->perPage());
        $this->assertLessThanOrEqual(15, $viewData['quotes']->count());
    }

    /**
     * Test that status method filters quotes by sent status correctly
     * 
     * @return void
     */
    #[Test]
    public function it_displays_only_sent_quotes_when_sent_status_selected(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $sentQuote = Quote::factory()->sent()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->status('sent');
        
        /** Assert */
        $viewData = $response->getData();
        $quoteIds = $viewData['quotes']->pluck('quote_id')->toArray();
        
        $this->assertNotContains($draftQuote->quote_id, $quoteIds);
        $this->assertContains($sentQuote->quote_id, $quoteIds);
    }

    /**
     * Test that status method filters quotes by approved status correctly
     * 
     * @return void
     */
    #[Test]
    public function it_displays_only_approved_quotes_when_approved_status_selected(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        
        $draftQuote = Quote::factory()->draft()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $approvedQuote = Quote::factory()->approved()->create([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
        ]);
        
        $controller = new QuotesController();
        
        /** Act */
        $response = $controller->status('approved');
        
        /** Assert */
        $viewData = $response->getData();
        $quoteIds = $viewData['quotes']->pluck('quote_id')->toArray();
        
        $this->assertNotContains($draftQuote->quote_id, $quoteIds);
        $this->assertContains($approvedQuote->quote_id, $quoteIds);
    }
}
