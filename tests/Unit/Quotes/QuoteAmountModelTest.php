<?php

namespace Tests\Unit\Quotes;

use Mdl_Quote_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quote_Amounts::class)]
class QuoteAmountModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quote_amounts');
        $this->model = $this->CI->mdl_quote_amounts;
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_global_discount(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_discount_for_legacy_mode(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_gets_total_quoted_for_all_time(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_gets_status_totals_for_period(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
