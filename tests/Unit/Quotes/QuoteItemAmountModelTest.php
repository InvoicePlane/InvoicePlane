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
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'QIAClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quotes', [
            'client_id'              => $client_id,
            'user_id'                => 1,
            'invoice_group_id'       => 1,
            'quote_status_id'        => 1,
            'quote_number'           => 'QUO-QIA-' . uniqid(),
            'quote_date_created'     => date('Y-m-d'),
            'quote_date_expires'     => date('Y-m-d', strtotime('+30 days')),
            'quote_discount_amount'  => 0,
            'quote_discount_percent' => 0,
            'quote_password'         => '',
            'quote_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $quote_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quote_items', [
            'quote_id'             => $quote_id,
            'item_name'            => 'QIA Item',
            'item_quantity'        => 3,
            'item_price'           => 50,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $item_id = $this->CI->db->insert_id();

        /* Act */
        $global_discount = [];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $item_amount = $this->CI->db
            ->get_where('ip_quote_item_amounts', ['item_id' => $item_id])
            ->row();

        $this->assertNotNull($item_amount);
        $this->assertEquals(150.0, (float) $item_amount->item_subtotal); // 3 * 50
        $this->assertEquals(0.0, (float) $item_amount->item_tax_total);
        $this->assertEquals(150.0, (float) $item_amount->item_total);

        /* Cleanup */
        $this->CI->db->delete('ip_quote_item_amounts', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_quote_items', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_quotes', ['quote_id' => $quote_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('exotic')]
    #[Test]
    public function it_applies_global_discount_proportionally(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'QIAGDClient_' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quotes', [
            'client_id'              => $client_id,
            'user_id'                => 1,
            'invoice_group_id'       => 1,
            'quote_status_id'        => 1,
            'quote_number'           => 'QUO-GD2-' . uniqid(),
            'quote_date_created'     => date('Y-m-d'),
            'quote_date_expires'     => date('Y-m-d', strtotime('+30 days')),
            'quote_discount_amount'  => 50,
            'quote_discount_percent' => 0,
            'quote_password'         => '',
            'quote_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $quote_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_quote_items', [
            'quote_id'             => $quote_id,
            'item_name'            => 'GD Item',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $item_id = $this->CI->db->insert_id();

        /* Act */
        $global_discount = [
            'amount'         => 50,
            'items_subtotal' => 100,
        ];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $item_amount = $this->CI->db
            ->get_where('ip_quote_item_amounts', ['item_id' => $item_id])
            ->row();

        $this->assertNotNull($item_amount);
        $this->assertEquals(100.0, (float) $item_amount->item_subtotal);
        // Global discount tracked
        $this->assertArrayHasKey('item', $global_discount);

        /* Cleanup */
        $this->CI->db->delete('ip_quote_item_amounts', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_quote_items', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_quotes', ['quote_id' => $quote_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }
}
