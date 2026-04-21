<?php

namespace Tests\Feature\Quotes;

use Modules\Quotes\Services\QuoteAmountService;
use Modules\Quotes\Services\QuoteService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractServiceTestCase;

#[CoversClass(QuoteAmountService::class)]

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
        $rules   = $this->service->getSaveValidationRules($quoteId);

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_number', $rules);
        $this->assertStringContainsString('unique:ip_quotes,quote_number', $rules['quote_number']);
        $this->assertStringContainsString((string) $quoteId, $rules['quote_number']);
    }

    #[Test]
    public function it_generates_url_key(): void
    {
        $urlKey = $this->service->generateUrlKey();

        $this->assertIsString($urlKey);
        $this->assertEquals(32, mb_strlen($urlKey)); // 16 bytes = 32 hex chars
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
