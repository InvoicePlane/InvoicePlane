<?php

namespace Tests\Feature\Products;

use Modules\Quotes\Services\QuoteAmountService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(QuoteAmountService::class)]

class QuoteItemAmountServiceTest extends AbstractServiceTestCase
{
    private QuoteItemAmountService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuoteItemAmountService();
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amounts_in_legacy_mode(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        // Mock config_item to return legacy mode
        if ( ! function_exists('config_item')) {
            function config_item($key)
            {
                if ($key === 'legacy_calculation') {
                    return true;
                }
            }
        }

        $item = \Modules\Quotes\Models\QuoteItem::create([
            'quote_id'             => $quote->quote_id,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2,
            'item_price'           => 100.00,
            'item_discount_amount' => 5.00,
        ]);

        /* Act */
        $this->service->calculate($item->item_id);

        /* Assert */
        $itemAmount = \Modules\Quotes\Models\QuoteItemAmount::query()->where('item_id', $item->item_id)->first();
        $this->assertNotNull($itemAmount);
        $this->assertEquals(200.00, $itemAmount->item_subtotal); // 2 * 100
        $this->assertEquals(10.00, $itemAmount->item_discount); // 5 * 2
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amounts_in_new_mode(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        // Mock config_item to return new mode (false for legacy)
        if ( ! function_exists('config_item')) {
            function config_item($key)
            {
                if ($key === 'legacy_calculation') {
                    return false;
                }
            }
        }

        $item = \Modules\Quotes\Models\QuoteItem::create([
            'quote_id'             => $quote->quote_id,
            'item_name'            => 'Test Item',
            'item_quantity'        => 2,
            'item_price'           => 100.00,
            'item_discount_amount' => 5.00,
        ]);

        $globalDiscount = [
            'amount'         => 20.00,
            'items_subtotal' => 200.00,
        ];

        /* Act */
        $this->service->calculate($item->item_id, $globalDiscount);

        /* Assert */
        $itemAmount = \Modules\Quotes\Models\QuoteItemAmount::query()->where('item_id', $item->item_id)->first();
        $this->assertNotNull($itemAmount);
        $this->assertEquals(200.00, $itemAmount->item_subtotal); // 2 * 100
        // Global discount should be applied proportionally
        $this->assertArrayHasKey('item', $globalDiscount);
    }

    #[Test]
    public function it_applies_global_discount_proportionally(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        $item = \Modules\Quotes\Models\QuoteItem::create([
            'quote_id'             => $quote->quote_id,
            'item_name'            => 'Test Item',
            'item_quantity'        => 1,
            'item_price'           => 100.00,
            'item_discount_amount' => 0.00,
        ]);

        // Set up global discount scenario
        $globalDiscount = [
            'amount'         => 50.00, // $50 discount
            'items_subtotal' => 200.00, // Total items worth $200
        ];

        /* Act */
        $this->service->calculate($item->item_id, $globalDiscount);

        /* Assert */
        $itemAmount = \Modules\Quotes\Models\QuoteItemAmount::query()->where('item_id', $item->item_id)->first();
        $this->assertNotNull($itemAmount);
        $this->assertEquals(100.00, $itemAmount->item_subtotal);
        // This item should get 25.00 discount (50% of total discount since it's 50% of subtotal)
        $this->assertArrayHasKey('item', $globalDiscount);
        $this->assertEquals(25.00, $globalDiscount['item']);
    }
}
