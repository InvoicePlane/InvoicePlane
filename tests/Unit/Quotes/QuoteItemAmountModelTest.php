<?php

namespace Tests\Unit\Quotes;

use Mdl_Quote_Item_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quote_Item_Amounts::class)]
class QuoteItemAmountModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quote_item_amounts');
        $this->model = $this->CI->mdl_quote_item_amounts;
    }

    #[Test]
    public function it_has_calculate_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'calculate'));
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amounts_with_no_tax(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id = $this->seedModel('Client')->client_id;
        $quote_id  = $this->seedModel('Quote', ['client_id' => $client_id])->quote_id;
        $item_id   = $this->seedModel('QuoteItem', ['quote_id' => $quote_id, 'item_quantity' => 3, 'item_price' => 50])->item_id;

        /* Act */
        $global_discount = [];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $row = $this->databaseFetchOne('ip_quote_item_amounts', ['item_id' => $item_id]);
        $this->assertNotNull($row);
        $this->assertEquals(150.0, (float) $row['item_subtotal']); // 3 * 50
        $this->assertEquals(0.0, (float) $row['item_tax_total']);
        $this->assertEquals(150.0, (float) $row['item_total']);

        /* Cleanup */
        $this->databaseDelete('ip_quote_item_amounts', ['item_id' => $item_id]);
        $this->databaseDelete('ip_quote_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_quotes', ['quote_id' => $quote_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('exotic')]
    #[Test]
    public function it_applies_global_discount_proportionally(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id = $this->seedModel('Client')->client_id;
        $quote_id  = $this->seedModel('Quote', ['client_id' => $client_id, 'quote_discount_amount' => 50])->quote_id;
        $item_id   = $this->seedModel('QuoteItem', ['quote_id' => $quote_id, 'item_quantity' => 1, 'item_price' => 100])->item_id;

        /* Act */
        $global_discount = ['amount' => 50, 'items_subtotal' => 100];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $row = $this->databaseFetchOne('ip_quote_item_amounts', ['item_id' => $item_id]);
        $this->assertNotNull($row);
        $this->assertEquals(100.0, (float) $row['item_subtotal']);
        $this->assertArrayHasKey('item', $global_discount);

        /* Cleanup */
        $this->databaseDelete('ip_quote_item_amounts', ['item_id' => $item_id]);
        $this->databaseDelete('ip_quote_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_quotes', ['quote_id' => $quote_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
