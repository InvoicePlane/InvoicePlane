<?php

namespace Tests;

use Mdl_Quote_Item_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Mdl_Quote_Item_Amounts::class)]
class QuoteItemAmountServiceTest extends AbstractTestCase
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
