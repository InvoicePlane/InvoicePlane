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
        $this->CI->load->model('payments/mdl_payment_log');
        $this->model = $this->CI->mdl_payment_log;
    }

    #[Group('relationships')]
    #[Test]
    public function it_gets_all_payment_logs_with_relations_paginated(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_orders_payment_logs_by_date_descending(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_respects_custom_per_page_parameter(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Group('relationships')]
    #[Test]
    public function it_loads_custom_relations(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
