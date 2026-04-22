<?php

namespace Tests\Feature\Quotes;

use Modules\Quotes\Services\QuoteAmountService;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(QuoteAmountService::class)]

class QuoteTaxRateServiceTest extends AbstractTestCase
{
    private QuoteTaxRateService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $quoteService       = $this->createMock(\Modules\Quotes\Services\QuoteService::class);
        $quoteAmountService = new QuoteAmountService($quoteService);
        $this->service      = new QuoteTaxRateService($quoteAmountService);
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
