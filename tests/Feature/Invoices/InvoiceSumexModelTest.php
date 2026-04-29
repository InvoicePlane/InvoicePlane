<?php

namespace Tests\Feature\Invoices;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\AbstractTestCase;

class InvoiceSumexModelTest extends AbstractTestCase
{
    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete('InvoiceSumexService does not exist in CI3');
    }

    #[Test]
    public function it_validates_sumex_invoice_as_required_integer(): void
    {
        $this->markTestIncomplete('InvoiceSumexService does not exist in CI3');
    }

    #[Test]
    public function it_validates_optional_fields_as_nullable(): void
    {
        $this->markTestIncomplete('InvoiceSumexService does not exist in CI3');
    }

    #[Test]
    public function it_validates_date_fields(): void
    {
        $this->markTestIncomplete('InvoiceSumexService does not exist in CI3');
    }

    #[Test]
    public function it_validates_string_fields(): void
    {
        $this->markTestIncomplete('InvoiceSumexService does not exist in CI3');
    }
}
