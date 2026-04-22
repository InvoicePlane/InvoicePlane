<?php

namespace Tests\Unit\Quotes;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(\Mdl_Quote_Item_Amounts::class)]
class QuoteItemServiceTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $quoteService           = $this->createMock(\Modules\Quotes\Services\QuoteService::class);
        $quoteAmountService     = new QuoteAmountService($quoteService);
        $quoteItemAmountService = new QuoteItemAmountService();
        $this->service          = new QuoteItemService($quoteAmountService, $quoteItemAmountService);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_id', $rules);
        $this->assertArrayHasKey('item_name', $rules);
        $this->assertArrayHasKey('item_description', $rules);
        $this->assertArrayHasKey('item_quantity', $rules);
        $this->assertArrayHasKey('item_price', $rules);
    }

    #[Group('crud')]
    #[Test]
    public function it_saves_item(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        $itemData = [
            'quote_id'         => $quote->quote_id,
            'item_name'        => 'Test Item',
            'item_description' => 'Test Description',
            'item_quantity'    => 2,
            'item_price'       => 100.00,
        ];

        /* Act */
        $item = $this->service->saveItem($itemData);

        /* Assert */
        $this->assertNotNull($item);
        $this->assertEquals('Test Item', $item->item_name);
        $this->assertEquals('Test Description', $item->item_description);
        $this->assertEquals(2, $item->item_quantity);
        $this->assertEquals(100.00, $item->item_price);
        $this->assertDatabaseHas('ip_quote_items', [
            'quote_id'  => $quote->quote_id,
            'item_name' => 'Test Item',
        ]);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_item(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        $item = \Modules\Quotes\Models\QuoteItem::create([
            'quote_id'      => $quote->quote_id,
            'item_name'     => 'Test Item',
            'item_quantity' => 1,
            'item_price'    => 50.00,
        ]);

        /* Act */
        $result = $this->service->deleteItem($item->item_id);

        /* Assert */
        $this->assertTrue($result);
        $this->assertDatabaseMissing('ip_quote_items', [
            'item_id' => $item->item_id,
        ]);
    }

    #[Test]
    public function it_gets_items_subtotal(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        // Create two items
        $item1 = \Modules\Quotes\Models\QuoteItem::create([
            'quote_id'      => $quote->quote_id,
            'item_name'     => 'Item 1',
            'item_quantity' => 2,
            'item_price'    => 100.00,
        ]);
        $item2 = \Modules\Quotes\Models\QuoteItem::create([
            'quote_id'      => $quote->quote_id,
            'item_name'     => 'Item 2',
            'item_quantity' => 1,
            'item_price'    => 50.00,
        ]);

        // Create item amounts
        \Modules\Quotes\Models\QuoteItemAmount::create([
            'item_id'        => $item1->item_id,
            'item_subtotal'  => 200.00,
            'item_tax_total' => 0.00,
            'item_discount'  => 0.00,
            'item_total'     => 200.00,
        ]);
        \Modules\Quotes\Models\QuoteItemAmount::create([
            'item_id'        => $item2->item_id,
            'item_subtotal'  => 50.00,
            'item_tax_total' => 0.00,
            'item_discount'  => 0.00,
            'item_total'     => 50.00,
        ]);

        /* Act */
        $subtotal = $this->service->getItemsSubtotal($quote->quote_id);

        /* Assert */
        $this->assertEquals(250.00, $subtotal);
    }
}
