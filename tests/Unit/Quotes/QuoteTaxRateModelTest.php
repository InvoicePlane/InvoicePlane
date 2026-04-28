<?php

// TODO: InvoicePlane does not have namespaces yet - this will need to be refactored when namespaces are introduced
namespace Tests\Unit\Quotes;

use Mdl_Quote_Tax_Rates;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

#[CoversClass(Mdl_Quote_Tax_Rates::class)]

class QuoteTaxRateModelTest extends AbstractTestCase
{
    private $service;

    protected function setUp(): void
    {
        parent::setUp();
        $quoteService       = $this->createMock(\Modules\Quotes\Services\QuoteService::class);
        $quoteAmountService = new QuoteAmountService($quoteService);
        $this->model      = new QuoteTaxRateService($quoteAmountService);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->getValidationRules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_id', $rules);
        $this->assertArrayHasKey('tax_rate_id', $rules);
        $this->assertArrayHasKey('include_item_tax', $rules);
    }

    #[Group('crud')]
    #[Test]
    public function it_saves_tax_rate_in_legacy_mode(): void
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

        // Create a tax rate
        $taxRate = \Modules\Products\Models\TaxRate::create([
            'tax_rate_name'    => 'VAT',
            'tax_rate_percent' => 20.00,
        ]);

        $data = [
            'quote_id'               => $quote->quote_id,
            'tax_rate_id'            => $taxRate->tax_rate_id,
            'include_item_tax'       => 0,
            'quote_tax_rate_percent' => 20.00,
        ];

        /* Act */
        $quoteTaxRate = $this->model->saveTaxRate($data);

        /* Assert */
        $this->assertNotNull($quoteTaxRate);
        $this->assertEquals($quote->quote_id, $quoteTaxRate->quote_id);
        $this->assertEquals($taxRate->tax_rate_id, $quoteTaxRate->tax_rate_id);
        $this->assertEquals(20.00, $quoteTaxRate->quote_tax_rate_percent);

        // Verify database persistence
        $this->assertDatabaseHas('ip_quote_tax_rates', [
            'quote_id'    => $quote->quote_id,
            'tax_rate_id' => $taxRate->tax_rate_id,
        ]);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_not_in_legacy_mode(): void
    {
        /* Arrange */
        $this->cleanupQuoteTables();
        $this->createClientFixture(['client_id' => 1]);
        $quote = $this->createQuoteFixture(['quote_id' => 100, 'client_id' => 1]);

        // Mock config_item to return non-legacy mode
        if ( ! function_exists('config_item')) {
            function config_item($key)
            {
                if ($key === 'legacy_calculation') {
                    return false;
                }
            }
        }

        $data = [
            'quote_id'               => $quote->quote_id,
            'tax_rate_id'            => 1,
            'include_item_tax'       => 0,
            'quote_tax_rate_percent' => 20.00,
        ];

        /* Act */
        $result = $this->model->saveTaxRate($data);

        /* Assert */
        $this->assertNull($result);
        // In non-legacy mode, tax rates are calculated differently
        // and quote-level tax rates may not be used
    }
}
