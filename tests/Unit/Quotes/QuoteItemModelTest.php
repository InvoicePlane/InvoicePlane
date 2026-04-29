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
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'QItemClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quotes', [
            'client_id'          => $client_id,
            'user_id'            => 1,
            'invoice_group_id'   => 1,
            'quote_status_id'    => 1,
            'quote_number'       => 'QUO-ITEM-' . uniqid(),
            'quote_date_created' => date('Y-m-d'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_password'     => '',
            'quote_url_key'      => bin2hex(random_bytes(16)),
        ]);
        $quote_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quote_items', [
            'quote_id'             => $quote_id,
            'item_name'            => 'Test Quote Item',
            'item_quantity'        => 2,
            'item_price'           => 75.00,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $item_id = $this->CI->db->insert_id();

        /* Act */
        $item = $this->CI->db->get_where('ip_quote_items', ['item_id' => $item_id])->row();

        /* Assert */
        $this->assertNotNull($item);
        $this->assertEquals('Test Quote Item', $item->item_name);
        $this->assertEquals(2, (int) $item->item_quantity);
        $this->assertEquals(75.00, (float) $item->item_price);

        /* Cleanup */
        $this->CI->db->delete('ip_quote_items', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_quotes', ['quote_id' => $quote_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_quote_item(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'QDelClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quotes', [
            'client_id'          => $client_id,
            'user_id'            => 1,
            'invoice_group_id'   => 1,
            'quote_status_id'    => 1,
            'quote_number'       => 'QUO-DEL-' . uniqid(),
            'quote_date_created' => date('Y-m-d'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_password'     => '',
            'quote_url_key'      => bin2hex(random_bytes(16)),
        ]);
        $quote_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quote_items', [
            'quote_id'             => $quote_id,
            'item_name'            => 'Delete Me',
            'item_quantity'        => 1,
            'item_price'           => 50.00,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $item_id = $this->CI->db->insert_id();

        /* Act */
        $this->model->delete($item_id);

        /* Assert */
        $item = $this->CI->db->get_where('ip_quote_items', ['item_id' => $item_id])->row();
        $this->assertNull($item);

        /* Cleanup */
        $this->CI->db->delete('ip_quotes', ['quote_id' => $quote_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Test]
    public function it_returns_zero_subtotal_for_quote_with_no_items(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'QSubClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quotes', [
            'client_id'          => $client_id,
            'user_id'            => 1,
            'invoice_group_id'   => 1,
            'quote_status_id'    => 1,
            'quote_number'       => 'QUO-EMPTY-' . uniqid(),
            'quote_date_created' => date('Y-m-d'),
            'quote_date_expires' => date('Y-m-d', strtotime('+30 days')),
            'quote_password'     => '',
            'quote_url_key'      => bin2hex(random_bytes(16)),
        ]);
        $quote_id = $this->CI->db->insert_id();

        /* Act */
        $subtotal = $this->model->get_items_subtotal($quote_id);

        /* Assert */
        $this->assertEquals(0.0, (float) $subtotal);

        /* Cleanup */
        $this->CI->db->delete('ip_quotes', ['quote_id' => $quote_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }
}
