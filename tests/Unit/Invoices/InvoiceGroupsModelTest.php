<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoice_Groups;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoice_Groups::class)]
class InvoiceGroupsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        get_instance()->load->model('invoice_groups/mdl_invoice_groups');
        $this->model = get_instance()->mdl_invoice_groups;
    }

    #[Test]
    #[Group('crud')]
    public function it_retrieves_all_invoice_groups(): void
    {
        /* Arrange */
        InvoiceGroup::create([
            'invoice_group_name' => 'Default',
        ]);
        InvoiceGroup::create([
            'invoice_group_name' => 'Custom Group',
        ]);

        /* Act */
        $result = $this->model->defaultSelect()->get();

        /* Assert */
        $this->assertCount(2, $result);
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        /* Act */
        $rules = $this->model->validationRules();

        /* Assert */
        $this->assertIsArray($rules);
    }

    #[Test]
    public function it_orders_by_next_id_by_default(): void
    {
        /* Arrange */
        InvoiceGroup::create([
            'invoice_group_name'    => 'Group A',
            'invoice_group_next_id' => 100,
        ]);
        InvoiceGroup::create([
            'invoice_group_name'    => 'Group B',
            'invoice_group_next_id' => 50,
        ]);

        /* Act */
        $result = $this->model->defaultOrderBy()->get();

        /* Assert */
        $this->assertCount(2, $result);
    }
}
