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

    #[Test]
    public function it_has_calculate_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'calculate'));
    }

    #[Test]
    public function it_has_get_global_discount_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_global_discount'));
    }

    #[Test]
    public function it_has_get_total_quoted_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_total_quoted'));
    }

    #[Test]
    public function it_has_get_status_totals_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_status_totals'));
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_status_totals_as_array(): void
    {
        $this->skipWithoutDatabase();

        $this->assertIsArray($this->model->get_status_totals(''));
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_total_quoted_as_numeric_or_null(): void
    {
        $this->skipWithoutDatabase();

        $total = $this->model->get_total_quoted();

        /* NULL when quote_amounts table is empty, numeric otherwise */
        $this->assertTrue($total === null || is_numeric($total));
    }

    #[Group('exotic')]
    #[Test]
    public function it_returns_null_global_discount_for_quote_with_no_items(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id = $this->seedModel('Client')->client_id;
        $quote_id  = $this->seedModel('Quote', ['client_id' => $client_id])->quote_id;

        /* Act */
        $discount = $this->model->get_global_discount($quote_id);

        /* Assert: no items → SUM returns NULL */
        $this->assertNull($discount);

        /* Cleanup */
        $this->databaseDelete('ip_quotes', ['quote_id' => $quote_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
