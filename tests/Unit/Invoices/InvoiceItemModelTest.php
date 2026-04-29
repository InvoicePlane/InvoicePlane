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
        $rules = $this->model->validation_rules();

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
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;

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
        $item_id         = $this->model->save(null, $data, $global_discount);

        /* Assert */
        $this->assertNotNull($item_id);
        $row = $this->databaseFetchOne('ip_invoice_items', ['item_id' => $item_id]);
        $this->assertEquals('Test Item', $row['item_name']);
        $this->assertEquals(2, (int) $row['item_quantity']);
        $this->assertEquals(100, (float) $row['item_price']);

        /* Cleanup */
        $this->databaseDelete('ip_invoice_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_item(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;
        $item_id    = $this->seedModel('InvoiceItem', ['invoice_id' => $invoice_id,
            'item_name'        => 'Original Name',
            'item_quantity'    => 1,
            'item_price'       => 50,
        ])->item_id;

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
        $returned_id     = $this->model->save($item_id, $data, $global_discount);

        /* Assert */
        $this->assertEquals($item_id, $returned_id);
        $row = $this->databaseFetchOne('ip_invoice_items', ['item_id' => $item_id]);
        $this->assertEquals('Updated Name', $row['item_name']);
        $this->assertEquals(3, (int) $row['item_quantity']);
        $this->assertEquals(150, (float) $row['item_price']);
        $this->assertEquals(10, (float) $row['item_discount_amount']);

        /* Cleanup */
        $this->databaseDelete('ip_invoice_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_item_and_recalculates_invoice(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;
        $item_id    = $this->seedModel('InvoiceItem', ['invoice_id' => $invoice_id])->item_id;

        /* Act */
        $result = $this->model->delete($item_id);

        /* Assert */
        $this->assertNull($result);
        $this->assertNull($this->databaseFetchOne('ip_invoice_items', ['item_id' => $item_id]));

        /* Cleanup */
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Test]
    public function it_gets_items_subtotal(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;
        $item1_id   = $this->seedModel('InvoiceItem', ['invoice_id' => $invoice_id, 'item_quantity' => 2, 'item_price' => 100])->item_id;
        $item2_id   = $this->seedModel('InvoiceItem', ['invoice_id' => $invoice_id, 'item_quantity' => 1, 'item_price' => 150])->item_id;

        $this->databaseInsert('ip_invoice_item_amounts', [
            'item_id'        => $item1_id,
            'item_subtotal'  => 200,
            'item_tax_total' => 20,
            'item_discount'  => 0,
            'item_total'     => 220,
        ]);
        $this->databaseInsert('ip_invoice_item_amounts', [
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
        $this->assertEquals(350.0, (float) $subtotal);

        /* Cleanup */
        $this->databaseDelete('ip_invoice_item_amounts', ['item_id' => $item1_id]);
        $this->databaseDelete('ip_invoice_item_amounts', ['item_id' => $item2_id]);
        $this->databaseDelete('ip_invoice_items', ['item_id' => $item1_id]);
        $this->databaseDelete('ip_invoice_items', ['item_id' => $item2_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_subtotal_for_invoice_without_items(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;

        /* Act */
        $this->CI->load->model('invoices/mdl_invoice_amounts');
        $subtotal = $this->CI->mdl_invoice_amounts->get_invoice_item_subtotal($invoice_id);

        /* Assert */
        $this->assertEquals(0.0, (float) $subtotal);

        /* Cleanup */
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
