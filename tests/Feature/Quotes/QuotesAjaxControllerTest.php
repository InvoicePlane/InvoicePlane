<?php

namespace Tests\Feature\Controllers;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversClass;
use Modules\Quotes\Controllers\QuotesAjaxController;
use Modules\Quotes\Models\Quote;
use Modules\Quotes\Models\QuoteItem;
use Modules\Quotes\Models\QuoteTaxRate;
use Modules\Quotes\Models\QuoteAmount;
use Modules\Crm\Models\Client;
use Modules\Users\Models\User;
use Modules\Products\Models\TaxRate;
use Modules\Core\Models\InvoiceGroup;
use Modules\Invoices\Models\Invoice;

/**
 * Test suite for QuotesAjaxController
 * 
 * Tests AJAX operations including save, copy, create, and quote-to-invoice conversion
 */
#[CoversClass(QuotesAjaxController::class)]
class QuotesAjaxControllerTest extends TestCase
{
    private QuotesAjaxController $controller;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->controller = new QuotesAjaxController();
    }
    
    /**
     * Test saving a quote with items returns success
     */
    #[Test]
    public function it_saves_quote_with_items_and_returns_success(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create(['quote_status_id' => 1]);
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'quote_status_id' => 2,
            'quote_date_created' => date('Y-m-d'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'items' => json_encode([
                (object) [
                    'item_name' => 'Test Item',
                    'item_quantity' => 2,
                    'item_price' => 100.00,
                    'item_order' => 1
                ]
            ])
        ]);
        
        /** Act */
        $response = $this->controller->save($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        
        /** Verify quote was updated */
        $updatedQuote = Quote::find($quote->quote_id);
        $this->assertEquals(2, $updatedQuote->quote_status_id);
    }
    
    /**
     * Test saving quote with validation errors returns error response
     */
    #[Test]
    public function it_returns_validation_errors_when_saving_invalid_quote(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            /** Missing required fields */
        ]);
        
        /** Act */
        $response = $this->controller->save($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(0, $data['success']);
        $this->assertArrayHasKey('validation_errors', $data);
    }
    
    /**
     * Test saving quote with discount percent prevents discount amount
     */
    #[Test]
    public function it_prevents_both_discount_types_when_saving_quote(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'quote_discount_percent' => 10,
            'quote_discount_amount' => 50,
            'items' => json_encode([])
        ]);
        
        /** Act */
        $response = $this->controller->save($request);
        
        /** Assert */
        $updatedQuote = Quote::find($quote->quote_id);
        $this->assertEquals(10, $updatedQuote->quote_discount_percent);
        $this->assertEquals(0, $updatedQuote->quote_discount_amount);
    }
    
    /**
     * Test saving quote with empty item name but quantity returns validation error
     */
    #[Test]
    public function it_returns_error_when_item_has_quantity_but_no_name(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'items' => json_encode([
                (object) [
                    'item_name' => '',
                    'item_quantity' => 2,
                    'item_price' => 100.00
                ]
            ])
        ]);
        
        /** Act */
        $response = $this->controller->save($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(0, $data['success']);
        $this->assertArrayHasKey('item_name', $data['validation_errors']);
    }
    
    /**
     * Test saving quote tax rate in legacy mode
     */
    #[Test]
    public function it_saves_quote_tax_rate_in_legacy_calculation_mode(): void
    {
        /** Arrange */
        config(['legacy_calculation' => true]);
        $quote = Quote::factory()->create();
        $taxRate = TaxRate::factory()->create();
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'tax_rate_id' => $taxRate->tax_rate_id,
            'include_item_tax' => 1
        ]);
        
        /** Act */
        $response = $this->controller->saveQuoteTaxRate($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        
        /** Verify tax rate was saved */
        $quoteTaxRate = QuoteTaxRate::where('quote_id', $quote->quote_id)->first();
        $this->assertNotNull($quoteTaxRate);
    }
    
    /**
     * Test deleting quote item returns success
     */
    #[Test]
    public function it_deletes_quote_item_and_returns_success(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $item = QuoteItem::factory()->create(['quote_id' => $quote->quote_id]);
        $request = $this->createMockRequest(['item_id' => $item->item_id]);
        
        /** Act */
        $response = $this->controller->deleteItem($request, $quote->quote_id);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        
        /** Verify item was deleted */
        $this->assertNull(QuoteItem::find($item->item_id));
    }
    
    /**
     * Test deleting quote item for non-existent quote returns failure
     */
    #[Test]
    public function it_returns_failure_when_deleting_item_for_non_existent_quote(): void
    {
        /** Arrange */
        $request = $this->createMockRequest(['item_id' => 999]);
        
        /** Act */
        $response = $this->controller->deleteItem($request, 99999);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(0, $data['success']);
    }
    
    /**
     * Test getting quote item by ID returns item data
     */
    #[Test]
    public function it_returns_quote_item_data_when_getting_item(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $item = QuoteItem::factory()->create([
            'quote_id' => $quote->quote_id,
            'item_name' => 'Test Item',
            'item_quantity' => 5,
            'item_price' => 50.00
        ]);
        $request = $this->createMockRequest(['item_id' => $item->item_id]);
        
        /** Act */
        $response = $this->controller->getItem($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Test Item', $data['item_name']);
        $this->assertEquals(5, $data['item_quantity']);
        $this->assertEquals(50.00, $data['item_price']);
    }
    
    /**
     * Test getting non-existent item returns empty array
     */
    #[Test]
    public function it_returns_empty_array_when_getting_non_existent_item(): void
    {
        /** Arrange */
        $request = $this->createMockRequest(['item_id' => 99999]);
        
        /** Act */
        $response = $this->controller->getItem($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEmpty($data);
    }
    
    /**
     * Test copying quote creates new quote with all data
     */
    #[Test]
    public function it_copies_quote_with_all_items_and_tax_rates(): void
    {
        /** Arrange */
        $sourceQuote = Quote::factory()->create();
        QuoteItem::factory()->count(3)->create(['quote_id' => $sourceQuote->quote_id]);
        QuoteTaxRate::factory()->count(2)->create(['quote_id' => $sourceQuote->quote_id]);
        
        $client = Client::factory()->create();
        $invoiceGroup = InvoiceGroup::factory()->create();
        
        $request = $this->createMockRequest([
            'quote_id' => $sourceQuote->quote_id,
            'client_id' => $client->client_id,
            'invoice_group_id' => $invoiceGroup->invoice_group_id,
            'user_id' => $sourceQuote->user_id
        ]);
        
        /** Act */
        $response = $this->controller->copyQuote($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('quote_id', $data);
        
        $newQuoteId = $data['quote_id'];
        $newQuote = Quote::find($newQuoteId);
        $this->assertNotNull($newQuote);
        
        /** Verify items were copied */
        $copiedItems = QuoteItem::where('quote_id', $newQuoteId)->count();
        $this->assertEquals(3, $copiedItems);
        
        /** Verify tax rates were copied */
        $copiedTaxRates = QuoteTaxRate::where('quote_id', $newQuoteId)->count();
        $this->assertEquals(2, $copiedTaxRates);
    }
    
    /**
     * Test changing quote user updates user_id
     */
    #[Test]
    public function it_changes_quote_user_and_returns_success(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $newUser = User::factory()->create();
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'user_id' => $newUser->user_id
        ]);
        
        /** Act */
        $response = $this->controller->changeUser($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        
        /** Verify user was changed */
        $updatedQuote = Quote::find($quote->quote_id);
        $this->assertEquals($newUser->user_id, $updatedQuote->user_id);
    }
    
    /**
     * Test changing quote user to non-existent user returns error
     */
    #[Test]
    public function it_returns_error_when_changing_to_non_existent_user(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'user_id' => 99999
        ]);
        
        /** Act */
        $response = $this->controller->changeUser($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(0, $data['success']);
        $this->assertArrayHasKey('validation_errors', $data);
    }
    
    /**
     * Test changing quote client updates client_id
     */
    #[Test]
    public function it_changes_quote_client_and_returns_success(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $newClient = Client::factory()->create();
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'client_id' => $newClient->client_id
        ]);
        
        /** Act */
        $response = $this->controller->changeClient($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        
        /** Verify client was changed */
        $updatedQuote = Quote::find($quote->quote_id);
        $this->assertEquals($newClient->client_id, $updatedQuote->client_id);
    }
    
    /**
     * Test creating new quote returns quote_id
     */
    #[Test]
    public function it_creates_new_quote_and_returns_quote_id(): void
    {
        /** Arrange */
        $client = Client::factory()->create();
        $user = User::factory()->create();
        $invoiceGroup = InvoiceGroup::factory()->create();
        
        $request = $this->createMockRequest([
            'client_id' => $client->client_id,
            'user_id' => $user->user_id,
            'invoice_group_id' => $invoiceGroup->invoice_group_id
        ]);
        
        /** Act */
        $response = $this->controller->create($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('quote_id', $data);
        
        /** Verify quote was created */
        $quoteId = $data['quote_id'];
        $quote = Quote::find($quoteId);
        $this->assertNotNull($quote);
        $this->assertEquals($client->client_id, $quote->client_id);
    }
    
    /**
     * Test converting quote to invoice creates invoice with all data
     */
    #[Test]
    public function it_converts_quote_to_invoice_with_all_items_and_tax_rates(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create([
            'quote_discount_amount' => 10.00,
            'quote_discount_percent' => 0
        ]);
        QuoteItem::factory()->count(3)->create(['quote_id' => $quote->quote_id]);
        QuoteTaxRate::factory()->count(2)->create(['quote_id' => $quote->quote_id]);
        
        $invoiceGroup = InvoiceGroup::factory()->create();
        
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'invoice_group_id' => $invoiceGroup->invoice_group_id
        ]);
        
        /** Act */
        $response = $this->controller->quoteToInvoice($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $this->assertEquals(1, $data['success']);
        $this->assertArrayHasKey('invoice_id', $data);
        
        $invoiceId = $data['invoice_id'];
        $invoice = Invoice::find($invoiceId);
        
        /** Verify invoice was created */
        $this->assertNotNull($invoice);
        $this->assertEquals($quote->client_id, $invoice->client_id);
        $this->assertEquals(10.00, $invoice->invoice_discount_amount);
        
        /** Verify quote was linked to invoice */
        $updatedQuote = Quote::find($quote->quote_id);
        $this->assertEquals($invoiceId, $updatedQuote->invoice_id);
        
        /** Verify items were copied */
        $invoiceItems = Item::where('invoice_id', $invoiceId)->count();
        $this->assertEquals(3, $invoiceItems);
        
        /** Verify tax rates were copied */
        $invoiceTaxRates = InvoiceTaxRate::where('invoice_id', $invoiceId)->count();
        $this->assertEquals(2, $invoiceTaxRates);
    }
    
    /**
     * Test converting quote to invoice preserves item details
     */
    #[Test]
    public function it_preserves_item_details_when_converting_quote_to_invoice(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $quoteItem = QuoteItem::factory()->create([
            'quote_id' => $quote->quote_id,
            'item_name' => 'Original Item',
            'item_quantity' => 5,
            'item_price' => 100.00,
            'item_description' => 'Test description'
        ]);
        
        $invoiceGroup = InvoiceGroup::factory()->create();
        
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'invoice_group_id' => $invoiceGroup->invoice_group_id
        ]);
        
        /** Act */
        $response = $this->controller->quoteToInvoice($request);
        
        /** Assert */
        $data = json_decode($response->getContent(), true);
        $invoiceId = $data['invoice_id'];
        
        $invoiceItem = Item::where('invoice_id', $invoiceId)->first();
        $this->assertEquals('Original Item', $invoiceItem->item_name);
        $this->assertEquals(5, $invoiceItem->item_quantity);
        $this->assertEquals(100.00, $invoiceItem->item_price);
        $this->assertEquals('Test description', $invoiceItem->item_description);
    }
    
    /**
     * Test modal copy quote loads view with correct data
     */
    #[Test]
    public function it_loads_copy_quote_modal_with_invoice_groups_and_tax_rates(): void
    {
        /** Arrange */
        $quote = Quote::factory()->create();
        $client = Client::factory()->create();
        InvoiceGroup::factory()->count(3)->create();
        TaxRate::factory()->count(5)->create();
        
        $request = $this->createMockRequest([
            'quote_id' => $quote->quote_id,
            'client_id' => $client->client_id
        ]);
        
        /** Act */
        $response = $this->controller->modalCopyQuote($request);
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('invoice_groups', $viewData);
        $this->assertArrayHasKey('tax_rates', $viewData);
        $this->assertArrayHasKey('quote', $viewData);
        $this->assertCount(3, $viewData['invoice_groups']);
        $this->assertCount(5, $viewData['tax_rates']);
    }
    
    /**
     * Test modal create quote loads view with clients
     */
    #[Test]
    public function it_loads_create_quote_modal_with_clients_list(): void
    {
        /** Arrange */
        Client::factory()->count(10)->create();
        $client = Client::factory()->create();
        
        $request = $this->createMockRequest(['client_id' => $client->client_id]);
        
        /** Act */
        $response = $this->controller->modalCreateQuote($request);
        
        /** Assert */
        $this->assertInstanceOf(\Illuminate\View\View::class, $response);
        $viewData = $response->getData();
        
        $this->assertArrayHasKey('clients', $viewData);
        $this->assertArrayHasKey('client', $viewData);
        $this->assertGreaterThanOrEqual(10, count($viewData['clients']));
    }
    
    /**
     * Helper method to create mock request
     */
    private function createMockRequest(array $data): \Illuminate\Http\Request
    {
        $request = new \Illuminate\Http\Request();
        $request->replace($data);
        return $request;
    }
}
