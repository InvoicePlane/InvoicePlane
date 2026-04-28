<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoice_Groups;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoice_Groups::class)]
class InvoiceGroupModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoice_groups/mdl_invoice_groups');
        $this->model = $this->CI->mdl_invoice_groups;
    }

    #[Group('crud')]
    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete();
        $rules = $this->model->validation_rules();

        $this->assertIsArray($rules);
        $this->assertArrayHasKey('invoice_group_name', $rules);
        $this->assertArrayHasKey('invoice_group_identifier_format', $rules);
        $this->assertArrayHasKey('invoice_group_next_id', $rules);
        $this->assertArrayHasKey('invoice_group_left_pad', $rules);
    }

    #[Test]
    public function it_generates_invoice_number_with_year_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV-{{{year}}}-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 4,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $expectedYear = date('Y');
        $this->assertEquals("INV-{$expectedYear}-0001", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_month_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{month}}}/{{{id}}}',
            'invoice_group_next_id'           => 5,
            'invoice_group_left_pad'          => 3,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $expectedMonth = date('m');
        $this->assertEquals("{$expectedMonth}/005", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_day_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{day}}}-{{{id}}}',
            'invoice_group_next_id'           => 10,
            'invoice_group_left_pad'          => 2,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $expectedDay = date('d');
        $this->assertEquals("{$expectedDay}-10", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_short_year_template(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{yy}}}{{{id}}}',
            'invoice_group_next_id'           => 100,
            'invoice_group_left_pad'          => 5,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $expectedYY = date('y');
        $this->assertEquals("{$expectedYY}00100", $number);
    }

    #[Test]
    public function it_generates_invoice_number_with_multiple_templates(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{year}}}/{{{month}}}/{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 6,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $expectedYear  = date('Y');
        $expectedMonth = date('m');
        $this->assertEquals("{$expectedYear}/{$expectedMonth}/000001", $number);
    }

    #[Test]
    public function it_generates_invoice_number_without_templates(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'STATIC-PREFIX',
            'invoice_group_next_id'           => 999,
            'invoice_group_left_pad'          => 0,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $this->assertEquals('STATIC-PREFIX', $number);
    }

    #[Test]
    public function it_increments_next_id_when_set_next_is_true(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 50,
            'invoice_group_left_pad'          => 3,
        ]);

        $this->model->generateInvoiceNumber($group, true);

        $group->refresh();
        $this->assertEquals(51, $group->invoice_group_next_id);
    }

    #[Test]
    public function it_does_not_increment_next_id_when_set_next_is_false(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV-{{{id}}}',
            'invoice_group_next_id'           => 50,
            'invoice_group_left_pad'          => 3,
        ]);

        $this->model->generateInvoiceNumber($group, false);

        $group->refresh();
        $this->assertEquals(50, $group->invoice_group_next_id);
    }

    #[Test]
    public function it_pads_invoice_id_with_zeros(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{id}}}',
            'invoice_group_next_id'           => 7,
            'invoice_group_left_pad'          => 10,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $this->assertEquals('0000000007', $number);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_zero_left_pad(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => 'INV{{{id}}}',
            'invoice_group_next_id'           => 123,
            'invoice_group_left_pad'          => 0,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $this->assertEquals('INV123', $number);
    }

    #[Group('exotic')]
    #[Test]
    public function it_handles_unknown_template_variables(): void
    {
        $this->markTestIncomplete();
        $group = InvoiceGroup::query()->create([
            'invoice_group_name'              => 'Test Group',
            'invoice_group_identifier_format' => '{{{unknown}}}-{{{id}}}',
            'invoice_group_next_id'           => 1,
            'invoice_group_left_pad'          => 2,
        ]);

        $number = $this->model->generateInvoiceNumber($group, false);

        $this->assertEquals('-01', $number); // Unknown variable replaced with empty string
    }
}
