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
        $this->markTestIncomplete('This test uses Laravel patterns (Model::create, assertInstanceOf) which need to be refactored to use CodeIgniter patterns');
    }

    #[Group('crud')]
    #[Test]
    public function it_updates_existing_item(): void
    {
        $this->markTestIncomplete('This test uses Laravel patterns (Model::create) which need to be refactored to use CodeIgniter patterns');
    }

    #[Group('crud')]
    #[Test]
    public function it_deletes_item_and_recalculates_invoice(): void
    {
        $this->markTestIncomplete('This test uses Laravel patterns (Model::create) which need to be refactored to use CodeIgniter patterns');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_false_when_deleting_non_existent_item(): void
    {
        /* Arrange */
        /* (no setup needed) */

        /* Act */
        $result = $this->model->delete(99999);

        /* Assert */
        $this->assertFalse($result);
    }

    #[Test]
    public function it_gets_items_subtotal(): void
    {
        $this->markTestIncomplete('This test uses Laravel patterns (Model::create) which need to be refactored to use CodeIgniter patterns');
    }

    #[Group('smoke')]
    #[Test]
    public function it_returns_zero_subtotal_for_invoice_without_items(): void
    {
        $this->markTestIncomplete('This test uses Laravel patterns (Model::create) which need to be refactored to use CodeIgniter patterns');
    }
}
