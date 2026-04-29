<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class InvoiceTaxRateModelTest extends AbstractTestCase
{
    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceTaxRateService');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_null_when_not_in_legacy_mode(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceTaxRateService');
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_tax_rate_in_legacy_mode(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceTaxRateService');
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_tax_rate_in_legacy_mode(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceTaxRateService');
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_include_item_tax_flag(): void
    {
        $this->markTestIncomplete('Requires CI3 migration of InvoiceTaxRateService');
    }
}
