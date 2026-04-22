<?php

namespace Tests\Feature\Quotes;

use Modules\Quotes\Services\QuoteAmountService;
use Modules\Quotes\Services\QuoteService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(QuoteAmountService::class)]

class QuoteAmountServiceTest extends AbstractTestCase
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
        $this->markTestIncomplete('Requires database setup with quote items');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_discount_for_legacy_mode(): void
    {
        $this->markTestIncomplete('Requires database setup with quote data');
    }

    #[Test]
    public function it_gets_total_quoted_for_all_time(): void
    {
        $this->markTestIncomplete('Requires database setup with quote amounts');
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
