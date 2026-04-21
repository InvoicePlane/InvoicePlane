<?php

namespace Tests\Feature\Products;

use Modules\Quotes\Services\QuoteAmountService;
use Modules\Quotes\Services\QuoteService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(QuoteAmountService::class)]

class QuoteAmountServiceTest extends AbstractServiceTestCase
{
    private QuoteAmountService $service;

    private QuoteService $quoteService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->quoteService = new QuoteService();
        $this->service      = new QuoteAmountService($this->quoteService);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_global_discount(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        // Create items with amounts
        $item = \Modules\Quotes\Models\QuoteItem::create([
            'quote_id'      => $quote->quote_id,
            'item_name'     => 'Test Item',
            'item_quantity' => 1,
            'item_price'    => 100.00,
        ]);

        // Create item amount with discount
        \Modules\Quotes\Models\QuoteItemAmount::create([
            'item_id'        => $item->item_id,
            'item_subtotal'  => 100.00,
            'item_tax_total' => 10.00,
            'item_discount'  => 5.00,
            'item_total'     => 105.00,
        ]);

        /** Act */
        $globalDiscount = $this->service->getGlobalDiscount($quote->quote_id);

        /* Assert */
        // Global discount = subtotal - (total - tax + discount)
        // = 100 - (105 - 10 + 5) = 100 - 100 = 0
        $this->assertEquals(0.00, $globalDiscount);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_discount_for_legacy_mode(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture([
            'quote_id'               => 100,
            'client_id'              => 1,
            'quote_discount_amount'  => 10.00,
            'quote_discount_percent' => 5.00,
        ]);

        $quoteTotal    = 200.00;
        $decimalPlaces = 2;

        /** Act */
        $result = $this->service->calculateDiscount($quote->quote_id, $quoteTotal, $decimalPlaces);

        /* Assert */
        // Total: 200.00
        // After amount discount: 200 - 10 = 190.00
        // After percent discount: 190 - (190 * 5/100) = 190 - 9.50 = 180.50
        $this->assertEquals(180.50, $result);
    }

    #[Test]
    public function it_gets_total_quoted_for_all_time(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);

        // Create multiple quotes with amounts
        $quote1 = $this->createQuoteFixture([
            'quote_id'           => 100,
            'client_id'          => 1,
            'quote_date_created' => '2024-01-15',
        ]);
        $quote2 = $this->createQuoteFixture([
            'quote_id'           => 101,
            'client_id'          => 1,
            'quote_date_created' => '2024-02-15',
        ]);

        \Modules\Quotes\Models\QuoteAmount::create([
            'quote_id'             => $quote1->quote_id,
            'quote_item_subtotal'  => 100.00,
            'quote_item_tax_total' => 10.00,
            'quote_total'          => 110.00,
        ]);

        \Modules\Quotes\Models\QuoteAmount::create([
            'quote_id'             => $quote2->quote_id,
            'quote_item_subtotal'  => 200.00,
            'quote_item_tax_total' => 20.00,
            'quote_total'          => 220.00,
        ]);

        /** Act */
        $total = $this->service->getTotalQuoted();

        /* Assert */
        $this->assertEquals(330.00, $total); // 110 + 220
    }

    #[Test]
    public function it_gets_status_totals_for_period(): void
    {
        $totals = $this->service->getStatusTotals('this-month');

        $this->assertIsArray($totals);
        // Should have entries for all 6 statuses
        $this->assertCount(6, $totals);
    }
}
