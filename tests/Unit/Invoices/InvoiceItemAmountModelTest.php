<?php

namespace Tests\Unit\Invoices;

use Mdl_Item_Amounts;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Item_Amounts::class)]
class InvoiceItemAmountModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_item_amounts');
        $this->model = $this->CI->mdl_item_amounts;
    }

    #[Test]
    public function it_has_calculate_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'calculate'));
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_with_no_tax(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange: create client, invoice, and item */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'Test Client ' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoices', [
            'client_id'                => $client_id,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-IAMT-' . uniqid(),
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $invoice_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoice_items', [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Test Item',
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
            ->get_where('ip_invoice_item_amounts', ['item_id' => $item_id])
            ->row();

        $this->assertNotNull($item_amount);
        $this->assertEquals(150.0, (float) $item_amount->item_subtotal); // 3 * 50
        $this->assertEquals(0.0, (float) $item_amount->item_tax_total);
        $this->assertEquals(0.0, (float) $item_amount->item_discount);
        $this->assertEquals(150.0, (float) $item_amount->item_total);

        /* Cleanup */
        $this->CI->db->delete('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_invoice_items', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_with_item_discount(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'Test Client ' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoices', [
            'client_id'                => $client_id,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-IAMTD-' . uniqid(),
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $invoice_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoice_items', [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Discounted Item',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 5, // discount per unit
        ]);
        $item_id = $this->CI->db->insert_id();

        /* Act */
        $global_discount = [];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $item_amount = $this->CI->db
            ->get_where('ip_invoice_item_amounts', ['item_id' => $item_id])
            ->row();

        $this->assertNotNull($item_amount);
        $this->assertEquals(200.0, (float) $item_amount->item_subtotal); // 2 * 100

        /* Cleanup */
        $this->CI->db->delete('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_invoice_items', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('exotic')]
    #[Test]
    public function it_tracks_global_percent_discount_across_items(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $this->CI->db->insert('ip_clients', [
            'client_name'          => 'Test Client ' . uniqid(),
            'client_active'        => 1,
            'client_date_created'  => date('Y-m-d H:i:s'),
            'client_date_modified' => date('Y-m-d H:i:s'),
        ]);
        $client_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoices', [
            'client_id'                => $client_id,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-GD-' . uniqid(),
            'invoice_date_created'     => date('Y-m-d'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+30 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 10, // 10% global discount
            'invoice_terms'            => '',
            'invoice_url_key'          => bin2hex(random_bytes(16)),
        ]);
        $invoice_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoice_items', [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'GD Item',
            'item_quantity'        => 1,
            'item_price'           => 1000,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $item_id = $this->CI->db->insert_id();

        /* Act: pass a global percent discount */
        $global_discount = [
            'percent' => 10,
        ];
        $this->model->calculate($item_id, $global_discount);

        /* Assert: item subtotal is correct and global_discount['item'] is populated */
        $item_amount = $this->CI->db
            ->get_where('ip_invoice_item_amounts', ['item_id' => $item_id])
            ->row();

        $this->assertNotNull($item_amount);
        $this->assertEquals(1000.0, (float) $item_amount->item_subtotal);
        // Global discount should be tracked per-item
        $this->assertArrayHasKey('item', $global_discount);
        $this->assertEquals(100.0, (float) $global_discount['item']); // 10% of 1000

        /* Cleanup */
        $this->CI->db->delete('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_invoice_items', ['item_id' => $item_id]);
        $this->CI->db->delete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->CI->db->delete('ip_clients', ['client_id' => $client_id]);
    }
}
