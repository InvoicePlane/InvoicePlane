<?php

namespace Tests\Unit\Payments;

use Mdl_Payments;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Payments::class)]
class PaymentsModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('payments/mdl_payments');
        $this->model = $this->CI->mdl_payments;
    }

    #[Test]
    public function it_retrieves_payments_by_invoice_id(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_returns_db_array_with_correct_structure(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_returns_validation_rules(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_orders_payments_by_date_descending(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_gets_payments_by_client_id(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
