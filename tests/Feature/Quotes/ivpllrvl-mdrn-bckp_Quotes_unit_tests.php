<?php

namespace Modules\Quotes\Tests\Unit;

use Modules\Quotes\Services\QuoteAmountService;
use Modules\Quotes\Services\QuoteService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
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
        $this->service = new QuoteAmountService($this->quoteService);
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

#[CoversClass(QuoteItemAmountService::class)]
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
        $this->markTestIncomplete('Requires database setup with quote items');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amounts_in_new_mode(): void
    {
        $this->markTestIncomplete('Requires database setup with quote items');
    }

    #[Test]
    public function it_applies_global_discount_proportionally(): void
    {
        $this->markTestIncomplete('Requires database setup');
    }
}

#[CoversClass(QuoteItemService::class)]
class QuoteItemServiceTest extends AbstractServiceTestCase
{
    private QuoteItemService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $quoteService = $this->createMock(\Modules\Quotes\Services\QuoteService::class);
        $quoteAmountService = new QuoteAmountService($quoteService);
        $quoteItemAmountService = new QuoteItemAmountService();
        $this->service = new QuoteItemService($quoteAmountService, $quoteItemAmountService);
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
        $this->markTestIncomplete('Requires database setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_item(): void
    {
        $this->markTestIncomplete('Requires database setup');
    }

    #[Test]
    public function it_gets_items_subtotal(): void
    {
        $this->markTestIncomplete('Requires database setup');
    }
}

#[CoversClass(QuoteService::class)]
class QuoteServiceTest extends AbstractServiceTestCase
{
    private QuoteService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new QuoteService();
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_quote_statuses(): void
    {
        $statuses = $this->service->getStatuses();

        $this->assertIsArray($statuses);
        $this->assertCount(6, $statuses);
        $this->assertArrayHasKey('1', $statuses); // Draft
        $this->assertArrayHasKey('2', $statuses); // Sent
        $this->assertArrayHasKey('3', $statuses); // Viewed
        $this->assertArrayHasKey('4', $statuses); // Approved
        $this->assertArrayHasKey('5', $statuses); // Rejected
        $this->assertArrayHasKey('6', $statuses); // Canceled

        foreach ($statuses as $status) {
            $this->assertArrayHasKey('label', $status);
            $this->assertArrayHasKey('class', $status);
            $this->assertArrayHasKey('href', $status);
        }
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('client_id', $rules);
        $this->assertArrayHasKey('quote_date_created', $rules);
        $this->assertArrayHasKey('invoice_group_id', $rules);
        $this->assertArrayHasKey('quote_password', $rules);
        $this->assertArrayHasKey('user_id', $rules);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_save_validation_rules_without_quote_id(): void
    {
        $rules = $this->service->getSaveValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_number', $rules);
        $this->assertEquals('unique:ip_quotes,quote_number', $rules['quote_number']);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_save_validation_rules_with_quote_id(): void
    {
        $quoteId = 123;
        $rules = $this->service->getSaveValidationRules($quoteId);

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_number', $rules);
        $this->assertStringContainsString('unique:ip_quotes,quote_number', $rules['quote_number']);
        $this->assertStringContainsString((string)$quoteId, $rules['quote_number']);
    }

    #[Test]
    public function it_generates_url_key(): void
    {
        $urlKey = $this->service->generateUrlKey();

        $this->assertIsString($urlKey);
        $this->assertEquals(32, strlen($urlKey)); // 16 bytes = 32 hex chars
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_date_due(): void
    {
        // Mock the get_setting function would normally be needed here
        // For now, test the basic functionality
        $this->markTestIncomplete('Requires mocking get_setting function');
    }
}

#[CoversClass(QuoteTaxRateService::class)]
class QuoteTaxRateServiceTest extends AbstractServiceTestCase
{
    private QuoteTaxRateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $quoteService = $this->createMock(\Modules\Quotes\Services\QuoteService::class);
        $quoteAmountService = new QuoteAmountService($quoteService);
        $this->service = new QuoteTaxRateService($quoteAmountService);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->service->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('include_item_tax', $rules);
    }

    #[Group('crud')]
    #[Test]
    public function it_saves_tax_rate_in_legacy_mode(): void
    {
        $this->markTestIncomplete('Requires database setup and legacy mode');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_not_in_legacy_mode(): void
    {
        $this->markTestIncomplete('Requires config_item mock');
    }
}

