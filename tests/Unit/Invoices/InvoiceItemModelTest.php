<?php

namespace Tests\Unit\Invoices;

use Mdl_Items;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Items::class)]
class InvoiceItemModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->CI->load->model('invoices/mdl_items');
        $this->model = $this->CI->mdl_items;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Arrange */
        /* (no additional setup needed beyond setUp()) */

        /* Act */
        $rules = $this->model->validation_rules();

        /* Assert */
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_id', $rules);
        $this->assertArrayHasKey('item_name', $rules);
        $this->assertArrayHasKey('item_description', $rules);
        $this->assertArrayHasKey('item_quantity', $rules);
        $this->assertArrayHasKey('item_price', $rules);
        $this->assertArrayHasKey('item_tax_rate_id', $rules);
        $this->assertArrayHasKey('item_product_id', $rules);
    }

    #[Group('crud')]
    #[Test]
    public function it_creates_new_item(): void
    {
        /* Arrange */
        // Create a basic invoice first using CI db
        $this->CI->db->insert('ip_invoices', [
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-001',
            'invoice_date_created'     => date('Y-m-d H:i:s'),
            'invoice_date_modified'    => date('Y-m-d H:i:s'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+15 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-001',
        ]);
        $invoice_id = $this->CI->db->insert_id();

        $data = [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Test Item',
            'item_description'     => 'Test Description',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ];

        /* Act */
        $global_discount = [];
        $item_id = $this->model->save(null, $data, $global_discount);

        /* Assert */
        $this->assertNotNull($item_id);
        $item = $this->CI->db->get_where('ip_invoice_items', ['item_id' => $item_id])->row();
        $this->assertEquals('Test Item', $item->item_name);
        $this->assertEquals(2, $item->item_quantity);
        $this->assertEquals(100, $item->item_price);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_item(): void
    {
        /* Arrange */
        // Create a basic invoice
        $this->CI->db->insert('ip_invoices', [
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-002',
            'invoice_date_created'     => date('Y-m-d H:i:s'),
            'invoice_date_modified'    => date('Y-m-d H:i:s'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+15 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-002',
        ]);
        $invoice_id = $this->CI->db->insert_id();

        // Create existing item
        $this->CI->db->insert('ip_invoice_items', [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Original Name',
            'item_description'     => 'Original Description',
            'item_quantity'        => 1,
            'item_price'           => 50,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $existing_item_id = $this->CI->db->insert_id();

        $data = [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Updated Name',
            'item_description'     => 'Updated Description',
            'item_quantity'        => 3,
            'item_price'           => 150,
            'item_order'           => 1,
            'item_discount_amount' => 10,
        ];

        /* Act */
        $global_discount = [];
        $item_id = $this->model->save($existing_item_id, $data, $global_discount);

        /* Assert */
        $this->assertEquals($existing_item_id, $item_id);
        $item = $this->CI->db->get_where('ip_invoice_items', ['item_id' => $item_id])->row();
        $this->assertEquals('Updated Name', $item->item_name);
        $this->assertEquals(3, $item->item_quantity);
        $this->assertEquals(150, $item->item_price);
        $this->assertEquals(10, $item->item_discount_amount);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_item_and_recalculates_invoice(): void
    {
        /* Arrange */
        // Create a basic invoice
        $this->CI->db->insert('ip_invoices', [
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-003',
            'invoice_date_created'     => date('Y-m-d H:i:s'),
            'invoice_date_modified'    => date('Y-m-d H:i:s'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+15 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-003',
        ]);
        $invoice_id = $this->CI->db->insert_id();

        // Create item
        $this->CI->db->insert('ip_invoice_items', [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Item to Delete',
            'item_description'     => 'Description',
            'item_quantity'        => 1,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $item_id = $this->CI->db->insert_id();

        /* Act */
        $result = $this->model->delete($item_id);

        /* Assert */
        $this->assertNull($result);
        $item = $this->CI->db->get_where('ip_invoice_items', ['item_id' => $item_id])->row();
        $this->assertNull($item);
    }

    #[Test]
    public function it_gets_items_subtotal(): void
    {
        /* Arrange */
        // Create a basic invoice
        $this->CI->db->insert('ip_invoices', [
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-004',
            'invoice_date_created'     => date('Y-m-d H:i:s'),
            'invoice_date_modified'    => date('Y-m-d H:i:s'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+15 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-004',
        ]);
        $invoice_id = $this->CI->db->insert_id();

        // Create two items
        $this->CI->db->insert('ip_invoice_items', [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Item 1',
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_order'           => 1,
            'item_discount_amount' => 0,
        ]);
        $item1_id = $this->CI->db->insert_id();

        $this->CI->db->insert('ip_invoice_items', [
            'invoice_id'           => $invoice_id,
            'item_name'            => 'Item 2',
            'item_quantity'        => 1,
            'item_price'           => 150,
            'item_order'           => 2,
            'item_discount_amount' => 0,
        ]);
        $item2_id = $this->CI->db->insert_id();

        // Create item amounts
        $this->CI->db->insert('ip_invoice_item_amounts', [
            'item_id'        => $item1_id,
            'item_subtotal'  => 200,
            'item_tax_total' => 20,
            'item_discount'  => 0,
            'item_total'     => 220,
        ]);

        $this->CI->db->insert('ip_invoice_item_amounts', [
            'item_id'        => $item2_id,
            'item_subtotal'  => 150,
            'item_tax_total' => 15,
            'item_discount'  => 0,
            'item_total'     => 165,
        ]);

        /* Act */
        $this->CI->load->model('invoices/mdl_invoice_amounts');
        $subtotal = $this->CI->mdl_invoice_amounts->get_invoice_item_subtotal($invoice_id);

        /* Assert */
        $this->assertEquals(350.0, (float)$subtotal);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_subtotal_for_invoice_without_items(): void
    {
        /* Arrange */
        // Create a basic invoice with no items
        $this->CI->db->insert('ip_invoices', [
            'client_id'                => 1,
            'user_id'                  => 1,
            'invoice_group_id'         => 1,
            'invoice_status_id'        => 1,
            'invoice_number'           => 'INV-ITEM-005',
            'invoice_date_created'     => date('Y-m-d H:i:s'),
            'invoice_date_modified'    => date('Y-m-d H:i:s'),
            'invoice_date_due'         => date('Y-m-d', strtotime('+15 days')),
            'invoice_password'         => '',
            'invoice_discount_amount'  => 0,
            'invoice_discount_percent' => 0,
            'invoice_terms'            => '',
            'invoice_url_key'          => 'key-item-005',
        ]);
        $invoice_id = $this->CI->db->insert_id();

        /* Act */
        $this->CI->load->model('invoices/mdl_invoice_amounts');
        $subtotal = $this->CI->mdl_invoice_amounts->get_invoice_item_subtotal($invoice_id);

        /* Assert */
        $this->assertEquals(0.0, (float)$subtotal);
    }
}
