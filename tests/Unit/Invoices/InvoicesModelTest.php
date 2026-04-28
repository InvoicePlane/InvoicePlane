<?php

namespace Tests\Unit\Invoices;

use Mdl_Invoices;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\CiTestCase;

#[CoversClass(Mdl_Invoices::class)]
class InvoicesModelTest extends CiTestCase
{
    private $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->CI->load->model('invoices/mdl_invoices');
        $this->model = $this->CI->mdl_invoices;
    }

    #[Test]
    public function it_attaches_payments_to_invoice(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_returns_null_payments_when_invoice_has_no_payments(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_marks_invoice_as_viewed_when_status_is_sent(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_does_not_change_status_when_invoice_is_not_sent(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }

    #[Test]
    public function it_filters_invoices_by_client(): void
    {
        $this->markTestIncomplete('Requires CI3 database integration setup');
    }
}
