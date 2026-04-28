<?php

namespace Tests\Unit\Quotes;

use Mdl_Quote_Item_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quote_Item_Amounts::class)]
class QuoteItemModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quote_item_amounts');
        $this->model = $this->CI->mdl_quote_item_amounts;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_saves_item(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_item(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_gets_items_subtotal(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
