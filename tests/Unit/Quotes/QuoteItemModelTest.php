<?php

namespace Tests\Unit\Quotes;

use Mdl_Quote_Items;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Quote_Items::class)]
class QuoteItemModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('quotes/mdl_quote_items');
        $this->model = $this->CI->mdl_quote_items;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_quote_items', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('item_id', $this->model->primary_key);
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('quote_id', $rules);
        $this->assertArrayHasKey('item_name', $rules);
        $this->assertArrayHasKey('item_quantity', $rules);
        $this->assertArrayHasKey('item_price', $rules);
    }

    #[Test]
    public function it_has_save_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'save'));
    }

    #[Test]
    public function it_has_delete_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'delete'));
    }

    #[Test]
    public function it_has_get_items_subtotal_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'get_items_subtotal'));
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_quote_item_and_retrieves_it(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id = $this->seedModel('Client')->client_id;
        $quote_id  = $this->seedModel('Quote', ['client_id' => $client_id])->quote_id;
        $item_id   = $this->seedModel('QuoteItem', ['quote_id' => $quote_id,
            'item_name'     => 'Test Quote Item',
            'item_quantity' => 2,
            'item_price'    => 75.00,
        ])->item_id;

        /* Act */
        $row = $this->databaseFetchOne('ip_quote_items', ['item_id' => $item_id]);

        /* Assert */
        $this->assertNotNull($row);
        $this->assertEquals('Test Quote Item', $row['item_name']);
        $this->assertEquals(2, (int) $row['item_quantity']);
        $this->assertEquals(75.00, (float) $row['item_price']);

        /* Cleanup */
        $this->databaseDelete('ip_quote_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_quotes', ['quote_id' => $quote_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_quote_item(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id = $this->seedModel('Client')->client_id;
        $quote_id  = $this->seedModel('Quote', ['client_id' => $client_id])->quote_id;
        $item_id   = $this->seedModel('QuoteItem', ['quote_id' => $quote_id])->item_id;

        /* Act */
        $this->model->delete($item_id);

        /* Assert */
        $this->assertNull($this->databaseFetchOne('ip_quote_items', ['item_id' => $item_id]));

        /* Cleanup */
        $this->databaseDelete('ip_quotes', ['quote_id' => $quote_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Test]
    public function it_returns_zero_subtotal_for_quote_with_no_items(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id = $this->seedModel('Client')->client_id;
        $quote_id  = $this->seedModel('Quote', ['client_id' => $client_id])->quote_id;

        /* Act */
        $subtotal = $this->model->get_items_subtotal($quote_id);

        /* Assert */
        $this->assertEquals(0.0, (float) $subtotal);

        /* Cleanup */
        $this->databaseDelete('ip_quotes', ['quote_id' => $quote_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
