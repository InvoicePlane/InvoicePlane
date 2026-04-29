<?php

namespace Tests\Unit\Payments;

use Mdl_Payment_Logs;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Payment_Logs::class)]
class PaymentLogModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('payments/mdl_payment_logs');
        $this->model = $this->CI->mdl_payment_logs;
    }

    #[Test]
    public function it_has_correct_table_name(): void
    {
        $this->assertEquals('ip_merchant_responses', $this->model->table);
    }

    #[Test]
    public function it_has_correct_primary_key(): void
    {
        $this->assertStringContainsString('merchant_response_id', $this->model->primary_key);
    }

    #[Test]
    public function it_has_default_select_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_select'));
    }

    #[Test]
    public function it_has_default_order_by_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_order_by'));
    }

    #[Test]
    public function it_has_default_join_method(): void
    {
        $this->assertTrue(method_exists($this->model, 'default_join'));
    }

    #[Group('relationships')]
    #[Test]
    public function it_extends_response_model(): void
    {
        $this->assertInstanceOf('Response_Model', $this->model);
    }
}
