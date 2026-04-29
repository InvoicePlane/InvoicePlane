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

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;
        $item_id    = $this->seedModel('InvoiceItem', ['invoice_id' => $invoice_id, 'item_quantity' => 3, 'item_price' => 50])->item_id;

        /* Act */
        $global_discount = [];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $row = $this->databaseFetchOne('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->assertNotNull($row);
        $this->assertEquals(150.0, (float) $row['item_subtotal']); // 3 * 50
        $this->assertEquals(0.0, (float) $row['item_tax_total']);
        $this->assertEquals(0.0, (float) $row['item_discount']);
        $this->assertEquals(150.0, (float) $row['item_total']);

        /* Cleanup */
        $this->databaseDelete('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoice_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('exotic')]
    #[Test]
    public function it_calculates_item_amount_with_item_discount(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id])->invoice_id;
        $item_id    = $this->seedModel('InvoiceItem', ['invoice_id' => $invoice_id,
            'item_quantity'        => 2,
            'item_price'           => 100,
            'item_discount_amount' => 5, // discount per unit
        ])->item_id;

        /* Act */
        $global_discount = [];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $row = $this->databaseFetchOne('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->assertNotNull($row);
        $this->assertEquals(200.0, (float) $row['item_subtotal']); // 2 * 100

        /* Cleanup */
        $this->databaseDelete('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoice_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }

    #[Group('exotic')]
    #[Test]
    public function it_tracks_global_percent_discount_across_items(): void
    {
        $this->skipWithoutDatabase();

        /* Arrange */
        $client_id  = $this->seedModel('Client')->client_id;
        $invoice_id = $this->seedModel('Invoice', ['client_id' => $client_id, 'invoice_discount_percent' => 10])->invoice_id;
        $item_id    = $this->seedModel('InvoiceItem', ['invoice_id' => $invoice_id, 'item_quantity' => 1, 'item_price' => 1000])->item_id;

        /* Act */
        $global_discount = ['percent' => 10];
        $this->model->calculate($item_id, $global_discount);

        /* Assert */
        $row = $this->databaseFetchOne('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->assertNotNull($row);
        $this->assertEquals(1000.0, (float) $row['item_subtotal']);
        $this->assertArrayHasKey('item', $global_discount);
        $this->assertEquals(100.0, (float) $global_discount['item']); // 10% of 1000

        /* Cleanup */
        $this->databaseDelete('ip_invoice_item_amounts', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoice_items', ['item_id' => $item_id]);
        $this->databaseDelete('ip_invoices', ['invoice_id' => $invoice_id]);
        $this->databaseDelete('ip_clients', ['client_id' => $client_id]);
    }
}
