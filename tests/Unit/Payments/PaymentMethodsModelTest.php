<?php

namespace Tests\Unit\Payments;

use Mdl_Payment_Methods;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Payment_Methods::class)]
class PaymentMethodsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('payment_methods/mdl_payment_methods');
        $this->model = $this->CI->mdl_payment_methods;
    }

    #[Test]
    public function it_retrieves_all_payment_methods(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_orders_by_name_by_default(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
